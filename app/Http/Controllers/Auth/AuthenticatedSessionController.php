<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    public function create(): View
    {
        return view('auth.login');
    }

    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();
        $request->session()->regenerate();

        $role = $request->user()->role;
        $pesanSukses = 'Selamat datang kembali, ' . $request->user()->nama . '!';

        if ($role === 'admin') {
            return redirect()->intended(route('dashboard.admin', absolute: false))->with('success', $pesanSukses);
        } elseif ($role === 'pengawas') {
            return redirect()->intended(route('dashboard.pengawas', absolute: false))->with('success', $pesanSukses);
        }

        return redirect()->intended(route('dashboard.narapidana', absolute: false))->with('success', $pesanSukses);
    }

    public function destroy(Request $request): RedirectResponse
    {
        try {
            Auth::guard('web')->logout();

            $request->session()->invalidate();

            $request->session()->regenerateToken();

            // Jika berhasil, arahkan langsung ke rute login dan kirim pesan sukses
            return redirect()->route('login')->with('success', 'Anda telah berhasil logout dari sistem.');

        } catch (\Exception $e) {
            // Jika terjadi kegagalan sistem saat logout, kembalikan ke halaman sebelumnya dengan pesan error
            return redirect()->back()->withErrors(['logout_error' => 'Terjadi kesalahan sistem saat mencoba keluar. Silakan coba lagi.']);
        }
    }
}
