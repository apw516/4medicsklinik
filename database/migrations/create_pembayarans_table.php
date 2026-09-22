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
        Schema::create('pembayarans', function (Blueprint $table) {
            $table->id();

            // Relasi ke tabel kunjungans
            $table->foreignId('kunjungan_id')
                ->constrained('kunjungans')
                ->onDelete('cascade');

            // Metode Pembayaran (misal: tunai, transfer, qris, debit, bpjs)
            $table->string('metode_pembayaran', 50);

            // Jumlah uang yang dibayarkan oleh pasien/pembayar
            $table->decimal('jumlah_bayar', 15, 2);

            // Status Pembayaran (default 'lunas')
            $table->string('status', 20)->default('lunas');

            // Relasi ke tabel users (Petugas Kasir)
            $table->foreignId('kasir_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pembayarans');
    }
};
