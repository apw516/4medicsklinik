<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Helpers\LicenseHelper;

class CheckAppLicense
{
    public function handle(Request $request, Closure $next)
    {
        // Bebaskan route aktivasi agar Anda bisa akses saat mengaktifkan
        if ($request->is('app-activation*')) {
            return $next($request);
        }

        $check = LicenseHelper::checkLicenseStatus();

        if (!$check['status']) {
            if ($request->ajax()) {
                return response()->json([
                    'status' => 'error',
                    'message' => $check['message']
                ], 403);
            }

            return response()->view('errors.license_expired', ['message' => $check['message']], 403);
        }

        return $next($request);
    }
}
