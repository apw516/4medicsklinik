<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    use HasFactory;
    protected $fillable = [
        'satusehat_location_id',
        'managing_organization_id',
        'nama_lokasi',
        'tipe_fisik',
        'status',
        'deskripsi',
        'client_id'
    ];
}
