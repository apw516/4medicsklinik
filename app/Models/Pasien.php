<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes; // 1. Import the trait
class Pasien extends Model
{
    use HasFactory, SoftDeletes; // 2. Use the trait inside the class
    protected $guarded = [
        'id'
    ];
    public function kunjungans()
    {
        return $this->hasMany(Kunjungan::class, 'pasien_id');
    }

    /**
     * Relasi ke Kunjungan Hari Ini (Optional / Helper)
     */
    public function kunjunganHariIni()
    {
        return $this->hasOne(Kunjungan::class, 'pasien_id')
            ->whereDate('tgl_masuk', now()->today())
            ->latest();
    }
    public function riwayatKunjungan()
    {
        return $this->hasMany(Kunjungan::class, 'pasien_id', 'id')
            ->latest(); // Mengurutkan dari kunjungan terbaru
    }
}
