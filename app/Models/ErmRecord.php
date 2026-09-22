<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ErmRecord extends Model
{
    use HasFactory;

    // Nama tabel jika tidak menggunakan penamaan standar jamak (opsional)
    protected $table = 'erm_records'; 

    protected $guarded = [];

    /**
     * Relasi ERM Record milik Dokter Pemeriksa
     */
    public function dokter()
    {
        return $this->belongsTo(Dokter::class, 'dokter_id');
    }

    /**
     * Relasi ERM Record milik Kunjungan
     */
    public function kunjungan()
    {
        return $this->belongsTo(Kunjungan::class, 'kunjungan_id');
    }
}
