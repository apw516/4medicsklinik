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
        Schema::create('master_obats', function (Blueprint $table) {
            $table->id();
            $table->string('kode_obat')->unique();
            $table->string('nama_obat');
            $table->string('kategori')->nullable(); // Contoh: Analgesik, Antibiotik, Vitamin
            $table->string('satuan')->default('Tablet'); // Contoh: Tablet, Botol, Tube, Ampul
            $table->integer('stok')->default(0);
            $table->decimal('harga_beli', 12, 2)->default(0);
            $table->decimal('harga_jual', 12, 2)->default(0);

            // Kolom Integrasi SATUSEHAT KFA
            $table->string('kfa_code')->nullable(); // Kode KFA dari Kemenkes
            $table->string('kfa_display')->nullable(); // Deskripsi Nama Resmi KFA

            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('master_obats');
    }
};
