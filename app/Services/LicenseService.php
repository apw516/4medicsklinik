<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class LicenseService
{
    // URL Server Central Anda di Cloud
    private $centralServerUrl = 'https://license.domain-anda.com/api/v1/check-license';

    public function isLicenseValid()
    {
        // Gunakan cache agar tidak selalu menembak API central di setiap refresh halaman
        return Cache::remember('app_license_status', now()->addHours(6), function () {
            return $this->verifyWithCentralServer();
        });
    }

    public function verifyWithCentralServer()
    {
        try {
            $clientKey = config('app.client_license_key'); // Diambil dari file .env klinik

            // Panggil API ke Server Central milik Anda
            $response = Http::timeout(5)->post($this->centralServerUrl, [
                'license_key' => $clientKey,
                'domain_ip'   => request()->ip(),
                'mac_address' => $this->getMacAddress()
            ]);

            if ($response->successful() && $response->json('status') === 'ACTIVE') {
                // Simpan bukti offline validasi sementara ( encrypted file )
                Storage::put('license.lic', encrypt(now()->addDays(3)->timestamp));
                return true;
            }

            return false;
        } catch (\Exception $e) {
            // JIKA INTERNET MATI: Toleransi Offline Mode (Grace Period 3 Hari)
            return $this->checkOfflineBackup();
        }
    }

    private function checkOfflineBackup()
    {
        if (Storage::exists('license.lic')) {
            try {
                $validUntil = decrypt(Storage::get('license.lic'));
                return now()->timestamp < $validUntil;
            } catch (\Exception $e) {
                return false;
            }
        }
        return false;
    }

    private function getMacAddress()
    {
        // Opsional: Untuk mengunci aplikasi ke 1 hardware fisik server klinik saja
        return exec('getmac');
    }
}
