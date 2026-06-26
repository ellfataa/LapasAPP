<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Str;

class GoogleAuthController extends Controller
{
    // Arahkan ke halaman login Google
    public function redirect()
    {
        return Socialite::driver('google')->redirect();
    }

    // Tangkap data balasan dari Google
    public function callback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();

            // Cari user, apakah sudah pernah daftar pakai Google/Email ini?
            $user = User::where('google_id', $googleUser->id)->orWhere('email', $googleUser->email)->first();

            if ($user) {
                // Update google ID jika sebelumnya daftar manual (jika kebetulan email sama)
                $user->update(['google_id' => $googleUser->id]);
                Auth::login($user);
                $pesan = 'Login akun berhasil! Selamat datang kembali, ' . $user->nama . '.';
            } else {
                // Jika belum ada, buat akun Klien (Narapidana) Otomatis
                $user = User::create([
                    'nama' => $googleUser->name,
                    'email' => $googleUser->email,
                    'google_id' => $googleUser->id,
                    'nomor_induk' => 'GL' . date('ymd') . rand(1000, 9999), // Generate NIK Sementara
                    'role' => 'narapidana',
                    'password' => Hash::make(Str::random(24)), // Password diacak tidak bisa ditebak
                ]);
                Auth::login($user);
                $pesan = 'Registrasi akun berhasil! Selamat datang, ' . $user->nama . '.';
            }

            // PERBAIKAN: Arahkan langsung ke dashboard masing-masing agar pesan Pop-up tidak hangus di jalan
            $role = $user->role;
            if ($role === 'admin') {
                return redirect()->route('dashboard.admin')->with('success', $pesan);
            } elseif ($role === 'pengawas') {
                return redirect()->route('dashboard.pengawas')->with('success', $pesan);
            }

            return redirect()->route('dashboard.narapidana')->with('success', $pesan);

        } catch (\Exception $e) {
            // Jika gagal/batal login google, kembalikan ke halaman login beserta pop-up error
            return redirect()->route('login')->withErrors(['identitas' => 'Gagal terhubung ke Akun Google. Silakan coba lagi.']);
        }
    }
}
