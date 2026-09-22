<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes; // 1. Import the trait

class Kunjungan extends Model
{
    use HasFactory, SoftDeletes; // 2. Use the trait inside the class
    protected $guarded = [
        'id'
    ];
    public function unit()
    {
        return $this->belongsTo(Unit::class, 'poli_id', 'id');
    }
    public function dokter()
    {
        return $this->belongsTo(Dokter::class, 'dokter_id', 'id');
    }
    public function pasien()
    {
        return $this->belongsTo(Pasien::class, 'pasien_id', 'id');
    }
    public function pembayaran()
    {
        return $this->hasOne(Pembayaran::class, 'kunjungan_id', 'id');
    }
    public function poli()
    {
        return $this->belongsTo(Location::class, 'poli_id', 'id');
    }
    public function erm()
    {
        return $this->hasOne(ErmRecord::class, 'kunjungan_id');
    }
    public function billing_details()
    {
        return $this->hasOne(billing_details::class, 'kunjungan_id');
    }
}
