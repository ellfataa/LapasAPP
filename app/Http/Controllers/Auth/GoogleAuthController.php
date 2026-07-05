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
    public function redirect()
    {
        return Socialite::driver('google')->redirect();
    }

    // Tangkap data balasan dari Google
    public function callback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();

            // Cari user, apakah sudah pernah daftar pakai Google/Email
            $user = User::where('google_id', $googleUser->id)->orWhere('email', $googleUser->email)->first();

            if ($user) {
                // UPDATE & LOGIN: Jika akun sudah ada (misal sudah daftar manual sebelumnya)
                $user->update(['google_id' => $googleUser->id]);
                Auth::login($user);
                $pesan = 'Login via Google berhasil! Selamat datang kembali, ' . $user->nama . '.';

                // Arahkan ke dashboard masing-masing
                $role = $user->role;
                if ($role === 'admin') {
                    return redirect()->route('dashboard.admin')->with('success', $pesan);
                } elseif ($role === 'pengawas') {
                    return redirect()->route('dashboard.pengawas')->with('success', $pesan);
                }
                return redirect()->route('dashboard.narapidana')->with('success', $pesan);

            } else {
                // REGISTRASI BARU: Jika belum ada di database

                // 1. Bersihkan nama dari Google (Hapus emoticon/simbol yang tidak diizinkan regex sistem kita)
                $cleanName = preg_replace('/[^a-zA-ZÀ-ÖØ-öø-ÿ\s.,\'’()\/&-]/u', '', $googleUser->name);

                // 2. Buat akun Klien/Narapidana Otomatis
                $user = User::create([
                    'nama' => $cleanName ?: 'User Google', // Fallback jika nama kosong setelah dibersihkan
                    'email' => $googleUser->email,
                    'google_id' => $googleUser->id,
                    'nomor_induk' => 'GOOGLE-' . date('ymd') . '-' . rand(1000, 9999), // Generate NIK Sementara (Cth: GOOGLE-260704-1234)
                    'role' => 'narapidana',
                    'password' => Hash::make(Str::random(24)), // Password diacak tidak bisa ditebak
                ]);

                Auth::login($user);

                // 3. Pesan peringatan keras untuk pengguna baru
                $pesanPeringatan = 'Registrasi Google berhasil! PENTING: Mohon segera ubah Nama Lengkap dan Nomor Induk sementara Anda di bawah ini agar sesuai dengan data resmi.';

                // 4. Arahkan LANGSUNG KE HALAMAN PROFIL, bukan ke Dashboard
                return redirect()->route('profile.edit')->with('success', $pesanPeringatan);
            }

        } catch (\Exception $e) {
            return redirect()->route('login')->withErrors(['identitas' => 'Gagal terhubung ke Akun Google. Silakan coba lagi.']);
        }
    }
}
