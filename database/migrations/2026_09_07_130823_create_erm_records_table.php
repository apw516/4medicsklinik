<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('erm_records', function (Blueprint $table) {
            $table->id();

            // Relasi Foreign Key (Sesuaikan nama tabel referensinya jika berbeda)
            $table->unsignedBigInteger('pasien_id');
            $table->unsignedBigInteger('kunjungan_id')->nullable();
            $table->unsignedBigInteger('dokter_id');

            // Anamnesis (Subjektif)
            $table->text('keluhan_utama');
            $table->text('riwayat_penyakit')->nullable();

            // Tanda-Tanda Vital & Fisik (Objektif)
            $table->integer('td_sistole')->nullable();
            $table->integer('td_diastole')->nullable();
            $table->integer('nadi')->nullable();
            $table->decimal('suhu', 4, 1)->nullable(); // Mengakomodir format misal 36.5
            $table->integer('spo2')->nullable();
            $table->integer('respirasi')->nullable();
            $table->text('pemeriksaan_fisik')->nullable();

            // Diagnosis / ICD-10 (Asesmen)
            $table->string('icd10_code', 20);
            $table->string('icd10_display');
            $table->text('diagnosa_catatan')->nullable();

            // Penatalaksanaan & Resep (Plan)
            $table->text('tindakan_edukasi')->nullable();
            $table->text('resep_obat')->nullable();

            // Optional: ID jika sudah terintegrasi SATUSEHAT
            $table->string('satusehat_condition_id')->nullable();

            $table->timestamps();

            // Pengaturan Foreign Key Constraint (opsional, bisa diaktifkan jika tabel induk sudah ada)
            // $table->foreign('pasien_id')->references('id')->on('pasiens')->onDelete('cascade');
            // $table->foreign('kunjungan_id')->references('id')->on('kunjungans')->onDelete('set null');
            // $table->foreign('dokter_id')->references('id')->on('dokters')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('erm_records');
    }
};
