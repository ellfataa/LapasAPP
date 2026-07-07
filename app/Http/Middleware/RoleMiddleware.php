<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        // 1. Jika belum login sama sekali, arahkan ke halaman login
        if (!Auth::check()) {
            return redirect()->route('login')->withErrors(['akses' => 'Sesi Anda telah habis. Silakan login kembali.']);
        }

        // 2. Jika Role tidak sesuai dengan halaman yang ingin diakses
        if (!in_array(Auth::user()->role, $roles)) {

            $roleAktif = Auth::user()->role;
            $pesanTolak = "Akses dialihkan. Sesi aktif Anda saat ini terdeteksi sebagai " . strtoupper($roleAktif) . ".";

            // Alihkan (Redirect) ke dashboard masing-masing sesuai sesi yang menimpa
            if ($roleAktif === 'admin') {
                return redirect()->route('dashboard.admin')->withErrors(['akses' => $pesanTolak]);
            } elseif ($roleAktif === 'pengawas') {
                return redirect()->route('dashboard.pengawas')->withErrors(['akses' => $pesanTolak]);
            } else {
                return redirect()->route('dashboard.narapidana')->withErrors(['akses' => $pesanTolak]);
            }
        }

        return $next($request);
    }
}
