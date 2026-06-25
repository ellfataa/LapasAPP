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
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();
        $request->session()->regenerate();

        $role = $request->user()->role;
        $pesanSukses = 'Login berhasil! Selamat datang kembali, ' . $request->user()->nama . '.';

        if ($role === 'admin') {
            return redirect()->intended(route('dashboard.admin', absolute: false))->with('success', $pesanSukses);
        } elseif ($role === 'pengawas') {
            return redirect()->intended(route('dashboard.pengawas', absolute: false))->with('success', $pesanSukses);
        }

        return redirect()->intended(route('dashboard.narapidana', absolute: false))->with('success', $pesanSukses);
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
