<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Organization extends Model
{
    use HasFactory;
    protected $guarded = [
        'id'
    ];
    protected $fillable = [
        'satusehat_org_id', // <-- PASTIKAN INI ADA
        'parent_org_id',
        'nama_organisasi',
        'telepon',
        'email',
        'alamat',
        'kode_pos',
        'status',
        'tipe',
        'client_id'
    ];
}
