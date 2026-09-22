<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class billing_details extends Model
{
    use HasFactory;
    protected $casts = [
        'qty'        => 'integer',
        'harga'      => 'float',
        'harga_jual' => 'float',
        'subtotal'   => 'float',
    ];
    public function kunjungan()
    {
        return $this->belongsTo(Kunjungan::class, 'kunjungan_id');
    }
    protected $table = 'billing_details'; // Sesuaikan nama tabel Anda

    /**
     * Relasi ke Master Obat
     */
    public function master_obats()
    {
        // Parameter 2: Nama FK di tabel billing_details (misal: 'obat_id' atau 'master_obat_id')
        // Parameter 3: PK di tabel master_obats (misal: 'id')
        return $this->belongsTo(master_obats::class, 'obat_id', 'id');
    }

    /**
     * Relasi ke Master Tarif
     */
    public function master_tarifs()
    {
        // Parameter 2: Nama FK di tabel billing_details (misal: 'tarif_id' atau 'master_tarif_id')
        // Parameter 3: PK di tabel master_tarifs (misal: 'id')
        return $this->belongsTo(master_tarifs::class, 'tarif_id', 'id');
    }
}
