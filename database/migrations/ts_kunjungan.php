<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kunjungans', function (Blueprint $table) {
            // 1. PRIMARY KEY & UTAMA SIMRS
            $table->id();
            $table->string('no_registrasi', 30)->unique()->comment('Nomor Registrasi Kunjungan SIMRS (misal: REG/20260401/001)');

            // Foreign Keys Internal SIMRS
            $table->foreignId('pasien_id')->constrained('pasiens')->cascadeOnDelete();
            $table->foreignId('poli_id')->nullable()->constrained('polis')->nullOnDelete();
            $table->foreignId('dokter_id')->nullable()->constrained('dokters')->nullOnDelete();

            // 2. DETAIL & WAKTU KUNJUNGAN
            $table->enum('jenis_kunjungan', ['RAWAT_JALAN', 'RAWAT_INAP', 'IGD'])->default('RAWAT_JALAN');
            $table->dateTime('tgl_masuk');
            $table->dateTime('tgl_keluar')->nullable();
            $table->enum('cara_masuk', ['DATANG_SENDIRI', 'RUJUKAN_FKTP', 'RUJUKAN_FKRTL', 'AMBULANS', 'LAINNYA'])->default('DATANG_SENDIRI');
            $table->enum('cara_keluar', ['SEMBUH', 'PERBAIKAN', 'DIRUJUK', 'Meninggal', 'APS'])->nullable();
            $table->text('keluhan_utama')->nullable();
            $table->enum('status_kunjungan', ['ANTRIAN', 'PERIKSA', 'SELESAI', 'BATAL'])->default('ANTRIAN');

            // 3. INTEGRASI BPJS KESEHATAN (V-CLAIM / P-CARE)
            $table->enum('penjamin', ['UMUM', 'BPJS', 'ASURANSI_SWASTA', 'PERUSAHAAN'])->default('UMUM');
            $table->string('no_sep', 30)->nullable()->unique()->comment('Nomor Surat Eligibilitas Peserta (SEP) BPJS');
            $table->string('no_rujukan', 30)->nullable()->comment('Nomor Rujukan dari Faskes Perujuk');
            $table->string('no_surat_kontrol', 30)->nullable()->comment('Nomor Surat Kontrol / SKDP BPJS');
            $table->enum('jenis_pelayanan_bpjs', ['1', '2'])->nullable()->comment('1: Rawat Inap, 2: Rawat Jalan');
            $table->string('kode_poli_bpjs', 10)->nullable()->comment('Kode Poliklinik BPJS (contoh: INT, ANM, KND)');
            $table->string('kode_dokter_bpjs', 20)->nullable()->comment('Kode Dokter DPJP versi BPJS');
            $table->string('kode_faskes_perujuk', 20)->nullable()->comment('Kode Faskes/Puskesmas/Klinik Perujuk');
            $table->enum('kelas_rawat_bpjs', ['1', '2', '3'])->nullable()->comment('Kelas Hak Peserta BPJS');
            $table->string('diag_awal_bpjs', 10)->nullable()->comment('Kode ICD-10 Diagnosa Awal saat cetak SEP');
            $table->text('catatan_sep')->nullable()->comment('Catatan Tambahan SEP BPJS');

            // 4. INTEGRASI SATUSEHAT (HL7 FHIR ENCOUNTER)
            $table->string('satusehat_encounter_id', 100)->nullable()->index()->comment('UUID Response dari SATUSEHAT POST Encounter');

            // ValueSet SATUSEHAT ActCode Encounter Class:
            // AMB (ambulatory/rawat jalan), IMP (inpatient/rawat inap), EMER (emergency/IGD), HH (home health)
            $table->string('satusehat_class', 10)->default('AMB')->comment('ValueSet FHIR ActCode (AMB / IMP / EMER)');

            // Status Encounter SATUSEHAT:
            // planned | arrived | triaged | in-progress | onleave | finished | cancelled | entered-in-error
            $table->enum('satusehat_status', [
                'planned',
                'arrived',
                'triaged',
                'in-progress',
                'onleave',
                'finished',
                'cancelled',
                'entered-in-error'
            ])->default('arrived')->comment('Status sesuai standard FHIR Encounter');

            $table->string('satusehat_location_id', 100)->nullable()->comment('UUID Location ID Ruangan/Poli di SATUSEHAT');
            $table->timestamp('satusehat_synced_at')->nullable()->comment('Waktu berhasil kirim/update ke SATUSEHAT');
            $table->text('satusehat_last_error')->nullable()->comment('Log jika gagal kirim ke SATUSEHAT');

            // 5. TIMESTAMPS & AUDIT
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kunjungans');
    }
};
