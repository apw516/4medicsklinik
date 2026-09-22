<?php

namespace App\Http\Controllers;

use App\Models\billing_details;
use App\Models\Dokter;
use App\Models\ErmRecord;
use App\Models\Kabupaten;
use App\Models\Kecamatan;
use App\Models\Kelurahan;
use App\Models\Kunjungan;
use App\Models\Location;
use App\Models\Organization;
use App\Models\Pasien;
use App\Models\Pembayaran;
use App\Models\Provinsi;
use App\Models\Unit;
use App\Services\SatuSehatService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class PendaftaranController extends Controller
{
    protected $ssService;
    public function __construct(SatuSehatService $ssService)
    {
        $this->ssService = $ssService;
    }
    public function indexmasterpasien(Request $request)
    {
        $menu = 'masterpasien';
        $listProvinsi = Provinsi::orderBy('name', 'asc')->get();
        return view('Rekamedis.indexmasterpasien', compact([
            'menu',
            'listProvinsi'
        ]));
    }
    public function indexriwayatkunjungan(Request $request)
    {
        $menu = 'riwayatkunjungan';
        return view('Rekamedis.indexriwayatkunjungan', compact([
            'menu',
        ]));
    }
    public function getTabelPasien(Request $request)
    {
        $tglAwal = $request->tgl_awal;
        $tglAkhir = $request->tgl_akhir;

        $kunjungans = DB::table('kunjungans')
            // ->leftJoin('pembayarans', 'kunjungans.id', '=', 'pembayarans.kunjungan_id')
            ->leftJoin('pasiens', 'kunjungans.pasien_id', '=', 'pasiens.id')
            ->leftJoin('dokters', 'kunjungans.dokter_id', '=', 'dokters.id')
            ->leftJoin('locations', 'kunjungans.poli_id', '=', 'locations.id')
            ->whereDate('kunjungans.created_at', '>=', $tglAwal)
            ->whereDate('kunjungans.created_at', '<=', $tglAkhir)
            ->where('kunjungans.client_id', '=', auth()->user()->client_id)
            ->select(
                'kunjungans.id',
                'kunjungans.status_kunjungan',
                'kunjungans.created_at as tgl_kunjungan',
                'locations.nama_lokasi as nama_poli',
                'dokters.nama_dokter as nama_dokter',
                'pasiens.no_rm',
                'pasiens.nama_lengkap',
                'kunjungans.status_pembayaran as status_pembayaran',
                // 'kunjungans.total_bayar'
            )
            ->orderBy('kunjungans.created_at', 'DESC')
            ->get();
        
        return view('Rekamedis.tabel_riwayat_kunjungan', compact('kunjungans'));
    }
    public function detailKunjungan($id)
    {
        $kunjungan = Kunjungan::with(['pasien', 'pembayaran'])->findOrFail($id);
        $details = DB::table('billing_details')
            ->leftJoin('master_obats', 'billing_details.obat_id', '=', 'master_obats.id')
            ->leftJoin('master_tarifs', 'billing_details.tarif_id', '=', 'master_tarifs.id')
            ->where('kunjungan_id', $id)
            ->get();

        $erm = DB::table('erm_records')
            ->leftJoin('dokters', 'erm_records.dokter_id', '=', 'dokters.id')
            ->where('erm_records.kunjungan_id', $id)
            ->select(
                'erm_records.*',
                'dokters.nama_dokter',
            )
            ->first();
        return view('Rekamedis.detail_kunjungan', compact('kunjungan', 'details', 'erm'));
    }
    public function batalkunjungan($id)
    {
        // Cek apakah ada record ERM terkait
        $ermExist = ErmRecord::where('kunjungan_id', $id)->exists();
        $kunjungan = Kunjungan::find($id); // Sesuaikan nama Model Kunjungan kamu
        if ($kunjungan->status_kunjungan == 'BATAL') {
            return response()->json([
                'status'  => 'error',
                'message' => 'Kunjungan sudah dibatalkan...'
            ], 422);
        }
        if ($ermExist) {
            // Kondisi 1: Ada record ERM, tidak bisa dibatalkan
            return response()->json([
                'status'  => 'error',
                'message' => 'Kunjungan tidak dapat dibatalkan karena sudah ada data ERM.'
            ], 422);
        }

        // Kondisi 2: Tidak ada ERM, update status kunjungan
        if (!$kunjungan) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Data kunjungan tidak ditemukan.'
            ], 444);
        }

        $kunjungan->update([
            'status_kunjungan' => 'BATAL' // Atau 'BATALK' sesuai kebutuhan kolom DB kamu
        ]);

        return response()->json([
            'status'  => 'success',
            'message' => 'Data kunjungan berhasil dibatalkan.'
        ]);
    }
    public function batalisierm($idkunjungan)
    {
        // Cek apakah pembayaran sudah lunas
        $pembayaranLunas = Pembayaran::where('kunjungan_id', $idkunjungan)
            ->where('status', 'lunas')
            ->exists();
        if ($pembayaranLunas) {
            return response()->json([
                'status'  => 'error',
                'message' => 'ERM tidak dapat dibatalkan karena pembayaran sudah lunas.'
            ], 422);
        }
        // Kondisi 2: Belum lunas, hapus record ERM terkait
        $ermDeleted = ErmRecord::where('kunjungan_id', $idkunjungan)->delete();
        $billingdetails = billing_details::where('kunjungan_id', $idkunjungan)->delete();
        $pembayarans = Pembayaran::where('kunjungan_id', $idkunjungan)->delete();
        if (!$ermDeleted) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Data ERM tidak ditemukan atau sudah dihapus.'
            ], 404);
        }
        return response()->json([
            'status'  => 'success',
            'message' => 'Pengisian ERM berhasil dibatalkan dan data telah dihapus.'
        ]);
    }
    public function cekPasien(Request $request)
    {
        $nik = '3209330506940001';
        try {
            // 2. Panggil API SatuSehat via Service
            $data = $this->ssService->getPatientByNik($nik);

            // 3. Cek jika API mengembalikan respons error/OperationOutcome dari FHIR
            if (isset($data['resourceType']) && $data['resourceType'] === 'OperationOutcome') {
                $errorMessage = $data['issue'][0]['diagnostics'] ?? 'Terjadi kesalahan pada respon SATUSEHAT.';
                return response()->json([
                    'status'  => 'error',
                    'message' => $errorMessage,
                ], 400);
            }

            // 4. Cek ketersediaan data pasien (Bundle entry tidak kosong)
            if (!empty($data['entry']) && isset($data['entry'][0]['resource']['id'])) {
                $resource  = $data['entry'][0]['resource'];
                $ihsNumber = $resource['id'];
                $nama      = $resource['name'][0]['text'] ?? ($resource['name'][0]['given'][0] ?? 'Tanpa Nama');

                // TODO: Simpan/Update ke database lokal mt_pasien
                // \App\Models\Pasien::where('nik', $nik)->update(['ihs_number' => $ihsNumber]);

                return response()->json([
                    'status'  => 'success',
                    'message' => 'Data pasien berhasil ditemukan di SATUSEHAT.',
                    'data'    => [
                        'nik'        => $nik,
                        'ihs_number' => $ihsNumber,
                        'nama'       => $nama,
                    ]
                ], 200);
            }

            // 5. Respons jika NIK tidak terdaftar di SATUSEHAT
            return response()->json([
                'status'  => 'not_found',
                'message' => 'Pasien dengan NIK ' . $nik . ' tidak ditemukan di SATUSEHAT.',
            ], 404);
        } catch (\Exception $e) {
            Log::error('Error SATUSEHAT Cek Pasien [NIK: ' . $nik . ']: ' . $e->getMessage());
            return response()->json([
                'status'  => 'error',
                'message' => 'Gagal terhubung ke layanan SATUSEHAT: ' . $e->getMessage(),
            ], 500);
        }
    }
    public function getProvinsi()
    {
        $provList = $this->ssService->getProvinsi();

        if (empty($provList)) {
            return response()->json(['status' => 'error', 'message' => 'Data provinsi kosong'], 500);
        }

        foreach ($provList as $item) {
            Provinsi::firstOrCreate(
                ['code' => $item['code']], // Acuan pencarian
                ['name' => $item['name']]  // Data tambahan jika baru dibuat
            );
        }

        return response()->json([
            'status'  => 'success',
            'message' => 'Data provinsi berhasil disimpan/diperbarui.',
        ]);
    }
    public function getKabKota(Request $request)
    {
        $codesProv = $request->input('codes_prov');

        if (!$codesProv) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Kode provinsi wajib diisi.'
            ], 400);
        }
        $kabList = $this->ssService->getKabKota($codesProv);
        if (empty($kabList)) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Data Kabupaten/Kota tidak ditemukan atau gagal diambil dari SATUSEHAT.'
            ], 404);
        }

        // 2. Format array data sesuai kolom database
        $dataToInsert = collect($kabList)->map(function ($item) use ($codesProv) {
            return [
                'code'          => $item['code'],
                'name'          => $item['name'],
                'provinsi_code' => $item['parent_code'] ?? $codesProv, // Menyimpan relasi ke kode provinsi
                'created_at'    => now(),
                'updated_at'    => now(),
            ];
        })->toArray();
        Kabupaten::upsert($dataToInsert, ['code'], ['name', 'provinsi_code', 'updated_at']);
        return response()->json([
            'status'  => 'success',
            'message' => 'Data Kabupaten/Kota berhasil disimpan ke database.',
            'total'   => count($dataToInsert),
            'data'    => $dataToInsert
        ]);
    }
    public function getKecamatan(Request $request)
    {
        $codesKabKota = $request->input('codes_kabkota');

        if (!$codesKabKota) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Kode Kabupaten/Kota wajib dipilih.'
            ], 400);
        }

        $kecList = $this->ssService->getKecamatan($codesKabKota);

        if (empty($kecList)) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Data Kecamatan tidak ditemukan atau gagal diambil dari SATUSEHAT.'
            ], 404);
        }

        // 2. Format array data sesuai kolom database
        $dataToInsert = collect($kecList)->map(function ($item) use ($codesKabKota) {
            return [
                'code'         => $item['code'],
                'name'         => $item['name'],
                'kabkota_code' => $item['parent_code'] ?? $codesKabKota, // Kode relasi ke kab/kota
                'created_at'   => now(),
                'updated_at'   => now(),
            ];
        })->toArray();
        Kecamatan::upsert($dataToInsert, ['code'], ['name', 'kabkota_code', 'updated_at']);

        return response()->json([
            'status'  => 'success',
            'message' => 'Data Kecamatan berhasil disimpan ke database.',
            'total'   => count($dataToInsert),
            'data'    => $dataToInsert
        ]);
    }
    public function getKelurahan(Request $request)
    {
        $codesKec = $request->input('codes_kec');
        if (!$codesKec) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Kode Kecamatan wajib diisi.'
            ], 400);
        }

        // 1. Ambil data Kelurahan/Desa dari SATUSEHAT Service
        $kelList = $this->ssService->getKelurahan($codesKec);

        if (empty($kelList)) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Data Kelurahan/Desa tidak ditemukan atau gagal diambil dari SATUSEHAT.'
            ], 404);
        }

        // 2. Format array data untuk database
        $dataToInsert = collect($kelList)->map(function ($item) use ($codesKec) {
            return [
                'code'           => $item['code'],
                'name'           => $item['name'],
                'kecamatan_code' => $item['parent_code'] ?? $codesKec, // Simpan relasi ke kecamatan
                'created_at'     => now(),
                'updated_at'     => now(),
            ];
        })->toArray();

        // 3. Simpan / Update otomatis ke database (Mencegah Duplikasi)
        // Param 1: Data array
        // Param 2: Key unik (primary key / unique index)
        // Param 3: Kolom yang di-update jika kode sudah ada
        Kelurahan::upsert($dataToInsert, ['code'], ['name', 'kecamatan_code', 'updated_at']);

        return response()->json([
            'status'  => 'success',
            'message' => 'Data Kelurahan/Desa berhasil disimpan ke database.',
            'total'   => count($dataToInsert),
            'data'    => $dataToInsert
        ]);;
    }
    public function getKabKotaByProv(Request $request)
    {
        $provCode = $request->input('provinsi_code');
        $kabKota = Kabupaten::where('provinsi_code', $provCode)
            ->orderBy('name', 'asc')
            ->get(['code', 'name']);

        return response()->json($kabKota);
    }
    public function getKecamatanByKab(Request $request)
    {
        $kabCode = $request->input('kabkota_code');
        $kecamatan = Kecamatan::where('kabkota_code', $kabCode)
            ->orderBy('name', 'asc')
            ->get(['code', 'name']);

        return response()->json($kecamatan);
    }
    public function getKelurahanByKec(Request $request)
    {
        $kecCode = $request->input('kecamatan_code');
        $kecamatan = Kelurahan::where('kecamatan_code', $kecCode)
            ->orderBy('name', 'asc')
            ->get(['code', 'name']);
        return response()->json($kecamatan);
    }
    public function storePasien(Request $request)
    {
        // 1. Validasi Input Data
        $validated = $request->validate([
            'no_rm'             => 'nullable|string|max:20|unique:pasiens,no_rm',
            'nik'               => 'required|digits:16',
            'no_bpjs'           => 'nullable|digits:13',
            'ihs_number'        => 'nullable|string|max:64',
            'gelar_depan'       => 'nullable|string|max:20',
            'nama_lengkap'      => 'required|string|max:255',
            'gelar_belakang'    => 'nullable|string|max:20',
            'jenis_kelamin'     => 'required|in:L,P',
            'tempat_lahir'      => 'required|string|max:100',
            'tanggal_lahir'     => 'required|date',
            'golongan_darah'    => 'nullable|in:A,B,AB,O',
            'rhesus'            => 'nullable|in:+,-',
            'agama'             => 'nullable|string|max:20',
            'status_pernikahan' => 'nullable|in:S,M,D,W',
            'pekerjaan'         => 'nullable|string|max:100',
            'pendidikan'        => 'nullable|string|max:50',
            'kewarganegaraan'   => 'nullable|in:WNI,WNA',
            'no_hp'             => 'nullable|string|max:20',
            'email'             => 'nullable|email|max:100',
            'alamat'            => 'required|string',
            'rt'                => 'nullable|string|max:5',
            'rw'                => 'nullable|string|max:5',
            'kode_pos'          => 'nullable|string|max:10',
            'provinsi_code'     => 'nullable|string|max:10',
            'kabkota_code'      => 'nullable|string|max:10',
            'kecamatan_code'    => 'nullable|string|max:10',
            'kelurahan_code'    => 'nullable|string|max:10',
            'nama_pj'           => 'nullable|string|max:150',
            'hubungan_pj'       => 'nullable|string|max:50',
            'no_hp_pj'          => 'nullable|string|max:20',
        ], [
            'nik.required'      => 'NIK wajib diisi.',
            'nik.digits'        => 'NIK harus berjumlah 16 digit.',
            // 'nik.unique'        => 'NIK sudah terdaftar pada sistem.',
            'no_rm.unique'      => 'Nomor RM sudah digunakan.',
            'no_bpjs.digits'    => 'Nomor BPJS harus 13 digit.',
        ]);

        // 2. Prosedur Pengecekan & Pembuatan IHS Number SatuSehat
        $ihsFound = false;

        // A. Cek IHS Pasien via API
        try {
            $ssResponse = $this->ssService->getPatientByNik($validated['nik']);
            // Validasi struktur JSON standar FHIR Bundle SatuSehat (entry[0].resource.id)
            if (
                is_array($ssResponse) &&
                isset($ssResponse['entry'][0]['resource']['id']) &&
                !empty($ssResponse['entry'][0]['resource']['id'])
            ) {
                $validated['ihs_number'] = $ssResponse['entry'][0]['resource']['id'];
                $ihsFound = true;
            } elseif (isset($ssResponse['ihs_number']) && !empty($ssResponse['ihs_number'])) {
                // Fallback jika ssService mengembalikan format kustom
                $validated['ihs_number'] = $ssResponse['ihs_number'];
                $ihsFound = true;
            } else {
                Log::info("IHS untuk NIK {$validated['nik']} tidak ditemukan pada pencarian. Memulai pendaftaran baru.");
            }
        } catch (\Throwable $e) {
            Log::error("Gagal melakukan pencarian NIK {$validated['nik']} ke API SatuSehat: " . $e->getMessage());
        }

        // B. Buat Pasien Baru di SatuSehat jika tidak ditemukan & pencarian awal tidak mengalami kendala jaringan fatal
        if (!$ihsFound) {
            try {
                $createResponse = $this->ssService->createPatient($validated);
                // DD($createResponse);
                if (is_array($createResponse) && !empty($createResponse['id'])) {
                    $validated['ihs_number'] = $createResponse['id'];
                } elseif (isset($createResponse['ihs_number']) && !empty($createResponse['ihs_number'])) {
                    $validated['ihs_number'] = $createResponse['ihs_number'];
                } else {
                    Log::warning("Respon pembuatan pasien SatuSehat tidak mengembalikan ID IHS: " . json_encode($createResponse));
                }
            } catch (\Throwable $e) {
                Log::error("Gagal mendaftarkan pasien NIK {$validated['nik']} ke SatuSehat: " . $e->getMessage());
            }
        }
        // 3. Generate No. RM Otomatis jika input kosong
        if (empty($validated['no_rm'])) {
            $validated['no_rm'] = $this->generateNoRm();
        }
        // 4. Simpan ke Database Lokal
        try {
            DB::beginTransaction();
            $validated['client_id'] = auth()->user()->client_id;
            $pasien = Pasien::create($validated);
            DB::commit();
            $msg = "Pasien {$pasien->nama_lengkap} (No. RM: {$pasien->no_rm}) berhasil ditambahkan!";
            if (empty($pasien->ihs_number)) {
                $msg .= " (Catatan: IHS SatuSehat belum terhubung)";
            }
            return redirect()->route('pasien.index')->with('success', $msg);
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withInput()
                ->with('error', 'Gagal menyimpan data pasien ke database: ' . $e->getMessage());
        }
    }
    private function generateNoRm(): string
    {
        // Mengambil record ID terakhir termasuk yang ter-softdelete
        $lastPasien = Pasien::withTrashed()->latest('id')->first();
        $nextId = $lastPasien ? $lastPasien->id + 1 : 1;
        // Pad angka ke 8 digit (misal: 1 -> 00000001)
        $padded = str_pad($nextId, 8, '0', STR_PAD_LEFT);

        // Format menjadi 00-00-00-01
        return vsprintf('%s%s-%s%s-%s%s-%s%s', str_split($padded));
    }
    public function caripasien(Request $request)
    {
        $pasiens = Pasien::query()
            ->where('client_id', auth()->user()->client_id) // Filter berdasarkan client_id user yang sedang login
            ->when($request->filled('no_rm'), fn($q) => $q->where('no_rm', 'like', '%' . $request->no_rm . '%'))
            ->when($request->filled('no_ktp'), fn($q) => $q->where('nik', 'like', '%' . $request->no_ktp . '%'))
            ->when($request->filled('nama'), fn($q) => $q->where('nama_lengkap', 'like', '%' . $request->nama . '%'))
            ->when($request->filled('alamat'), fn($q) => $q->where('alamat', 'like', '%' . $request->alamat . '%'))
            ->latest()
            ->paginate(10);
        // Jika Request dari AJAX jQuery, kembalikan Partial View Tabel
        if ($request->ajax()) {
            return view('Rekamedis.tabel_pasien', compact('pasiens'))->render();
        }
        return view('Rekamedis.tabel_pasien', compact('pasiens'));
    }
    public function formpendaftaran(Request $request)
    {
        $rm = $request->rm;
        $pasien = Pasien::where('no_rm', $rm)->get()->first();
        // $unit = Unit::get();
        $unit = Location::where('client_id',auth()->user()->client_id)->get();
        $dokter = Dokter::where('client_id',auth()->user()->client_id)->get();
        $data_kunjungan = Kunjungan::with(['unit', 'dokter'])
            ->where('pasien_id', $pasien->id)
            ->where('status_kunjungan', '!=', 'BATAL')
            ->orderBy('tgl_masuk', 'desc')
            ->get();
        return view('Rekamedis.form_pendaftaran', compact('pasien', 'data_kunjungan', 'unit', 'dokter'));
    }
    public function storependaftaran(Request $request)
    {
        // 1. Validasi Input Data Form
        $validatedData = $request->validate([
            'pasien_id'        => 'required|exists:pasiens,id',
            'jenis_kunjungan'  => 'required|in:RAWAT_JALAN,RAWAT_INAP,IGD',
            'satusehat_class'  => 'required|string',
            'satusehat_status' => 'required|string',
            'cara_masuk'       => 'required|string',
            'tgl_masuk'        => 'required|date',
            'penjamin'         => 'required|in:UMUM,BPJS,ASURANSI_SWASTA,PERUSAHAAN',
            'poli_id'          => 'required|exists:locations,id',
            'dokter_id'        => 'required|exists:dokters,id',
            'keluhan_utama'    => 'nullable|string|max:255',

            // Validasi Kondisional untuk BPJS Kesehatan
            'no_sep'           => 'nullable|required_if:penjamin,BPJS|string|max:50',
            'no_rujukan'       => 'nullable|string|max:50',
            'no_surat_kontrol' => 'nullable|string|max:50',
            'kelas_rawat_bpjs' => 'nullable|required_if:penjamin,BPJS|string',
            'diag_awal_bpjs'   => 'nullable|string|max:50',
            'catatan_sep'      => 'nullable|string|max:255',
        ], [
            'pasien_id.required'           => 'Identitas pasien wajib dipilih.',
            'jenis_kunjungan.required'     => 'Jenis kunjungan harus dipilih.',
            'poli_id.required'             => 'Poliklinik / Unit tujuan wajib diisi.',
            'dokter_id.required'           => 'Dokter DPJP wajib diisi.',
            'no_sep.required_if'           => 'Nomor SEP wajib diisi jika penjamin adalah BPJS Kesehatan.',
            'kelas_rawat_bpjs.required_if' => 'Kelas rawat BPJS wajib dipilih jika penjamin adalah BPJS Kesehatan.',
        ]);

        DB::beginTransaction();
        try {
            // 2. Generate Nomor Registrasi Kunjungan Otomatis
            $today = Carbon::now()->format('Ymd');
            $countToday = Kunjungan::whereDate('created_at', Carbon::today())->count() + 1;
            $noRegistrasi = 'REG-' . $today . '-' . str_pad($countToday, 4, '0', STR_PAD_LEFT);

            // 3. Simpan Data ke Tabel kunjungans
            $kunjungan = Kunjungan::create([
                'no_registrasi'    => $noRegistrasi,
                'pasien_id'        => $request->pasien_id,
                'jenis_kunjungan'  => $request->jenis_kunjungan,
                'cara_masuk'       => $request->cara_masuk,
                'tgl_masuk'        => $request->tgl_masuk,
                'penjamin'         => $request->penjamin,
                'poli_id'          => $request->poli_id,
                'dokter_id'        => $request->dokter_id,
                'keluhan_utama'    => $request->keluhan_utama,
                'status'           => 'ANTRIAN',
                'satusehat_class'  => $request->satusehat_class ?? 'AMB',
                'satusehat_status' => $request->satusehat_status ?? 'arrived',
                'no_sep'           => $request->penjamin === 'BPJS' ? $request->no_sep : null,
                'no_rujukan'       => $request->penjamin === 'BPJS' ? $request->no_rujukan : null,
                'no_surat_kontrol' => $request->penjamin === 'BPJS' ? $request->no_surat_kontrol : null,
                'kelas_rawat_bpjs' => $request->penjamin === 'BPJS' ? $request->kelas_rawat_bpjs : null,
                'diag_awal_bpjs'   => $request->penjamin === 'BPJS' ? $request->diag_awal_bpjs : null,
                'catatan_sep'      => $request->penjamin === 'BPJS' ? $request->catatan_sep : null,
                'client_id'         => auth()->user()->client_id
            ]);

            // 4. Bridging SATUSEHAT: Cek IHS Pasien & Buat Encounter
            $pasien = Pasien::find($request->pasien_id);

            if (!empty($pasien->ihs_number)) {
                // Load relasi agar data pendukung Encounter (IHS Dokter, Org ID Poli, dll) terbawa
                $kunjungan->load(['pasien', 'dokter', 'poli']);
                // Panggil service storeEncounter dengan mengirim object kunjungan
                $ssResponse = $this->ssService->storeEncounter($kunjungan);
                // DD($ssResponse);
                // Jika berhasil mendapat ID Encounter dari SATUSEHAT, simpan ke database
                if (isset($ssResponse['status']) && $ssResponse['status'] === true && isset($ssResponse['id'])) {
                    $kunjungan->update([
                        'satusehat_encounter_id' => $ssResponse['id']
                    ]);
                }
            }
            DB::commit();

            return response()->json([
                'status'  => 'success',
                'message' => 'Pendaftaran kunjungan berhasil disimpan.',
                'data'    => $kunjungan
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'status'  => 'error',
                'message' => 'Terjadi kesalahan sistem: ' . $e->getMessage()
            ], 500);
        }
    }
    public function updatepasien(Request $request, SatuSehatService $satuSehat)
    {
        // 1. Validasi Input (Lengkap sesuai form Blade)
        $validated = $request->validate([
            'no_rm'             => 'required|exists:pasiens,no_rm',
            'nik'               => 'required|numeric|digits:16',
            'no_bpjs'           => 'nullable|string|max:13',
            'ihs_number'        => 'nullable|string',
            'gelar_depan'       => 'nullable|string',
            'nama_lengkap'      => 'required|string|max:255',
            'gelar_belakang'    => 'nullable|string',
            'jenis_kelamin'     => 'required|in:L,P',
            'tempat_lahir'      => 'required|string|max:100',
            'tanggal_lahir'     => 'required|date',
            'golongan_darah'    => 'nullable|in:A,B,AB,O',
            'rhesus'            => 'nullable|in:+,-',
            'agama'             => 'nullable|string',
            'status_pernikahan' => 'nullable|in:S,M,D,W',
            'pekerjaan'         => 'nullable|string|max:100',
            'pendidikan'        => 'nullable|string',
            'kewarganegaraan'   => 'nullable|in:WNI,WNA',
            'no_hp'             => 'nullable|string|max:20',
            'email'             => 'nullable|email|max:255',
            'alamat'            => 'required|string',
            'rt'                => 'nullable|string|max:5',
            'rw'                => 'nullable|string|max:5',
            'kode_pos'          => 'nullable|string|max:10',
            'provinsi_code'     => 'nullable|string',
            'kabkota_code'      => 'nullable|string',
            'kecamatan_code'    => 'nullable|string',
            'kelurahan_code'    => 'nullable|string',
            'nama_pj'           => 'nullable|string|max:255',
            'hubungan_pj'       => 'nullable|string',
            'no_hp_pj'          => 'nullable|string|max:20',
        ]);

        // 2. Cari Data Pasien Berdasarkan No. RM (atau NIK jika Form Add/Edit berbasis No RM)
        $pasien = Pasien::where('no_rm', $validated['no_rm'])->firstOrFail();

        // 3. Update Data Pasien di Database
        $pasien->update($validated);

        $statusMsg = 'Data pasien berhasil diperbarui.';

        // 4. Cek Jika IHS Number Masih Kosong -> Daftarkan ke SATUSEHAT
        if (empty($pasien->ihs_number)) {
            try {
                // Mengirim data pasien terbaru ke SATUSEHAT
                $createResponse = $satuSehat->createPatient($pasien->toArray());

                $ihsId = null;
                if (is_array($createResponse)) {
                    $ihsId = $createResponse['id'] ?? $createResponse['ihs_number'] ?? null;
                }

                if ($ihsId) {
                    // Simpan IHS Number ke Database Pasien
                    $pasien->update(['ihs_number' => $ihsId]);
                    $statusMsg .= ' Berhasil didaftarkan ke SATUSEHAT.';
                } else {
                    Log::warning("Respon pembuatan pasien SATUSEHAT tidak mengembalikan ID IHS: " . json_encode($createResponse));
                    $statusMsg .= ' (Gagal mendapatkan IHS Number dari SATUSEHAT).';
                }
            } catch (\Throwable $e) {
                Log::error("Gagal mendaftarkan pasien NIK {$pasien->nik} ke SATUSEHAT: " . $e->getMessage());
                $statusMsg .= ' (Terjadi kesalahan koneksi SATUSEHAT).';
            }
        }

        return response()->json([
            'success'    => true,
            'message'    => $statusMsg,
            'ihs_number' => $pasien->ihs_number
        ]);
    }
    public function ambilFormEditPasien(Request $request)
    {
        $id = $request->id;
        $pasien = Pasien::where('id', $id)->get()->first();
        $listProvinsi = Provinsi::orderBy('name', 'asc')->get();

        return view('Rekamedis.form_edit_pasien', compact([
            'pasien',
            'listProvinsi'
        ]));
    }
}
