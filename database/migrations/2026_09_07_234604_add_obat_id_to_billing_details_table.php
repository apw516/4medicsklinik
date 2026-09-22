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
        Schema::create('billing_details', function (Blueprint $table) {
            $table->id();

            // Relasi ke Tabel Kunjungan
            $table->foreignId('kunjungan_id')
                ->constrained('kunjungans')
                ->onDelete('cascade');

            // Relasi ke Tabel Master Tarif (Dibuat nullable untuk item resep obat)
            $table->foreignId('tarif_id')
                ->nullable()
                ->constrained('master_tarifs')
                ->onDelete('cascade');

            // Relasi ke Tabel Master Obat (Dibuat nullable untuk item tindakan medis)
            $table->foreignId('obat_id')
                ->nullable()
                ->constrained('master_obats')
                ->onDelete('cascade');

            // Detail Rincian Biaya
            $table->decimal('harga', 12, 2)->default(0);
            $table->integer('qty')->default(1);
            $table->decimal('subtotal', 12, 2)->default(0);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('billing_details');
    }
};