<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MasterObatSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $obats = [
            [
                'kode_obat'   => 'OBT-001',
                'nama_obat'   => 'Paracetamol 500 mg Tablet',
                'kategori'    => 'Analgesik & Antipiretik',
                'satuan'      => 'Tablet',
                'stok'        => 500,
                'harga_beli'  => 300,
                'harga_jual'  => 500,
                'kfa_code'    => '93000108',
                'kfa_display' => 'Paracetamol 500 mg Tablet',
                'is_active'   => true,
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
            [
                'kode_obat'   => 'OBT-002',
                'nama_obat'   => 'Amoxicillin 500 mg Kaplet',
                'kategori'    => 'Antibiotik',
                'satuan'      => 'Kaplet',
                'stok'        => 300,
                'harga_beli'  => 800,
                'harga_jual'  => 1200,
                'kfa_code'    => '93000001',
                'kfa_display' => 'Amoxicillin 500 mg Kaplet',
                'is_active'   => true,
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
            [
                'kode_obat'   => 'OBT-003',
                'nama_obat'   => 'Asam Mefenamat 500 mg Kaplet',
                'kategori'    => 'Analgesik / Antiinflamasi',
                'satuan'      => 'Kaplet',
                'stok'        => 250,
                'harga_beli'  => 600,
                'harga_jual'  => 1000,
                'kfa_code'    => '93000215',
                'kfa_display' => 'Mefenamic Acid 500 mg Kaplet',
                'is_active'   => true,
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
            [
                'kode_obat'   => 'OBT-004',
                'nama_obat'   => 'Omeprazole 20 mg Kapsul',
                'kategori'    => 'Antasida & Antiulserasi',
                'satuan'      => 'Kapsul',
                'stok'        => 200,
                'harga_beli'  => 1000,
                'harga_jual'  => 1800,
                'kfa_code'    => '93000450',
                'kfa_display' => 'Omeprazole 20 mg Kapsul',
                'is_active'   => true,
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
            [
                'kode_obat'   => 'OBT-005',
                'nama_obat'   => 'Cetirizine HCI 10 mg Tablet',
                'kategori'    => 'Antihistamin / Alergi',
                'satuan'      => 'Tablet',
                'stok'        => 400,
                'harga_beli'  => 400,
                'harga_jual'  => 800,
                'kfa_code'    => '93000188',
                'kfa_display' => 'Cetirizine Hydrochloride 10 mg Tablet',
                'is_active'   => true,
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
            [
                'kode_obat'   => 'OBT-006',
                'nama_obat'   => 'Vitamin C 500 mg Tablet',
                'kategori'    => 'Vitamin & Suplemen',
                'satuan'      => 'Tablet',
                'stok'        => 600,
                'harga_beli'  => 250,
                'harga_jual'  => 500,
                'kfa_code'    => '93000820',
                'kfa_display' => 'Ascorbic Acid 500 mg Tablet',
                'is_active'   => true,
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
            [
                'kode_obat'   => 'OBT-007',
                'nama_obat'   => 'OBH Sirup 100 ml',
                'kategori'    => 'Obat Batuk',
                'satuan'      => 'Botol',
                'stok'        => 50,
                'harga_beli'  => 12000,
                'harga_jual'  => 18000,
                'kfa_code'    => '93001020',
                'kfa_display' => 'Obat Batuk Hitam Sirup 100 ml',
                'is_active'   => true,
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
        ];

        DB::table('master_obats')->insert($obats);
    }
}