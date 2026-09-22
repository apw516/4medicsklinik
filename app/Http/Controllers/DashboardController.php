<?php

namespace App\Http\Controllers;

use App\Models\master_obats;
use App\Models\master_tarifs;
use App\Services\SatuSehatService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DashboardController extends Controller
{
    public function index()
    {
        $menu = 'dashboard';
        return view('Dashboard.index', compact([
            'menu'
        ]));
    }
    public function indexmasterunit()
    {
        $menu = 'indexmasterunit';
        $units = DB::table('locations')
            ->where('client_id',auth()->user()->client_id)
            ->orderBy('nama_lokasi', 'asc')
            ->get();
        $organizations = DB::table('organizations')
            ->where('client_id',auth()->user()->client_id)
            ->orderBy('nama_organisasi', 'asc')
            ->get();

        return view('Dashboard.indexmasterunit', compact([
            'units',
            'menu',
            'organizations'
        ]));
    }
    public function indexmastertarif()
    {
        $menu = 'indexmastertarif';
        $tarifs = DB::table('master_tarifs')
            ->where('client_id',auth()->user()->client_id)
            ->orderBy('nama_tindakan', 'asc')
            ->get();
        return view('Dashboard.indexmastertarif', compact([
            'menu',
            'tarifs'
        ]));
    }
    public function indexmasterobat()
    {
        $menu = 'indexmasterobat';
        $obats = master_obats::latest()->get();
        return view('Dashboard.indexmasterobat', compact([
            'menu',
            'obats'
        ]));
    }
    // Simpan Data Baru
    public function storetarif(Request $request)
    {
        $request->validate([
            'nama_tindakan' => 'required|string|max:255',
            'harga'         => 'required|numeric|min:0',
        ]);
        $kode_tindakan = $this->get_kode_tindakan();
        DB::table('master_tarifs')->insert([
            'kode_tindakan' => $kode_tindakan,
            'nama_tindakan' => $request->nama_tindakan,
            'harga'         => $request->harga, // Sesuaikan jika nama kolom harga di DB adalah 'tarif'
            'kategori'         => $request->kategori, // Sesuaikan jika nama kolom harga di DB adalah 'tarif'
            'icd9_code'         => $request->icd9_code, // Sesuaikan jika nama kolom harga di DB adalah 'tarif'
            'icd9_display'         => $request->icd9_display, // Sesuaikan jika nama kolom harga di DB adalah 'tarif'
            'is_active'         => 1, // Sesuaikan jika nama kolom harga di DB adalah 'tarif'
            'created_at'    => now(),
            'updated_at'    => now(),
            'client_id'    => auth()->user()->client_id,
        ]);

        return redirect()->back()->with('success', 'Data tarif berhasil ditambahkan.');
    }

    public function updatetarif(Request $request, $id)
    {
        $request->validate([
            'nama_tindakan' => 'required|string|max:255',
            'harga'         => 'required|numeric|min:0',
        ]);
        DB::table('master_tarifs')->where('id', $id)->update([
            'nama_tindakan' => $request->nama_tindakan,
            'harga'         => $request->harga,
            'kategori'         => $request->kategori,
            'icd9_code'         => $request->icd9_code,
            'icd9_display'         => $request->icd9_display,
            'updated_at'    => now(),
        ]);

        return redirect()->back()->with('success', 'Data tarif berhasil diperbarui.');
    }
    public function destroyorg($id)
    {
        DB::table('organizations')->where('id', $id)->delete();

        return redirect()->back()->with('success', 'Data tarif berhasil dihapus.');
    }
    public function destroyunit($id)
    {
        DB::table('locations')->where('id', $id)->delete();

        return redirect()->back()->with('success', 'Data tarif berhasil dihapus.');
    }
    public function destroytarif($id)
    {
        DB::table('master_tarifs')->where('id', $id)->delete();

        return redirect()->back()->with('success', 'Data tarif berhasil dihapus.');
    }
    public function searchIcd9(Request $request)
    {
        $search = $request->get('q');

        $data = DB::table('mt_icd9')
            ->where('diag', 'LIKE', "%{$search}%")
            ->orWhere('nama_panjang', 'LIKE', "%{$search}%")
            ->limit(20)
            ->get(['diag', 'nama_panjang']); // Sesuaikan nama kolom tabel mt_icd9 Anda

        return response()->json($data);
    }
    public function storeobat(Request $request)
    {
        $request->validate([
            'nama_obat'   => 'required|string|max:255',
            'kategori'    => 'nullable|string|max:100',
            'satuan'      => 'nullable|string|max:50',
            'kfa_code'    => 'nullable|string|max:100',
            'kfa_display' => 'nullable|string|max:255',
        ]);
        master_obats::create([
            'kode_obat' => $this->generateKodeObat(),
            'nama_obat'   => $request->nama_obat,
            'kategori'    => $request->kategori,
            'satuan'      => $request->satuan,
            'stok' => 1000,
            'harga_beli' => 1000,
            'harga_jual' => 1000,
            'kfa_code'    => $request->kfa_code,
            'kfa_display' => $request->kfa_display,
            'is_active' => 1,
        ]);

        return redirect()->back()->with('success', 'Data obat berhasil ditambahkan.');
    }

    public function updateobat(Request $request, $id)
    {
        $request->validate([
            'nama_obat'   => 'required|string|max:255',
            'kategori'    => 'nullable|string|max:100',
            'satuan'      => 'nullable|string|max:50',
            'kfa_code'    => 'nullable|string|max:100',
            'kfa_display' => 'nullable|string|max:255',
        ]);

        $obat = master_obats::findOrFail($id);
        $obat->update([
            'nama_obat'   => $request->nama_obat,
            'kategori'    => $request->kategori,
            'satuan'      => $request->satuan,
            'kfa_code'    => $request->kfa_code,
            'kfa_display' => $request->kfa_display,
        ]);

        return redirect()->back()->with('success', 'Data obat berhasil diperbarui.');
    }

    public function destroyobat($id)
    {
        $obat = master_obats::findOrFail($id);
        $obat->delete();

        return redirect()->back()->with('success', 'Data obat berhasil dihapus.');
    }
    public function searchKfa(Request $request, SatuSehatService $satuSehat)
    {
        $search = $request->get('q');

        if (empty($search) || strlen($search) < 3) {
            return response()->json([]);
        }

        try {
            // Mengirim kata kunci pencarian ke SATUSEHAT melalui Service
            $kfaResponse = $satuSehat->searchKfa($search);
            // dd($kfaResponse);
            $items = [];
            if (is_array($kfaResponse)) {
                // Ambil array produk dari struktur response SATUSEHAT
                $items = $kfaResponse['items']['data'] ?? $kfaResponse['data'] ?? $kfaResponse ?? [];
                // dd($items);
            }

            if (!empty($items) && is_array($items)) {
                // Format response untuk Select2 / AutoComplete Frontend
                $formattedData = array_map(function ($item) {
                    return [
                        'code'    => $item['kfa_code'] ?? $item['code'] ?? '',
                        'display' => $item['name'] ?? $item['display'] ?? '',
                        'satuan'   => $item['dosage_form']['name'] ?? $item['uom']['name'] ?? $item['unit'] ?? '',
                        // Ambil kategori obat (misal: Obat Keras, Farmasi)
                        'kategori' => $item['category'] ?? $item['product_type'] ?? 'Farmasi',
                    ];
                }, $items);
                return response()->json($formattedData);
            } else {
                Log::warning("Pencarian KFA SATUSEHAT tidak mengembalikan data untuk query: " . $search);
                return response()->json([]);
            }
        } catch (\Throwable $e) {
            Log::error("Gagal melakukan pencarian KFA SATUSEHAT untuk query {$search}: " . $e->getMessage());
            return response()->json([], 500);
        }
    }
    public function get_kode_tindakan()
    {
        // Mengambil record ID terakhir termasuk yang ter-softdelete
        $lastTarif = master_tarifs::latest('id')->first();
        $nextId = $lastTarif ? $lastTarif->id + 1 : 1;
        // Pad angka ke 8 digit (misal: 1 -> 00000001)
        $padded = str_pad($nextId, 3, '0', STR_PAD_LEFT);

        // Format menjadi 00-00-00-01
        return 'TND-' . $padded;
    }
    public function generateKodeObat()
    {
        $prefix = 'OBT-';
        // Ambil kode_obat terakhir yang berawalan 'OBT-'
        $lastObat = master_obats::where('kode_obat', 'LIKE', $prefix . '%')
            ->orderBy('kode_obat', 'desc')
            ->first();
        if (!$lastObat) {
            // Jika belum ada data sama sekali, mulai dari OBT-00001
            $nextNumber = 1;
        } else {
            // Ambil angka setelah prefix 'OBT-' (misal: dari 'OBT-01499' diambil '01499')
            $lastNumber = (int) substr($lastObat->kode_obat, strlen($prefix));
            $nextNumber = $lastNumber + 1;
        }

        // Format angka menjadi 5 digit string (contoh: 1 -> 00001, 1500 -> 01500)
        $newKodeObat = $prefix . str_pad($nextNumber, 5, '0', STR_PAD_LEFT);

        return $newKodeObat;
    }
}
