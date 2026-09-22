<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('pasien', function (Blueprint $table) {
            $table->id();

            // 1. Identitas Lokal SIMRS
            $table->string('no_rm', 20)->unique()->comment('Nomor Rekam Medis Utama');

            // 2. Identitas Integrasi (SATUSEHAT & BPJS)
            $table->string('nik', 16)->nullable()->unique()->index()->comment('NIK Kemendagri');
            $table->string('ihs_number', 64)->nullable()->unique()->index()->comment('Patient ID SATUSEHAT (IHS)');
            $table->string('no_bpjs', 13)->nullable()->index()->comment('Nomor Kartu BPJS Kesehatan');

            // 3. Data Diri Utama
            $table->string('nama_lengkap');
            $table->string('gelar_depan', 20)->nullable();
            $table->string('gelar_belakang', 20)->nullable();
            $table->enum('jenis_kelamin', ['L', 'P']);
            $table->string('tempat_lahir', 100);
            $table->date('tanggal_lahir');
            $table->string('golongan_darah', 5)->nullable();
            $table->enum('rhesus', ['+', '-'])->nullable();

            // 4. Profil Demografi (Standar FHIR & BPJS)
            $table->string('agama', 20)->nullable();
            $table->string('status_pernikahan', 20)->nullable()->comment('FHIR Code: S (Single), M (Married), D (Divorced), W (Widowed)');
            $table->string('pekerjaan', 100)->nullable();
            $table->string('pendidikan', 50)->nullable();
            $table->enum('kewarganegaraan', ['WNI', 'WNA'])->default('WNI');
            $table->string('bahasa', 10)->default('id');

            // 5. Alamat Sesuai Wilayah Kemendagri / SATUSEHAT
            $table->text('alamat');
            $table->string('rt', 5)->nullable();
            $table->string('rw', 5)->nullable();
            $table->string('kode_pos', 10)->nullable();
            $table->string('provinsi_code', 10)->nullable()->index();
            $table->string('kabkota_code', 10)->nullable()->index();
            $table->string('kecamatan_code', 10)->nullable()->index();
            $table->string('kelurahan_code', 10)->nullable()->index();

            // 6. Kontak Pasien
            $table->string('no_hp', 20)->nullable()->index();
            $table->string('email', 100)->nullable();

            // 7. Penanggung Jawab / Kontak Darurat (FHIR Contact)
            $table->string('nama_pj', 150)->nullable();
            $table->string('hubungan_pj', 50)->nullable();
            $table->string('no_hp_pj', 20)->nullable();

            // 8. Status & System Tracking
            $table->boolean('is_active')->default(true);
            $table->timestamp('satu_sehat_sync_at')->nullable()->comment('Waktu terakhir terhubung ke SATUSEHAT');

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pasien');
    }
};
