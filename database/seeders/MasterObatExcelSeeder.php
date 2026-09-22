<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\IOFactory;

class MasterObatExcelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $filePath = storage_path('app/Mapping Data KFA_20230119.xlsx');

        if (!file_exists($filePath)) {
            $this->command->error("File Excel tidak ditemukan di: {$filePath}");
            return;
        }

        $spreadsheet = IOFactory::load($filePath);
        $sheet = $spreadsheet->getSheet(0);
        $rows = $sheet->toArray(null, true, true, true);

        // Abaikan Baris Header (Baris 1)
        array_shift($rows);

        $batch = [];
        $index = 1;

        foreach ($rows as $row) {
            $kfaCode    = trim($row['A'] ?? ''); // Kolom Kode KFA (PA)
            $kfaDisplay = trim($row['B'] ?? ''); // Kolom Display Name
            $satuan     = trim($row['Q'] ?? 'Tablet'); // Kolom Bentuk Sediaan Display Name

            if (empty($kfaCode) || empty($kfaDisplay)) {
                continue;
            }

            $batch[] = [
                'kode_obat'   => 'OBT-' . str_pad($index, 5, '0', STR_PAD_LEFT),
                'nama_obat'   => $kfaDisplay,
                'kategori'    => 'Obat Farmasi',
                'satuan'      => !empty($satuan) ? $satuan : 'Tablet',
                'stok'        => 100,
                'harga_beli'  => 1000,
                'harga_jual'  => 1500,
                'kfa_code'    => $kfaCode,
                'kfa_display' => $kfaDisplay,
                'is_active'   => true,
                'created_at'  => now(),
                'updated_at'  => now(),
            ];

            $index++;

            // Insert bertahap per 500 record untuk performa optimal
            if (count($batch) >= 500) {
                DB::table('master_obats')->insert($batch);
                $batch = [];
            }
        }

        if (!empty($batch)) {
            DB::table('master_obats')->insert($batch);
        }

        $this->command->info("Berhasil mengimpor " . ($index - 1) . " data obat KFA ke database!");
    }
}
