<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MasterTarifSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tarifs = [
            [
                'kode_tindakan' => 'TND-001',
                'nama_tindakan' => 'Konsultasi Dokter Umum',
                'harga'        => 50000,
                'kategori'      => 'Konsultasi',
                'icd9_code'     => '89.07',
                'icd9_display'  => 'General medical examination',
                'is_active'     => true,
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
            [
                'kode_tindakan' => 'TND-002',
                'nama_tindakan' => 'Pemeriksaan Tanda Vital (TTV)',
                'harga'        => 15000,
                'kategori'      => 'Pemeriksaan',
                'icd9_code'     => '89.15',
                'icd9_display'  => 'Other nonoperative neurologic tests / Physical examination',
                'is_active'     => true,
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
            [
                'kode_tindakan' => 'TND-003',
                'nama_tindakan' => 'Jahit Luka Sederhana (1-3 Jahitan)',
                'harga'        => 100000,
                'kategori'      => 'Tindakan Medis',
                'icd9_code'     => '86.59',
                'icd9_display'  => 'Suture of skin and subcutaneous tissue of other sites',
                'is_active'     => true,
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
            [
                'kode_tindakan' => 'TND-004',
                'nama_tindakan' => 'Rawat Luka / Ganti Verband',
                'harga'        => 45000,
                'kategori'      => 'Tindakan Medis',
                'icd9_code'     => '93.57',
                'icd9_display'  => 'Application of other wound dressing',
                'is_active'     => true,
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
            [
                'kode_tindakan' => 'TND-005',
                'nama_tindakan' => 'Injeksi / Suntik IM/IV',
                'harga'        => 25000,
                'kategori'      => 'Tindakan Medis',
                'icd9_code'     => '99.29',
                'icd9_display'  => 'Injection or infusion of other therapeutic or prophylactic substance',
                'is_active'     => true,
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
            [
                'kode_tindakan' => 'TND-006',
                'nama_tindakan' => 'Nebulizer / Uap',
                'harga'        => 75000,
                'kategori'      => 'Tindakan Medis',
                'icd9_code'     => '93.11',
                'icd9_display'  => 'Assisted exercise in breathing / Inhalation therapy',
                'is_active'     => true,
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
            [
                'kode_tindakan' => 'TND-007',
                'nama_tindakan' => 'Ekstraksi Serumen (Pembersihan Telinga)',
                'harga'        => 60000,
                'kategori'      => 'Tindakan Medis',
                'icd9_code'     => '96.52',
                'icd9_display'  => 'Irrigation of ear',
                'is_active'     => true,
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
            [
                'kode_tindakan' => 'TND-008',
                'nama_tindakan' => 'Pemeriksaan EKG (Rekam Jantung)',
                'harga'        => 120000,
                'kategori'      => 'Penunjang',
                'icd9_code'     => '89.52',
                'icd9_display'  => 'Electrocardiogram',
                'is_active'     => true,
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
        ];

        DB::table('master_tarifs')->insert($tarifs);
    }
}
