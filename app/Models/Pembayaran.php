<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pembayaran extends Model
{
    use HasFactory;

    protected $fillable = [
        'kunjungan_id',
        'metode_pembayaran',
        'jumlah_bayar',
        'status',
        'kasir_id',
    ];

    public function kunjungan()
    {
        return $this->belongsTo(Kunjungan::class);
    }

    public function kasir()
    {
        return $this->belongsTo(User::class, 'kasir_id');
    }

    public function billing_details()
    {
        return $this->belongsTo(billing_details::class, 'pembayaran_id');
    }
}
