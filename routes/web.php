<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\KasirFarmasiController;
use App\Http\Controllers\KlinikController;
use App\Http\Controllers\KonfigurasiController;
use App\Http\Controllers\PendaftaranController;
use Illuminate\Support\Facades\Route;
use App\Helpers\LicenseHelper;

Route::get('/app-activation', function (\Illuminate\Http\Request $request) {
    $devKey = $request->query('key');
    $expired = $request->query('expired'); // Format YYYY-MM-DD

    // Kunci Pengaman agar Klinik tidak bisa tebak
    if ($devKey !== 'KunciSuperDev2026') {
        abort(404); // Pura-pura halaman tidak ditemukan
    }

    if (!$expired) {
        return "Masukkan tanggal expired! Contoh: ?key=KunciSuperDev2026&expired=2026-12-31";
    }

    $res = LicenseHelper::generateLicenseFile($expired);
    return "<h3>BERHASIL: " . $res . "</h3>";
});
Route::get('/', [AuthController::class, 'index'])->middleware('guest')->name('login');
Route::get('/register', [AuthController::class, 'registerindex'])->middleware('guest')->name('registrasi');
Route::post('/registrasi', [AuthController::class, 'store'])->name('registrasi.store');
Route::post('/login', [AuthController::class, 'authenticate'])->name('login.post');
Route::get('/logout', [AuthController::class, 'logout'])->name('logout');
Route::get('/indexdetailakun', [AuthController::class, 'indexdetailakun'])->name('indexdetailakun');
// Route::middleware(['auth'])->group(function () {
// Route::get('/profile', [AuthController::class, 'index'])->name('profile.index');
Route::put('/profile/update-password', [AuthController::class, 'updatePassword'])->name('profile.update-password');
// });

// Route Master Obat
Route::post('/master-obat', [DashboardController::class, 'storeobat'])->name('master-obat.store');
Route::put('/master-obat/{id}', [DashboardController::class, 'update'])->name('master-obat.update');
Route::delete('/master-obat/{id}', [DashboardController::class, 'destroy'])->name('master-obat.destroy');

// Route AJAX Select2 KFA Search
Route::get('/kfa/search', [DashboardController::class, 'searchKfa'])->name('kfa.search');
Route::put('/master-tarif/{id}', [DashboardController::class, 'updatetarif'])->name('master-tarif.update');
Route::middleware(['auth'])->group(function () {
    Route::post('/master-tarif', [DashboardController::class, 'storetarif'])->name('master-tarif.store');
    Route::delete('/master-tarif/{id}', [DashboardController::class, 'destroytarif'])->name('master-tarif.destroy');
    Route::delete('/master-unit/{id}', [DashboardController::class, 'destroyunit'])->name('master-unit.destroy');
    Route::delete('/master-org/{id}', [DashboardController::class, 'destroyorg'])->name('master-organisasi.destroy');
});
Route::get('/indexmasterunit', [DashboardController::class, 'indexmasterunit'])->middleware('auth')->name('indexmasterunit');
Route::get('/icd9/search', [DashboardController::class, 'searchIcd9'])->name('icd9.search');
Route::get('/indexmastertarif', [DashboardController::class, 'indexmastertarif'])->middleware('auth')->name('indexmastertarif');
Route::get('/indexmasterobat', [DashboardController::class, 'indexmasterobat'])->middleware('auth')->name('indexmasterobat');
Route::get('/dashboard', [DashboardController::class, 'index'])->middleware('auth')->name('dashboard');

Route::get('/indexmasteruser', [KonfigurasiController::class, 'indexmasteruser'])->middleware('auth')->name('indexmasteruser');
Route::get('/indexmasterpractitioner', [KonfigurasiController::class, 'indexmasterpractitioner'])->middleware('auth')->name('indexmasterpractitioner');
Route::get('/indexorganization', [KonfigurasiController::class, 'indexorganization'])->middleware('auth')->name('indexorganization');
Route::post('/simpanorganization', [KonfigurasiController::class, 'simpanorganization'])->middleware('auth')->name('organization.store');
Route::post('/simpanlocation', [KonfigurasiController::class, 'simpanlocation'])->middleware('auth')->name('location.store');
Route::post('/simpandokter', [KonfigurasiController::class, 'simpanPractitioner'])->middleware('auth')->name('practitioner.store');

Route::prefix('satusehat/practitioner')->as('satusehat.practitioner.')->group(function () {
    Route::put('/{id}', [KonfigurasiController::class, 'updatedokter'])->name('update');
    Route::post('/{id}', [KonfigurasiController::class, 'destroydokter'])->name('destroy');
});

Route::prefix('user-practitioner')->as('user.practitioner.')->group(function () {
    Route::put('/{id}', [KonfigurasiController::class, 'updateuser'])->name('update');
    Route::delete('/{id}', [KonfigurasiController::class, 'destroyuser'])->name('destroy');
});
Route::post('billing/batal_tindakan', [KlinikController::class, 'batalTindakan'])->name('billing.batal_tindakan');
Route::post('billing/retur_obat', [KlinikController::class, 'batalTindakan'])->name('resep.retur_obat');
Route::post('/satusehat/encounter/send/{id}', [KlinikController::class, 'sendEncounter'])->name('satusehat.encounter.send');
Route::get('/indexdokter', [KlinikController::class, 'indexdokter'])->middleware('auth')->name('indexdokter');
Route::get('/pasien.getTabelPasien', [KlinikController::class, 'getTabelPasien'])->middleware('auth')->name('pasien.getTabelPasiendokter');
// Route Form ERM Klinik
Route::get('/erm/form-input/{pasien_id}', [KlinikController::class, 'getFormErm']);
// Route Detail Kunjungan Pasien
// Route::get('/kunjungan/detail/{pasien_id}', [KlinikController::class, 'getDetailKunjungan']);

