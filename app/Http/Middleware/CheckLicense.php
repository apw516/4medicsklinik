<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Services\LicenseService;

class CheckLicense
{
    protected $licenseService;

    public function __construct(LicenseService $licenseService)
    {
        $this->licenseService = $licenseService;
    }

    public function handle(Request $request, Closure $next)
    {
        // Bypass untuk halaman lisensi expired itu sendiri agar tidak infinite loop
        if ($request->is('license-expired') || $request->is('api/activate-license')) {
            return $next($request);
        }

        if (!$this->licenseService->isLicenseValid()) {
            if ($request->ajax()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Lisensi aplikasi tidak aktif atau terblokir. Silakan hubungi Vendor.'
                ], 403);
            }

            return redirect()->route('license.expired');
        }

        return $next($request);
    }
}