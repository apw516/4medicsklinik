<?php

namespace App\Http\Controllers;

use App\Models\Provinsi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\UserLoginLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Jenssegers\Agent\Agent;
use Stevebauman\Location\Facades\Location;
use Stevebauman\Location\Drivers\IpApi;

class AuthController extends Controller
{
    public function index()
    {
        return view('Auth.login');
    }

    public function registerindex()
    {
        return view('Auth.registrasi');
    }

    public function store(Request $request)
    {
        // Validasi input
        $request->validate([
            'name'     => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:user,username',
            'password' => 'required|string|min:6|confirmed',
        ], [
            'name.required'      => 'Nama lengkap wajib diisi.',
            'username.required'  => 'Username wajib diisi.',
            'username.unique'    => 'Username ini sudah terdaftar.',
            'password.required'  => 'Password wajib diisi.',
            'password.min'       => 'Password minimal harus 6 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
        ]);

        // Simpan data user ke database
        User::create([
            'nama'     => $request->name,
            'username' => $request->username,
            'password' => Hash::make($request->password),
        ]);

        return redirect()->route('login')->with('success', 'Registrasi berhasil! Silahkan login.');
    }

    public function authenticate(Request $request)
    {
        // Validasi input
        $credentials = $request->validate([
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
        ], [
            'username.required' => 'Username wajib diisi.',
            'password.required' => 'Password wajib diisi.',
        ]);

        // Autentikasi ke database
        if (Auth::attempt($credentials)) {
            $user = Auth::user();

            // 1. Cek jika status user TIDAK AKTIF (0 atau null)
            if ($user->status != 1) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return back()->with('error', 'Akun Anda belum diaktivasi. Silakan hubungi Administrator.')
                    ->onlyInput('username');
            }

            // Regenerasi session untuk keamanan
            $request->session()->regenerate();

            // 2. Ambil IP Address
            $ip = $request->ip();

            // Jika localhost / Private IP, gunakan IP fallback untuk testing
            if (in_array($ip, ['127.0.0.1', '::1']) || str_starts_with($ip, '192.168.')) {
                $ip = '180.252.80.1';
            }

            // 3. Ambil data lokasi dengan Try-Catch agar tidak error jika API down / timeout
            $location = null;
            try {
                $location = Location::get($ip);
            } catch (\Exception $e) {
                // Catat log jika API error, tapi jangan hentikan proses login user
                Log::error('Location API Error: ' . $e->getMessage());
            }

            // 4. Deteksi Device
            $agent = new Agent();

            // 5. Simpan Log Login
            UserLoginLog::create([
                'user_id'       => $user->id,
                'ip_address'    => $request->ip(),
                'city'          => $location ? $location->cityName : null,
                'region'        => $location ? $location->regionName : null,
                'country'       => $location ? $location->countryName : null,
                'device_type'   => $agent->isMobile() ? 'Mobile' : ($agent->isTablet() ? 'Tablet' : 'Desktop'),
                'platform'      => $agent->platform(),
                'browser'       => $agent->browser(),
                'user_agent'    => $request->userAgent(),
                'is_successful' => true,
                'login_at'      => now(),
            ]);

            return redirect()->intended('/dashboard')
                ->with('success', 'Selamat datang kembali, ' . $user->nama . '!');
        }

        // Jika kredensial salah
        return back()->with('error', 'Username atau password salah!')
            ->onlyInput('username');
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Anda telah berhasil keluar.');
    }

    public function indexdetailakun()
    {
        $menu = 'indexdetailakun';
        $listProvinsi = Provinsi::orderBy('name', 'asc')->get();
        return view('Auth.indexdetailakun', compact('menu', 'listProvinsi'));
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password'         => 'required|string|min:8|confirmed',
        ], [
            'current_password.required' => 'Password saat ini wajib diisi.',
            'password.required'         => 'Password baru wajib diisi.',
            'password.min'              => 'Password baru minimal 8 karakter.',
            'password.confirmed'        => 'Konfirmasi password baru tidak cocok.',
        ]);

        /** @var \App\Models\User $user */
        $user = auth()->user();

        // Cek apakah password lama cocok
        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json([
                'success' => false,
                'errors'  => [
                    'current_password' => ['Password saat ini tidak sesuai.']
                ]
            ], 422);
        }

        // Update password baru
        $user->update([
            'password' => Hash::make($request->password)
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Password berhasil diperbarui!'
        ], 200);
    }
}