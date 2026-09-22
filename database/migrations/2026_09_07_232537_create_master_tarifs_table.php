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
        Schema::create('master_tarifs', function (Blueprint $table) {
            $table->id();
            $table->string('kode_tindakan')->unique();
            $table->string('nama_tindakan');
            $table->decimal('harga', 12, 2)->default(0);
            $table->string('kategori')->default('Tindakan Medis'); // Contoh: Tindakan Medis, Laboratorium, Konsultasi
            $table->string('icd9_code')->nullable(); // Kode ICD-9-CM untuk SATUSEHAT
            $table->string('icd9_display')->nullable(); // Deskripsi standar ICD-9-CM
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('master_tarifs');
    }
};