Route::get('/kunjungan/detail/{pasien_id}', [KlinikController::class, 'getDetailKunjungan'])->name('kunjungan.detail');
// Route Riwayat Kunjungan Pasien
Route::get('/kunjungan/riwayat/{pasien_id}', [KlinikController::class, 'getRiwayatKunjungan']);
Route::post('/erm/simpan', [KlinikController::class, 'storeErm']);
Route::get('/icd10/search', [KlinikController::class, 'searchICD10'])->name('icd10.search');
Route::post('/pasien/update', [PendaftaranController::class, 'updatepasien'])->name('pasien.update');


Route::get('/indexriwayatkunjungan', [PendaftaranController::class, 'indexriwayatkunjungan'])->middleware('auth')->name('indexriwayatkunjungan');
Route::get('/indexmasterpasien', [PendaftaranController::class, 'indexmasterpasien'])->middleware('auth')->name('indexmasterpasien');
Route::get('/pasien/get-tabel-pasien', [PendaftaranController::class, 'getTabelPasien'])->name('pasien.getTabelPasien');
Route::get('/pasien/detail-kunjungan/{id}', [PendaftaranController::class, 'detailKunjungan'])->name('pasien.detailKunjungan');
Route::get('/pasien/batalkunjungan/{id}', [PendaftaranController::class, 'batalkunjungan'])->name('pasien.batalkunjungan');
Route::get('/pasien/batalisierm/{id}', [PendaftaranController::class, 'batalisierm'])->name('pasien.batalisierm');
Route::get('/cekpasien', [PendaftaranController::class, 'cekPasien'])->middleware('auth')->name('cekPasien');
Route::get('/provinsi', [PendaftaranController::class, 'getProvinsi'])->name('getprov');
Route::get('/kabkota', [PendaftaranController::class, 'getKabKota'])->name('getKabKota');
Route::get('/kecamatan', [PendaftaranController::class, 'getKecamatan'])->name('getKec');
Route::get('/kelurahan', [PendaftaranController::class, 'getKelurahan'])->name('getKelurahan');
Route::get('/kelurahan', [PendaftaranController::class, 'getKelurahan'])->name('getKelurahan');
Route::get('/get-kabkota-by-prov', [PendaftaranController::class, 'getKabKotaByProv'])->name('getKabKotaByProv');
Route::get('/get-kecamatan-by-kab', [PendaftaranController::class, 'getKecamatanByKab'])->name('getKecamatanByKab');
Route::get('/get-kelurahan-by-kec', [PendaftaranController::class, 'getKelurahanByKec'])->name('getKelurahanByKec');
Route::post('/pasien.index', [PendaftaranController::class, 'storePasien'])->name('pasien.store');
Route::get('/caripasienpasien', [PendaftaranController::class, 'caripasien'])->name('caripasien');
Route::post('/ambil_form_pendaftaran', [PendaftaranController::class, 'formpendaftaran'])->name('ambil_form_pendaftaran');
Route::post('/pendaftaran/store', [PendaftaranController::class, 'storependaftaran'])->name('pendaftaran.store');
Route::post('/ambil_form_editpasien', [PendaftaranController::class, 'ambilFormEditPasien'])->name('ambil_form_editpasien');


// Route Halaman Utama Kasir
Route::get('/indexfarmasi', [KasirFarmasiController::class, 'indexfarmasi'])->middleware('auth')->name('indexfarmasi');
Route::get('/indexriwayatpembayaran', [KasirFarmasiController::class, 'indexriwayatpembayaran'])->middleware('auth')->name('indexriwayatpembayaran');
Route::get('/indexkasir', [KasirFarmasiController::class, 'indexkasir'])->middleware('auth')->name('indexkasir');
Route::get('/kasir', [KasirFarmasiController::class, 'index'])->middleware('auth')->name('kasir.index');
// Route AJAX Detail Tagihan
Route::get('/kasir/detail/{id}/{id2}', [KasirFarmasiController::class, 'getDetailTagihan'])->middleware('auth')->name('kasir.detail');
// Route POST Proses Pembayaran
Route::post('/kasir/bayar', [KasirFarmasiController::class, 'prosesPembayaran'])->middleware('auth')->name('kasir.bayar');
Route::get('/farmasi/detail-obat/{id}', [KasirFarmasiController::class, 'getDetailObatFarmasi'])->name('farmasi.detail-obat');
Route::get('/farmasi/cetak-resep/{kunjungan_id}', [KasirFarmasiController::class, 'cetakResep']);
Route::post('/farmasi/serahkan-obat/{kunjungan_id}', [KasirFarmasiController::class, 'serahkanObat']);
Route::post('/ambil_riwayat_pembayaran', [KasirFarmasiController::class, 'ambil_riwayat_pembayaran'])->name('ambil_riwayat_pembayaran');

Route::get('/detail-pembayaran/{id}', [KasirFarmasiController::class, 'detailPembayaran'])->name('detail-pembayaran');    // Route untuk mencetak nota/struk pembayaran
Route::get('/cetak-pembayaran/{id}', [KasirFarmasiController::class, 'cetakPembayaran'])->name('cetak-pembayaran');
