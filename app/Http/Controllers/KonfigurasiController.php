<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Dokter;
use App\Models\Kabupaten;
use App\Models\Kecamatan;
use App\Models\Kelurahan;
use App\Models\Kunjungan;
use App\Models\Location;
use App\Models\Organization;
use App\Models\Pasien;
use App\Models\Provinsi;
use App\Models\Unit;
use App\Models\User;
use App\Services\SatuSehatService;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class KonfigurasiController extends Controller
{
    protected $ssService;

    public function __construct(SatuSehatService $ssService)
    {
        $this->ssService = $ssService;
    }
    public function indexmasteruser()
    {
        $menu = 'indexmasteruser';
        $user = User::where('client_id', auth()->user()->client_id)->get();
        return view('Konfigurasi.indexmasteruser', compact([
            'menu',
            'user'
        ]));
    }
    public function indexorganization()
    {
        $menu = 'indexorganization';
        $organizations = Organization::where('client_id',auth()->user()->client_id)->get();
        return view('Konfigurasi.indexorganization', compact([
            'menu',
            'organizations'
        ]));
    }
    public function indexmasterpractitioner()
    {
        $menu = 'indexmasterpractitioner';
        $dokter = Dokter::where('client_id',auth()->user()->client_id)->get();
        return view('Konfigurasi.indexmasterpractitioner', compact([
            'menu',
            'dokter'
        ]));
    }
    public function simpanorganization(Request $request)
    {
        // 1. Validasi Input
        // $satusehatOrgId = env('SATUSEHAT_ORGANIZATION_ID');
        $id = auth()->user()->client_id;
        $client = db::table('mt_client')->where('id', $id)->get();
        $satusehatOrgId =  $client[0]->id_org_satu_sehat;
        $request->validate([
            'satusehat_org_id' => 'nullable|string|max:255', // Kode Org unit ini (Opsional)
            'part_of_id'       => 'required|string|max:255', // Org ID Induk / RS / Faskes Utama
            'name'             => 'required|string|max:255',
            'phone'            => 'nullable|string',
            'email'            => 'nullable|email',
            'address'          => 'nullable|string',
            'postal_code'      => 'nullable|string',
            'active'           => 'required|in:0,1',
        ]);
        // dd($request->tipe);
        // 2. Simpan Data ke Database Lokal Dulu
        $organization = Organization::create([
            'satusehat_org_id' => $request->satusehat_org_id ?? null,
            'parent_org_id'    => $satusehatOrgId,
            'nama_organisasi'  => $request->name,
            'telepon'          => $request->phone,
            'email'            => $request->email,
            'alamat'           => $request->address,
            'kode_pos'         => $request->postal_code,
            'tipe'              => $request->tipe,
            'status'           => $request->active ? 'active' : 'inactive',
            'client_id'         => auth()->user()->client_id
        ]);
        try {
            // Susun payload/parameter sesuai ekspektasi Service SATUSEHAT kamu
            $payload = [
                'part_of_id'  => $satusehatOrgId,
                'name'        => $request->name,
                'phone'       => $request->phone,
                'email'       => $request->email,
                'address'     => $request->address,
                'postal_code' => $request->postal_code,
                'tipe' => $request->tipe,
                'active'      => (bool) $request->active,
            ];
            // Memanggil method dari Service SATUSEHAT
            $response = $this->ssService->createOrganization($payload);
            // DD($response);
            // Cek jika response dari service berhasil dan memiliki ID
            if (isset($response['id'])) {
                $satusehatOrgId = $response['id'];

                // Update record lokal dengan Organization ID yang didapat dari Kemenkes
                $organization->update([
                    'satusehat_org_id' => $satusehatOrgId,
                    'raw_response'     => json_encode($response)
                ]);

                return response()->json([
                    'status'          => true,
                    'is_bridged'      => true,
                    'message'         => 'Data berhasil disimpan dan dibridging ke SATUSEHAT.',
                    'organization_id' => $satusehatOrgId,
                    'data'            => $organization
                ], 200);
            }

            // Jika response service gagal / tidak mengembalikan 'id'
            return response()->json([
                'status'          => true,
                'is_bridged'      => false,
                'message'         => 'Data tersimpan di lokal, namun Gagal Bridging SATUSEHAT.',
                'organization_id' => null,
                'service_response' => $response,
                'data'            => $organization
            ], 200);
        } catch (\Exception $e) {
            Log::error('Bridging SATUSEHAT Organization Error: ' . $e->getMessage());
            return response()->json([
                'status'          => true,
                'is_bridged'      => false,
                'message'         => 'Data tersimpan di lokal, tetapi terjadi kesalahan pada Service Bridging: ' . $e->getMessage(),
                'organization_id' => null,
                'data'            => $organization
            ], 200);
        }
    }
    public function simpanlocation(Request $request)
    {
        // 1. Validasi Input
        $request->validate([
            'managing_organization_id' => 'required|string|max:255', // Organization ID pengelola
            'satusehat_location_id'    => 'nullable|string|max:255', // Opsional jika sudah diisi manual
            'name'                     => 'required|string|max:255', // Nama Ruangan / Lokasi Fisik
            'physical_type'            => 'required|string|in:ro,bd,bu,wi,ve', // Room, Bed, Building, Wing, Vehicle
            'status'                   => 'required|in:active,inactive',
            'description'              => 'nullable|string|max:500',
        ]);
        // 2. Simpan Data ke Database Lokal Terlebih Dahulu
        $location = Location::create([
            'satusehat_location_id'    => $request->satusehat_location_id ?? null,
            'managing_organization_id' => $request->managing_organization_id,
            'nama_lokasi'              => $request->name,
            'tipe_fisik'               => $request->physical_type,
            'status'                   => $request->status,
            'deskripsi'                => $request->description,
            'client_id'         => auth()->user()->client_id
        ]);

        // 3. KONDISI 1: Jika satusehat_location_id SUDAH DIISI MANUAL -> SKIP BRIDGING
        if ($request->filled('satusehat_location_id')) {
            return response()->json([
                'status'      => true,
                'is_bridged'  => false,
                'message'     => 'Data berhasil disimpan ke database lokal (Bridging dilewati karena Location ID sudah diisi manual).',
                'location_id' => $location->satusehat_location_id,
                'data'        => $location
            ], 201);
        }

        // 4. KONDISI 2: Jika satusehat_location_id KOSONG -> PROSES BRIDGING LEWAT SERVICE
        try {
            // Susun payload/parameter untuk dikirim ke SatusehatService
            $payload = [
                'managing_organization_id' => $request->managing_organization_id,
                'name'                     => $request->name,
                'physical_type'            => $request->physical_type,
                'status'                   => $request->status,
                'description'              => $request->description,
            ];

            // Memanggil method dari Service SATUSEHAT
            $response = $this->ssService->createLocation($payload);
            // Cek jika response dari service berhasil dan mengembalikan 'id'
            if (isset($response['id'])) {
                $satusehatLocationId = $response['id'];

                // Update record lokal dengan Location ID dari Kemenkes
                $location->update([
                    'satusehat_location_id' => $satusehatLocationId,
                    'raw_response'          => json_encode($response)
                ]);

                return response()->json([
                    'status'      => true,
                    'is_bridged'  => true,
                    'message'     => 'Data berhasil disimpan di lokal dan dibridging ke SATUSEHAT.',
                    'location_id' => $satusehatLocationId,
                    'data'        => $location
                ], 200);
            }

            // Jika response service gagal / tidak mengembalikan 'id'
            return response()->json([
                'status'           => true,
                'is_bridged'       => false,
                'message'          => 'Data tersimpan di lokal, namun Gagal Bridging SATUSEHAT.',
                'location_id'      => null,
                'service_response' => $response,
                'data'             => $location
            ], 200);
        } catch (\Exception $e) {
            Log::error('Bridging SATUSEHAT Location Error: ' . $e->getMessage());

            return response()->json([
                'status'      => true,
                'is_bridged'  => false,
                'message'     => 'Data tersimpan di lokal, tetapi terjadi kesalahan pada Service Bridging: ' . $e->getMessage(),
                'location_id' => null,
                'data'        => $location
            ], 200);
        }
    }
    public function simpanPractitioner(Request $request)
    {
        $datadokter = Dokter::create([
            'nama_dokter'    => $request->nama_dokter ?? null,
            'nik' => $request->nik,
            'ihs_number'              => $request->ihs_number,
            'jabatan'               => $request->jabatan,
            'is_active'                   => 1,
            'client_id' => auth()->user()->client_id
        ]);
        // Jika response service gagal / tidak mengembalikan 'id'
        return response()->json([
            'status'           => true,
            'is_bridged'       => false,
            'message'          => 'Data tersimpan di lokal, namun Gagal Bridging SATUSEHAT.',
            'location_id'      => null,
            'service_response' => 'sukses',
            'data'             => $datadokter
        ], 200);
    }
    public function updatedokter(Request $request, $id)
    {
        $request->validate([
            'nik'         => 'required|numeric|digits:16',
            'nama_dokter' => 'required|string|max:255',
            'jabatan'     => 'nullable|string|max:100',
            'is_active'   => 'required|in:0,1',
        ]);

        try {
            $dokter = Dokter::findOrFail($id);

            $dokter->update([
                'ihs_number'         => $request->ihs_number,
                'nik'         => $request->nik,
                'nama_dokter' => $request->nama_dokter,
                'jabatan'     => $request->jabatan ?? 'Dokter',
                'is_active'   => $request->is_active,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Data Practitioner berhasil diperbarui!'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui data: ' . $e->getMessage()
            ], 500);
        }
    }
    public function destroydokter($id)
    {
        try {
            $dokter = Dokter::findOrFail($id);
            $dokter->delete();
            return response()->json([
                'success' => true,
                'message' => 'Data Practitioner berhasil dihapus!'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus data: ' . $e->getMessage()
            ], 500);
        }
    }
    public function updateuser(Request $request, $id)
    {
        $request->validate([
            'nama'       => 'required|string|max:255',
            'username'   => 'required|string|max:100|unique:user,username,' . $id,
            'hak_akses'  => 'required',
            'ihs_number' => 'nullable|string|max:100',
            'status'     => 'required|in:0,1',
        ]);

        try {
            $user = User::findOrFail($id);

            $user->update([
                'nama'       => $request->nama,
                'username'   => $request->username,
                'hak_akses'  => $request->hak_akses,
                'ihs_number' => $request->ihs_number,
                'status'     => $request->status,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Data User Practitioner berhasil diperbarui!'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui data: ' . $e->getMessage()
            ], 500);
        }
    }
    public function destroyuser($id)
    {
        try {
            $user = User::findOrFail($id);
            $user->delete();
            return response()->json([
                'success' => true,
                'message' => 'Data User Practitioner berhasil dihapus!'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus data: ' . $e->getMessage()
            ], 500);
        }
    }
}
