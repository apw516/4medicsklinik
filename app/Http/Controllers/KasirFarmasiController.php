<?php

namespace App\Http\Controllers;

use App\Models\billing_details;
use Illuminate\Http\Request;
use App\Models\Dokter;
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
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class KasirFarmasiController extends Controller
{
    protected $ssService;
    public function __construct(SatuSehatService $ssService)
    {
        $this->ssService = $ssService;
    }
    public function indexriwayatpembayaran(Request $request)
    {
        $menu = 'indexriwayatpembayaran';
        // Ambil filter tanggal (default hari ini)
        $tanggal = $request->input('tanggal', Carbon::today()->toDateString());
        $search  = $request->input('search');
        // Query Utama berbasis Pembayaran
        $query = Pembayaran::with(['kunjungan.pasien', 'kunjungan.poli', 'billing_details'])
            ->where('status_tagihan', 'LUNAS')
            // Filter berdasarkan tanggal transaksi pembayaran
            ->whereDate('updated_at', $tanggal);
        // Filter Pencarian (Nama Pasien / No RM via relasi kunjungan)
        if (!empty($search)) {
            $query->whereHas('kunjungan.pasien', function ($q) use ($search) {
                $q->where('nama_lengkap', 'like', "%{$search}%")
                    ->orWhere('no_rm', 'like', "%{$search}%");
            });
        }
        $pembayarans = $query->orderBy('updated_at', 'desc')
            ->paginate(15)
            ->withQueryString();
        // Hitung total ringkasan pendapatan dari koleksi pembayaran
        $totalPendapatan = $pembayarans->sum(function ($pembayaran) {
            return $pembayaran->total_bayar ?? $pembayaran->billing_details->sum('subtotal');
        });

        return view('KasirFarmasi.indexpembayaran', compact('pembayarans', 'tanggal', 'search', 'menu', 'totalPendapatan'));
    }
    public function ambil_riwayat_pembayaran(Request $request)
    {
        $pembayarans = DB::table('pembayarans as a')
            ->select([
                'b.id as id_kunjungan',
                'b.no_surat_kontrol',
                'c.nama_lengkap',
                'c.no_rm',
                'a.jumlah_bayar',
                'a.status_tagihan',
                'a.metode_pembayaran',
                'a.no_transaksi',
                'b.tgl_masuk',
                'a.id as id_pembayaran',
                'd.nama_lokasi',
            ])
            ->join('kunjungans as b', 'a.kunjungan_id', '=', 'b.id')
            ->join('pasiens as c', 'b.pasien_id', '=', 'c.id')
            ->join('locations as d', 'b.poli_id', '=', 'd.id')
            ->whereBetween(DB::raw('DATE(b.tgl_masuk)'), [$request->tanggalawal, $request->tanggalakhir])
            ->where('a.client_id', auth()->user()->client_id)
            ->get();
        return view('KasirFarmasi.tabelriwayatpembayaran', compact(['pembayarans']));
    }
    public function indexfarmasi(Request $request)
    {
        // Default tanggal adalah hari ini (Y-m-d) jika request kosong
        $tanggal = $request->input('tanggal', Carbon::today()->toDateString());
        $menu = 'indexdatapasienfarmasi';

        // Mengambil kunjungan yang status pembayarannya 'lunas' pada tanggal tersebut
        $kunjungans = Kunjungan::with(['pasien', 'poli', 'billing_details'])
            ->where('status_pembayaran', 'lunas')
            ->where('client_id', auth()->user()->client_id)
            ->whereDate('created_at', $tanggal) // Sesuaikan dengan kolom tanggal kunjungan (e.g. 'created_at' / 'tgl_kunjungan')
            ->orderBy('updated_at', 'desc')
            ->paginate(15)
            ->withQueryString();

        return view('KasirFarmasi.indexfarmasi', compact('kunjungans', 'tanggal', 'menu'));
    }
    public function getDetailObatFarmasi($id)
    {
        $kunjungan = Kunjungan::with(['pasien', 'poli'])->findOrFail($id);
        // Ambil rincian billing yang berupa OBAT / RESEP yang berstatus lunas
        // Sesuaikan 'jenis' atau 'kategori' sesuai struktur database Anda (misal: jenis = 'obat' atau 'resep')
        $obats = billing_details::where('billing_details.kunjungan_id', $id)
            ->where('billing_details.status_tagihan', 'sudah dibayar')
            ->whereNotNull('billing_details.obat_id')
            ->join('master_obats', 'billing_details.obat_id', '=', 'master_obats.id')
            ->select(
                'billing_details.*',
                'master_obats.nama_obat',
                'master_obats.satuan',
                'master_obats.kode_obat'
            )
            ->get();
        return response()->json([
            'status'     => 'success',
            'kunjungan'  => $kunjungan,
            'obats'      => $obats,
        ]);
    }
    public function indexkasir(Request $request)
    {
        $menu = 'indexdatapasienkasir';
        $tanggal = $request->get('tanggal', date('Y-m-d'));
        $status  = $request->get('status', 'BELUM LUNAS');
        $search  = $request->get('search');

        // 1. Eager Loading Pembayaran -> Kunjungan -> Pasien & Poli
        $query = pembayaran::with(['kunjungan.pasien', 'kunjungan.poli'])
            ->whereDate('created_at', $tanggal)->where('client_id', auth()->user()->client_id);

        // 2. Filter Status Tagihan (Sesuai kolom status_tagihan di tabel pembayarans)
        if ($status !== 'semua') {
            $query->where('status_tagihan', $status);
        }

        // 3. Filter Pencarian Pasien / No Transaksi / No RM / Nama Pasien
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('no_transaksi', 'like', "%{$search}%")
                    ->orWhereHas('kunjungan', function ($qk) use ($search) {
                        $qk->where('no_registrasi', 'like', "%{$search}%")
                            ->orWhereHas('pasien', function ($qp) use ($search) {
                                $qp->where('nama_lengkap', 'like', "%{$search}%")
                                    ->orWhere('no_rm', 'like', "%{$search}%");
                            });
                    });
            });
        }

        $listKunjungan = $query->orderBy('created_at', 'desc')->paginate(10);

        // 4. Ringkasan KPI Statistik Hari Ini (Mengacu pada tabel pembayarans)
        $totalInProgress = pembayaran::whereDate('created_at', $tanggal)
            ->where('status_tagihan', 'BELUM LUNAS')
            ->where('client_id', auth()->user()->client_id)
            ->count();

        $totalLunas = pembayaran::whereDate('created_at', $tanggal)
            ->where('status_tagihan', 'LUNAS')
            ->where('client_id', auth()->user()->client_id)

            ->count();

        // Hitung akumulasi pendapatan khusus yang statusnya LUNAS hari ini
        $totalPendapatan = pembayaran::whereDate('created_at', $tanggal)
            ->where('status_tagihan', 'LUNAS')
            ->where('client_id', auth()->user()->client_id)
            ->sum('total_bayar');
        // DD($listKunjungan);
        return view('KasirFarmasi.indexkasir', compact(
            'listKunjungan',
            'menu',
            'totalInProgress',
            'totalLunas',
            'totalPendapatan'
        ));
    }
    public function getDetailTagihan($id, $kodekunjungan)
    {
        // 1. Ambil data utama Kunjungan, Pasien, dan Poli
        $kunjungan = DB::table('kunjungans as k')
            ->leftJoin('pasiens as p', 'k.pasien_id', '=', 'p.id')
            ->leftJoin('locations as pol', 'k.poli_id', '=', 'pol.id')
            ->select(
                'k.id',
                'k.no_registrasi',
                // 'k.biaya_registrasi',
                'p.nama_lengkap as nama_pasien_kunjungan',
                'p.no_rm as no_rm_kunjungan',
                'p.nama_lengkap as nama',
                'p.no_rm',
                'pol.nama_lokasi'
            )
            ->where('k.id', $kodekunjungan)
            ->first();
        // dd($kunjungan);
        if (!$kunjungan) {
            return response()->json(['message' => 'Data kunjungan tidak ditemukan'], 404);
        }

        $rincian = [];
        $totalTagihan = 0;

        // 2. Tambahkan Biaya Registrasi/Administrasi (jika ada)
        if (isset($kunjungan->biaya_registrasi) && $kunjungan->biaya_registrasi > 0) {
            $rincian[] = [
                'nama_layanan' => 'Biaya Administrasi & Registrasi',
                'qty'          => 1,
                'harga'        => (float) $kunjungan->biaya_registrasi,
                'subtotal'     => (float) $kunjungan->biaya_registrasi,
            ];
            $totalTagihan += (float) $kunjungan->biaya_registrasi;
        }
        // 3. JOIN Manual tabel billing_details dengan master_obats dan master_tarifs
        $details = DB::table('billing_details as bd')
            ->leftJoin('master_obats as mo', 'bd.obat_id', '=', 'mo.id')
            ->leftJoin('master_tarifs as mt', 'bd.tarif_id', '=', 'mt.id')
            ->select(
                'bd.*',
                'mo.nama_obat',
                'mo.nama_obat as nama_obat_alt',
                'mt.nama_tindakan'
            )
            ->where('bd.pembayaran_id', $id)
            ->get();

        // 4. Loop data detail tagihan
        foreach ($details as $detail) {
            $namaLayanan = 'Layanan / Tindakan';

            if (!empty($detail->nama_obat)) {
                $namaLayanan = 'Obat: ' . $detail->nama_obat;
            } elseif (!empty($detail->nama_obat_alt)) {
                $namaLayanan = 'Obat: ' . $detail->nama_obat_alt;
            } elseif (!empty($detail->nama_tindakan)) {
                $namaLayanan = $detail->nama_tindakan;
            } elseif (!empty($detail->nama_tarif)) {
                $namaLayanan = $detail->nama_tarif;
            } elseif (!empty($detail->deskripsi)) {
                $namaLayanan = $detail->deskripsi;
            } elseif (!empty($detail->nama_item)) {
                $namaLayanan = $detail->nama_item;
            }

            $qty      = $detail->qty ?? $detail->jumlah ?? 1;
            $harga    = $detail->harga ?? $detail->tarif ?? 0;
            $subtotal = $detail->subtotal ?? ($harga * $qty);

            $rincian[] = [
                'nama_layanan' => $namaLayanan,
                'qty'          => (int) $qty,
                'harga'        => (float) $harga,
                'subtotal'     => (float) $subtotal,
            ];

            $totalTagihan += (float) $subtotal;
        }

        // 5. Cek Pembayaran di tabel pembayarans
        $pembayaran = DB::table('pembayarans')
            ->where('id', $id)
            ->where('status', 'lunas')
            ->first();

        $isLunas = $pembayaran ? true : false;
        $statusPembayaran = $isLunas ? 'LUNAS' : 'BELUM DIBAYAR';
        // 6. Response JSON dengan Info Status Pembayaran
        return response()->json([
            'status'            => 'success',
            'status_pembayaran' => $statusPembayaran,
            'is_lunas'          => $isLunas,
            'dapat_dibayar'     => !$isLunas, // Digunakan frontend untuk enable/disable tombol bayar
            'no_registrasi'     => $kunjungan->no_registrasi,
            'pasien'            => [
                'nama'  => $kunjungan->nama ?? $kunjungan->nama_pasien_kunjungan ?? '-',
                'no_rm' => $kunjungan->no_rm ?? $kunjungan->no_rm_kunjungan ?? '-',
            ],
            'poli'              => [
                'nama_poli' => $kunjungan->nama_lokasi ?? '-',
            ],
            'rincian'           => $rincian,
            'total'             => $totalTagihan,
            'detail_pembayaran' => $pembayaran ? [
                'metode_pembayaran' => $pembayaran->metode_pembayaran,
                'jumlah_bayar'      => (float) $pembayaran->jumlah_bayar,
                'tanggal_bayar'     => $pembayaran->created_at ?? null,
            ] : null
        ]);
    }
    public function prosesPembayaran(Request $request)
    {
        // Validasi input dari form kasir
        $kode_pembayaran = $request->kunjungan_id;
        $request->validate([
            'metode_pembayaran' => 'required|string',
            'jumlah_bayar'      => 'required|numeric|min:0',
        ]);
        DB::beginTransaction();
        try {
            // 1. Ambil data header pembayaran beserta relasi kunjungannya
            $pembayaran = Pembayaran::findOrFail($kode_pembayaran);
            // 2. Validasi jika tagihan sudah LUNAS
            if (strtoupper($pembayaran->status_tagihan) === 'LUNAS') {
                DB::rollBack();
                return redirect()->back()->with('error', 'Tagihan ini sudah berstatus LUNAS.');
            }
            // 3. Validasi Nominal Bayar
            $totalBayar = $pembayaran->total_bayar; // Ambil total dari kolom total_bayar
            $jumlahDiterima = $request->jumlah_bayar;
            if ($jumlahDiterima < $totalBayar) {
                DB::rollBack();
                return redirect()->back()->with('error', 'Jumlah uang yang dibayarkan kurang dari total tagihan!');
            }
            $kembalian = $jumlahDiterima - $totalBayar;
            // 4. Update Record Header Pembayaran
            db::table('pembayarans')->where('id', $kode_pembayaran)->update([
                'metode_pembayaran' => $request->metode_pembayaran,
                'jumlah_bayar'      => $jumlahDiterima,
                'kembalian'         => $kembalian,
                'status_tagihan'    => 'LUNAS',
                'status'    => 'lunas',
                // 'kasir_id'          => auth()->id(),
                'tgl_dibayar'       => now(),
            ]);;
            // 5. Update Status Kunjungan (opsional jika ada flag di tabel kunjungans)
            if ($pembayaran->kunjungan) {
                $pembayaran->kunjungan->update([
                    'status_pembayaran' => 'LUNAS'
                ]);
            }

            // 6. Update Status Item di Detail Billing
            DB::table('billing_details')
                ->where('pembayaran_id', $pembayaran->id)
                ->update([
                    'updated_at' => now(),
                    'status_tagihan' => 'sudah dibayar',
                ]);
            $id_kunjungan = $pembayaran->kunjungan_id;
            $get_billing_detail = db::table('billing_details')->where('pembayaran_id', $pembayaran->id)->where('jenis_item', 'obat')->get();
            if (count($get_billing_detail) > 0) {
                DB::table('kunjungans')
                    ->where('id', $id_kunjungan)
                    ->update([
                        'status_penyerahan_obat' => 'belum diserahkan',
                    ]);
            }
            DB::commit();

            return redirect()->back()->with('success', 'Pembayaran berhasil diproses! Kembalian: Rp ' . number_format($kembalian, 0, ',', '.'));
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal memproses pembayaran: ' . $e->getMessage());
        }
    }
    public function cetakResep($kunjungan_id)
    {
        $kunjungan = Kunjungan::with(['pasien', 'poli'])->findOrFail($kunjungan_id);
        $obats = billing_details::where('kunjungan_id', $kunjungan_id)
            ->where('status_tagihan', 'lunas')
            ->whereNotNull('obat_id')
            ->join('master_obats', 'billing_details.obat_id', '=', 'master_obats.id')
            ->select('billing_details.*', 'master_obats.nama_obat', 'master_obats.satuan')
            ->get();

        return view('KasirFarmasi.cetak_resep', compact('kunjungan', 'obats'));
    }
    public function serahkanObat($kunjungan_id)
    {
        try {
            $kunjungan = Kunjungan::with(['pasien'])->findOrFail($kunjungan_id);
            $obats = billing_details::where('kunjungan_id', $kunjungan_id)
                ->where('status_tagihan', 'sudah dibayar')
                ->whereNotNull('obat_id')
                ->get();

            $dispenseResults = [];

            foreach ($obats as $itemObat) {
                // Kirim per item obat ke service SATUSEHAT
                $res = $this->ssService->sendMedicationDispense($kunjungan, $itemObat);
                if (isset($res['status']) && $res['status'] === true && !empty($res['id'])) {
                    DB::table('billing_details')
                        ->where('id', $itemObat->id)
                        ->update([
                            'satusehat_medication_dispense_id' => $res['id'],
                            'updated_at'                       => now()
                        ]);
                }
            }

            // 2. Update status penyerahan di database lokal
            $encounterRes = $this->ssService->updateEncounterFinished($kunjungan);

            // 2. Cek apakah update Encounter di SATUSEHAT berhasil
            $isSatusehatSuccess = isset($encounterRes['status']) && $encounterRes['status'] === true;

            // 3. Update data kunjungan di database lokal
            $kunjungan->update([
                'status_penyerahan_obat' => 'diserahkan',
                'waktu_penyerahan_obat'  => now(),
                'petugas_penyerah_id'    => auth()->id(), // Mengisi ID user/petugas penyerah
                'status_kunjungan'       => 'selesai',
                'satusehat_status'       => $isSatusehatSuccess ? 'finished' : 'gagal_finished'
            ]);
            return response()->json([
                'success' => true,
                'message' => 'Obat berhasil diserahkan dan data terkirim ke SATUSEHAT.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
    public function detailPembayaran(Request $request, $id)
    {
        $billingDetails = DB::table('billing_details as a')
            ->select([
                'b.nama_obat',
                'c.nama_tindakan',
                'a.qty',
                'a.jenis_item',
                'a.harga',
                'a.subtotal',
                'a.status_tagihan',
                'd.status_tagihan as sttsheader',
                'd.no_transaksi',
                'd.id as pembayaran_id'
            ])
            ->leftJoin('master_obats as b', 'a.obat_id', '=', 'b.id')
            ->leftJoin('master_tarifs as c', 'a.tarif_id', '=', 'c.id')
            ->leftJoin('pembayarans as d', 'a.pembayaran_id', '=', 'd.id')

            ->where('a.pembayaran_id', $id) // atau menggunakan variabel $pembayaranId
            ->get();
        return view('KasirFarmasi.detailbilling', compact([
            'billingDetails'
        ]));
    }
    public function cetakKwitansi($id)
    {
        $kunjungan = Kunjungan::with([
            'pasien',
            'poli',
            'pembayaran',
            'billing_details'
        ])->findOrFail($id);

        return view('KasirFarmasi.cetak_kwitansi', compact('kunjungan'));
    }
    public function cetakPembayaran($id)
    {
        $billingDetails = DB::table('billing_details as a')
            ->select([
                'a.pembayaran_id',
                'a.qty',
                'a.jenis_item',
                'a.harga',
                'a.subtotal',
                'a.status_tagihan',
                'b.nama_obat',
                'c.nama_tindakan',
                'd.no_transaksi',
                'd.metode_pembayaran',
                'd.created_at as tgl_transaksi'
            ])
            ->leftJoin('master_obats as b', 'a.obat_id', '=', 'b.id')
            ->leftJoin('master_tarifs as c', 'a.tarif_id', '=', 'c.id')
            ->leftJoin('pembayarans as d', 'a.pembayaran_id', '=', 'd.id')
            ->where('a.pembayaran_id', $id)
            ->get();

        // Jika data tidak ditemukan
        if ($billingDetails->isEmpty()) {
            abort(404, 'Data pembayaran tidak ditemukan.');
        }

        // 2. Ambil header informasi pembayaran/pasien utama
        $header = $billingDetails->first();
        $mt_client = db::table('mt_client')->where('id',auth()->user()->client_id)->first();
        return view('KasirFarmasi.cetak_nota', compact('billingDetails', 'header','mt_client'));
    }
}
