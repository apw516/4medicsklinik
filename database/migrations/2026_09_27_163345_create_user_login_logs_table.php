<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
  
    public function up(): void
    {
        Schema::create('user_login_logs', function (Blueprint $table) {
            $table->id();
          $table->foreignId('user_id')->constrained('user')->cascadeOnDelete();
            
            // Info Jaringan & Lokasi
            $table->string('ip_address', 45)->nullable();
            $table->string('city')->nullable();
            $table->string('region')->nullable(); // Provinsi
            $table->string('country')->nullable();
            
            // Info Perangkat & Peramban
            $table->string('device_type')->nullable(); // Contoh: Desktop, Mobile, Tablet
            $table->string('platform')->nullable();    // Contoh: Windows, Android, iOS, OS X
            $table->string('browser')->nullable();     // Contoh: Chrome, Firefox, Safari
            $table->text('user_agent')->nullable();    // Raw User Agent String
            
            // Status & Tanggal Login
            $table->boolean('is_successful')->default(true); // Status berhasil/gagal
            $table->timestamp('login_at')->useCurrent();     // Waktu login
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_login_logs');
    }
};
