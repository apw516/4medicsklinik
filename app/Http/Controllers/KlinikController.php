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

class KlinikController extends Controller
{
    protected $ssService;
    public function __construct(SatuSehatService $ssService)
    {
        $this->ssService = $ssService;
    }
    public function indexdokter()
    {
        $menu = 'indexdatapasienklinik';
        $listProvinsi = Provinsi::orderBy('name', 'asc')->get();
        return view('Dokter.indexdatapasien', compact([
            'menu',
            'listProvinsi'
        ]));
    }
    public function getTabelPasien(Request $request)
    {
        $tglAwal  = $request->tgl_awal;
        $tglAkhir = $request->tgl_akhir;
        $kunjungans = Kunjungan::whereDate('tgl_masuk', '>=', $tglAwal)
            ->whereDate('tgl_masuk', '<=', $tglAkhir)
            ->where('status_kunjungan', '!=', 'BATAL')
            ->where('client_id', '=', auth()->user()->client_id)
            ->with(['pasien', 'erm.dokter', 'dokter'])
            ->latest('id')
            ->get()
            ->unique('pasien_id'); // <--- Mencegah Pasien Tampil Dobel
        return view('Dokter.TabelPasien', compact('kunjungans'));
    }
    public function getFormErm($kunjungan_id)
    {
        $kunjungan = Kunjungan::with(['dokter', 'poli'])
            ->where('id', $kunjungan_id)
            ->where('status_kunjungan', '!=', 'BATAL')
            ->latest()
            ->first();

        $pasien = Pasien::findOrFail($kunjungan->pasien_id);
        $masterTarifTindakan = DB::table('master_tarifs')
            ->select('id', 'nama_tindakan', 'harga', 'icd9_code', 'icd9_display')
            ->where('is_active', true) // opsional, jika ada status aktif
            ->where('client_id', auth()->user()->client_id) // opsional, jika ada status aktif
            ->get();
        $masterObat = DB::table('master_obats')->where('is_active', true)->get();
        // Ambil data ERM jika pasien ini sudah memiliki rekam medis pada kunjungan tersebut
        $erm = null;
        if ($kunjungan) {
            $erm = DB::table('erm_records')
                ->where('kunjungan_id', $kunjungan_id)
                ->first();
        }

        $dokters = Dokter::where('is_active', true)->get();
        $billingDetails = DB::table('billing_details')
            ->Join('master_tarifs', 'billing_details.tarif_id', '=', 'master_tarifs.id')
            ->select('billing_details.*', 'master_tarifs.nama_tindakan', 'master_tarifs.icd9_code')
            ->where('billing_details.kunjungan_id', $kunjungan_id)
            ->get();

        $resepDetails = DB::table('billing_details')
            ->Join('master_obats', 'billing_details.obat_id', '=', 'master_obats.id')
            ->select(
                'billing_details.*',
                'master_obats.nama_obat',
                'master_obats.kfa_code'
            )
            ->where('billing_details.kunjungan_id', $kunjungan->id)
            ->get();


        $pasien2 = Pasien::with([
            'riwayatKunjungan' => function ($query) {
                $query->where('status_kunjungan', '!=', 'BATAL') // Filter status kunjungan tidak sama dengan BATAL
                    ->with([
                        'poli',        // Relasi ke tabel Poli / Lokasi
                        'erm',         // Relasi ke tabel ERM (untuk ICD-10)
                        'pembayaran',  // Relasi ke tabel Pembayaran / Billing
                        'dokter'       // Relasi ke tabel Dokter
                    ]);
            }
        ])->findOrFail($kunjungan->pasien_id);
        return view('Dokter.FormInput', compact('pasien', 'kunjungan', 'erm', 'dokters', 'masterTarifTindakan', 'masterObat', 'billingDetails', 'resepDetails', 'pasien2'));
    }
    public function storeErm(Request $request)
    {
        // 1. Validasi Input
        $request->validate([
            'pasien_id'      => 'required',
            'keluhan_utama'  => 'required|string',
            'icd10_code'     => 'required|string',
            'icd10_display'  => 'required|string',
            'dokter_id'      => 'required',
            'kategori_alergi' => 'nullable|string',
            'detail_alergi'  => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            $dataToSave = [
                'pasien_id'         => $request->pasien_id,
                'kunjungan_id'      => $request->kunjungan_id ?? null,
                'dokter_id'         => $request->dokter_id,
                'keluhan_utama'     => $request->keluhan_utama,
                'riwayat_penyakit'  => $request->riwayat_penyakit,
                'kategori_alergi'  => $request->kategori_alergi ?? null,
                'detail_alergi'    => $request->detail_alergi ?? null,
                'td_sistole'        => $request->td_sistole,
                'td_diastole'       => $request->td_diastole,
                'nadi'              => $request->nadi,
                'suhu'              => $request->suhu,
                'spo2'              => $request->spo2,
                'respirasi'         => $request->respirasi,
                'pemeriksaan_fisik' => $request->pemeriksaan_fisik,
                'icd10_code'        => strtoupper(trim($request->icd10_code)),
                'icd10_display'     => $request->icd10_display,
                'diagnosa_catatan'  => $request->diagnosa_catatan,
                'tindakan_edukasi'  => $request->tindakan_edukasi,
                'resep_obat'        => $request->resep_obat,
                'updated_at'        => now(),
                'client_id'     => auth()->user()->client_id

            ];
            // 2. Cek apakah ERM sudah ada berdasarkan erm_id / kunjungan_id
            $existingErm = null;
            if ($request->filled('erm_id')) {
                $existingErm = DB::table('erm_records')->where('id', $request->erm_id)->first();
            } elseif ($request->filled('kunjungan_id')) {
                $existingErm = DB::table('erm_records')->where('kunjungan_id', $request->kunjungan_id)->first();
            }

            if ($existingErm) {
                // UPDATE DATA
                DB::table('erm_records')
                    ->where('id', $existingErm->id)
                    ->update($dataToSave);

                $ermId = $existingErm->id;
                $actionMessage = 'Data ERM berhasil diperbarui';
            } else {
                // CREATE DATA BARU
                $dataToSave['created_at'] = now();
                $ermId = DB::table('erm_records')->insertGetId($dataToSave);
                $actionMessage = 'Data ERM berhasil disimpan';
            }

            // Ambil record ERM yang baru dibuat / diperbarui
            $ermData = DB::table('erm_records')->where('id', $ermId)->first();

            // 3. Persiapan & Bridging SATUSEHAT
            $pasien = Pasien::find($request->pasien_id);
            $satusehatSuccess = false;
            $satusehatLog = [];
            if ($pasien && !empty($pasien->ihs_number)) {
                $kunjungan = Kunjungan::with(['pasien', 'dokter', 'poli'])->find($request->kunjungan_id);
                if ($kunjungan) {
                    // Attach data ERM ke objek kunjungan
                    $kunjungan->erm = $ermData;

                    // Inisialisasi status success di awal sebelum tahap bridging dijalankan
                    $satusehatSuccess = true;

                    // TAHAP 1: Update Status Encounter ke "in-progress"
                    $ssResponse_1 = $this->ssService->updateInprogress($kunjungan);
                    $satusehatLog['encounter_inprogress'] = $ssResponse_1;
                    // TAHAP 2: Kirim Condition (Diagnosis ICD-10)
                    $ssResponse_2 = $this->ssService->conditionDiagnosis($kunjungan, $ermData);
                    $satusehatLog['condition_diagnosis'] = $ssResponse_2;

                    // TAHAP 3: Kirim Observation (TTV)
                    $ssResponse_3 = $this->ssService->updateTTV($kunjungan, $ermData);
                    $satusehatLog['observation_ttv'] = $ssResponse_3;

                    // TAHAP 4: Kirim AllergyIntolerance (Jika Alergi Diisi)
                    if (!empty($request->detail_alergi) && !empty($request->kategori_alergi)) {
                        $ssResponse_4 = $this->ssService->sendAllergyIntolerance($kunjungan, $ermData);
                        $satusehatLog['allergy_intolerance'] = $ssResponse_4;

                        if (isset($ssResponse_4['id'])) {
                            DB::table('erm_records')
                                ->where('id', $ermId)
                                ->update(['satusehat_allergy_id' => $ssResponse_4['id']]);
                        }
                    }

                    $totalTindakan = 0;
                    $totalResep = 0;

                    if ($request->has('tindakan') && is_array($request->tindakan)) {
                        foreach ($request->tindakan as $item) {
                            if (!empty($item['tarif_id'])) {
                                $totalTindakan += $item['subtotal'];
                            }
                        }
                    }

                    if ($request->has('resep') && is_array($request->resep)) {
                        foreach ($request->resep as $item) {
                            if (!empty($item['obat_id'])) {
                                $isPaket = isset($item['is_paket']) && $item['is_paket'] == '1';
                                $totalResep += $isPaket ? 0 : $item['subtotal'];
                            }
                        }
                    }
                    // Cek apakah ada minimal 1 data yang valid untuk diproses
                    $hasTindakan = $request->has('tindakan') && count(array_filter($request->tindakan, fn($i) => !empty($i['tarif_id']))) > 0;
                    $hasResep = $request->has('resep') && count(array_filter($request->resep, fn($i) => !empty($i['obat_id']))) > 0;
                    if ($hasTindakan || $hasResep) {
                        // 1. Insert Header ke Tabel Pembayarans
                        $pembayaranId = DB::table('pembayarans')->insertGetId([
                            'kunjungan_id'   => $request->kunjungan_id,
                            'no_transaksi'   => 'TRX-' . date('YmdHis') . '-' . rand(100, 999), // Contoh generator No. Transaksi
                            'total_tindakan' => $totalTindakan,
                            'total_resep'    => $totalResep,
                            'total_bayar'    => $totalTindakan + $totalResep,
                            'status_tagihan' => 'BELUM LUNAS', // Status awal sebelum dibayar di kasir
                            'created_at'     => now(),
                            'updated_at'     => now(),
                            'client_id'     => auth()->user()->client_id
                        ]);
                        // 2. Process Array Tindakan
                        if ($hasTindakan) {
                            foreach ($request->tindakan as $item) {
                                if (!empty($item['tarif_id'])) {
                                    // Insert ke Tabel Detail Billing / Detail Pembayarans
                                    $billingDetailId = DB::table('billing_details')->insertGetId([
                                        'pembayaran_id' => $pembayaranId, // Foreign Key ke tabel pembayarans
                                        'kunjungan_id'  => $request->kunjungan_id,
                                        'tarif_id'      => $item['tarif_id'],
                                        'jenis_item'    => 'tindakan',
                                        'harga'         => $item['harga'],
                                        'qty'           => $item['qty'],
                                        'subtotal'      => $item['subtotal'],
                                        'created_at'    => now(),
                                        'updated_at'    => now(),
                                    ]);

                                    // Bridging SATUSEHAT Procedure
                                    if (!empty($item['icd9_code']) && $satusehatSuccess) {
                                        $procResponse = $this->ssService->sendProcedure($kunjungan, $item);
                                        // dd($procResponse);
                                        $procedureId = $procResponse['procedure_id'] ?? $procResponse['id'] ?? null;

                                        if (isset($procResponse['status']) && $procResponse['status'] === true && !empty($procedureId)) {
                                            DB::table('billing_details')
                                                ->where('id', $billingDetailId)
                                                ->update([
                                                    'Procedure_ID' => $procedureId,
                                                    'updated_at'   => now()
                                                ]);
                                        }

                                        $satusehatLog['procedure_icd9'][] = $procResponse;
                                    }
                                }
                            }
                        }
                        // 3. Process Array Resep
                        if ($hasResep) {
                            foreach ($request->resep as $item) {
                                if (!empty($item['obat_id'])) {
                                    $isPaket  = isset($item['is_paket']) && $item['is_paket'] == '1';
                                    $harga    = $isPaket ? 0 : $item['harga'];
                                    $subtotal = $isPaket ? 0 : $item['subtotal'];

                                    $satusehatMedId        = null;
                                    $satusehatMedRequestId = null;

                                    if ($satusehatSuccess) {
                                        // DD($kunjungan);
                                        $medResponse = $this->ssService->sendMedicationRequest($kunjungan, $item);
                                        $satusehatLog['medication_request'][] = $medResponse;

                                        if (isset($medResponse['status']) && $medResponse['status'] === true) {
                                            $satusehatMedId        = $medResponse['medication_id'] ?? null;
                                            $satusehatMedRequestId = $medResponse['id'] ?? null;
                                        }
                                    }
                                    // Insert ke Tabel Detail Billing / Detail Pembayarans
                                    DB::table('billing_details')->insert([
                                        'pembayaran_id'                   => $pembayaranId, // Foreign Key ke tabel pembayarans
                                        'kunjungan_id'                    => $request->kunjungan_id,
                                        'tarif_id'                        => null,
                                        'obat_id'                         => $item['obat_id'],
                                        'jenis_item'                      => 'obat',
                                        'harga'                           => $harga,
                                        'qty'                             => $item['qty'],
                                        'subtotal'                        => $subtotal,
                                        'satusehat_medication_id'         => $satusehatMedId,
                                        'satusehat_medication_request_id' => $satusehatMedRequestId,
                                        'satusehat_sent_at'               => $satusehatMedRequestId ? now() : null,
                                        'created_at'                      => $satusehatMedcreated_at ?? now(),
                                        'updated_at'                      => now(),
                                    ]);

                                    // Potong Stok
                                    DB::table('master_obats')->where('id', $item['obat_id'])->decrement('stok', $item['qty']);
                                    DB::table('kunjungans')
                                        ->where('id', $request->kunjungan_id)
                                        ->update(['status_penyerahan_obat' => 'belum diserahkan']);
                                }
                            }
                        }
                        $STATUSBAYARKUNJUNGAN = DB::table('billing_details')->where('kunjungan_id', $request->kunjungan_id)->where('status_tagihan', 'belum dibayar')->get();
                        if (count($STATUSBAYARKUNJUNGAN) > 0) {
                            DB::table('kunjungans')
                                ->where('id', $request->kunjungan_id)
                                ->update(['status_pembayaran' => 'belum dibayar']);
                        }
                    }
                }
            }

            DB::commit();

            return response()->json([
                'status'    => true,
                'message'   => $actionMessage . ' & Bridging SATUSEHAT diproses.',
                'erm_id'    => $ermId,
                'satusehat' => [
                    'sent'    => $satusehatSuccess,
                    'details' => $satusehatLog
                ]
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error Simpan/Update ERM & Bridging SATUSEHAT: ' . $e->getMessage());

            return response()->json([
                'status'  => false,
                'message' => 'Gagal memproses data ERM: ' . $e->getMessage()
            ], 500);
        }
    }
    public function searchICD10(Request $request)
    {
        $search = $request->get('q');

        if (empty($search)) {
            return response()->json([]);
        }

        // Sesuaikan nama tabel 'icd10s' dan nama kolom di database Anda
        $data = DB::table('mt_icd10')
            ->select('diag', 'nama') // ganti 'code' & 'display' sesuai struktur tabel
            ->where('diag', 'LIKE', "%{$search}%")
            ->orWhere('nama', 'LIKE', "%{$search}%")
            ->limit(20)
            ->get();

        return response()->json($data);
    }
    public function sendEncounter($idkunjungan)
    {
        try {
            // 1. Cari data Kunjungan beserta relasinya
            $kunjungan = Kunjungan::with(['pasien', 'dokter', 'poli'])->find($idkunjungan);
            if (!$kunjungan) {
                return response()->json([
                    'status'  => false,
                    'message' => 'Data Kunjungan tidak ditemukan.'
                ], 404);
            }

            // 2. Cek apakah IHS Pasien tersedia
            $pasien = $kunjungan->pasien;
            if (empty($pasien->ihs_number)) {
                return response()->json([
                    'status'  => false,
                    'message' => 'IHS Pasien belum terdaftar/kosong. Kirim Encounter dibatalkan.'
                ], 400);
            }
            $today = Carbon::now()->format('Ymd');
            $countToday = Kunjungan::whereDate('created_at', Carbon::today())->count() + 1;
            $noRegistrasiBaru = 'REG-' . $today . '-' . str_pad($countToday, 4, '0', STR_PAD_LEFT);

            // Update no_registrasi di DB lokal dan instance objek
            $kunjungan->update(['no_registrasi' => $noRegistrasiBaru]);
            $kunjungan->no_registrasi = $noRegistrasiBaru;

            // 3. Panggil service storeEncounter
            $ssResponse = $this->ssService->storeEncounter($kunjungan);

            // 4. Jika berhasil mendapat ID Encounter, update database lokal
            if (isset($ssResponse['status']) && $ssResponse['status'] === true && !empty($ssResponse['id'])) {
                $kunjungan->update([
                    'satusehat_encounter_id' => $ssResponse['id']
                ]);

                return response()->json([
                    'status'       => true,
                    'message'      => 'Encounter berhasil dikirim ke SATUSEHAT.',
                    'encounter_id' => $ssResponse['id'],
                    'no_registrasi' => $noRegistrasiBaru
                ], 200);
            }

            // 5. Response jika service SATUSEHAT mengembalikan error
            return response()->json([
                'status'  => false,
                'message' => $ssResponse['message'] ?? 'Gagal mengirim Encounter ke SATUSEHAT.',
                'error'   => $ssResponse['error'] ?? null
            ], 400);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Exception Send Encounter: ' . $e->getMessage());

            return response()->json([
                'status'  => false,
                'message' => 'Terjadi kesalahan sistem: ' . $e->getMessage()
            ], 500);
        }
    }
    public function batalTindakan(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:billing_details,id',
        ]);
        try {
            // Load relasi ke header billing/tagihan untuk mengecek status_tagihan
            $detail = billing_details::findOrFail($request->id);
            if ($detail->status_tagihan && strtolower($detail->status_tagihan) === 'sudah dibayar') {
                return response()->json([
                    'success' => false,
                    'message' => 'Tindakan tidak dapat dibatalkan karena tagihan sudah LUNAS. Batalkan transaksi melalui menu Kasir terlebih dahulu.'
                ], 422);
            }
            $total_layanan_retur = $detail->subtotal;
            $kunjungan_id = $detail->kunjungan_id;
            $detail_2 = Pembayaran::findOrFail($detail->pembayaran_id);
            $total_tindakan_baru = $detail_2->total_tindakan - $total_layanan_retur;
            $total_bayar_baru = $detail_2->total_bayar - $total_layanan_retur;
            Pembayaran::where('id', $detail->pembayaran_id)->update([
                'total_tindakan' => $total_tindakan_baru,
                'total_bayar' => $total_bayar_baru,
            ]);

            $detail->delete();
            if ($total_bayar_baru == 0) {
                $detail_2->delete();
            }
            $STATUSBAYARKUNJUNGAN = DB::table('billing_details')->where('kunjungan_id', $kunjungan_id)->where('status_tagihan', 'belum dibayar')->get();
            if (count($STATUSBAYARKUNJUNGAN) > 0) {
                DB::table('kunjungans')
                    ->where('id', $kunjungan_id)
                    ->update(['status_pembayaran' => 'belum dibayar']);
            } else {
                DB::table('kunjungans')
                    ->where('id', $kunjungan_id)
                    ->update(['status_pembayaran' => 'LUNAS']);
            }
            return response()->json([
                'success' => true,
                'message' => 'Tindakan berhasil dibatalkan dari tagihan.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }
    public function returObat(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:resep_details,id', // Sesuaikan nama tabel resep detail
        ]);

        DB::beginTransaction();
        try {
            // Load detail resep beserta header billing/pendaftaran
            $resepDetail = billing_details::findOrFail($request->id);
            // 1. Validasi jika tagihan sudah LUNAS
            if (optional($resepDetail->resep->billing)->status_tagihan === 'sudah dibayar') {
                return response()->json([
                    'success' => false,
                    'message' => 'Obat tidak dapat diretur karena tagihan sudah LUNAS. Silakan lakukan retur dari menu Kasir.'
                ], 422);
            }
            // 2. Kembalikan stok obat jika terhubung ke master obat
            if ($resepDetail->obat_id && $resepDetail->obat) {
                $resepDetail->obat->increment('stok', $resepDetail->qty);
            }
            $total_layanan_retur = $resepDetail->subtotal;
            $kunjungan_id = $resepDetail->kunjungan_id;
            $detail_2 = Pembayaran::findOrFail($resepDetail->pembayaran_id);
            $total_resep_baru = $detail_2->total_resep - $total_layanan_retur;
            $total_bayar_baru = $detail_2->total_bayar - $total_layanan_retur;
            Pembayaran::where('id', $resepDetail->pembayaran_id)->update([
                'total_resep' => $total_resep_baru,
                'total_bayar' => $total_bayar_baru,
            ]);
            // 3. Hapus baris resep detail
            $resepDetail->delete();
            if ($total_bayar_baru == 0) {
                $detail_2->delete();
            }
            $STATUSBAYARKUNJUNGAN = DB::table('billing_details')->where('kunjungan_id', $kunjungan_id)->where('status_tagihan', 'belum dibayar')->get();
            if (count($STATUSBAYARKUNJUNGAN) > 0) {
                DB::table('kunjungans')
                    ->where('id', $kunjungan_id)
                    ->update(['status_pembayaran' => 'belum dibayar','status_penyerahan_obat' => 'belum diserahkan']);
            } else {
                DB::table('kunjungans')
                    ->where('id', $kunjungan_id)
                    ->update(['status_pembayaran' => 'LUNAS','status_penyerahan_obat' => 'diserahkan']);
            }
            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Obat berhasil diretur dan stok telah dikembalikan.'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }
    public function getDetailKunjungan($id)
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
}
