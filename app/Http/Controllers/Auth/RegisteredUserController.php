<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    public function create(): View
    {
        return view('auth.register');
    }

    public function store(Request $request): RedirectResponse
    {
        // 1. Validasi Input
        $request->validate([
            'nama' => ['required', 'string', 'max:255', 'regex:/^[\pL\s.,\'’()\/&-]+$/u'],
            'nomor_induk' => ['required', 'string', 'max:50', 'regex:/^[\pL\pN\s.,\'’()\/&-]+$/u', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ], [
            'nama.regex' => 'Format salah: Nama Lengkap wajib menggunakan format yang sesuai SK/Spreadsheet (huruf dan titik/koma diizinkan).',
            'nomor_induk.regex' => 'Format salah: Nomor Induk hanya boleh berisi huruf, angka, spasi, dan tanda baca umum.',
            'nomor_induk.max' => 'Nomor Induk tidak boleh lebih dari 50 karakter.',
            'nomor_induk.unique' => 'Nomor Induk ini sudah terdaftar di sistem.'
        ]);

        // 2. Simpan Data (Role default narapidana)
        $user = User::create([
            'nama' => $request->nama,
            'nomor_induk' => $request->nomor_induk,
            'email' => null,
            'role' => 'narapidana',
            'password' => Hash::make($request->password),
        ]);

        event(new Registered($user));

        return redirect()->route('login')->with('success', 'Registrasi berhasil! Silakan Login menggunakan Nomor Induk dan Password Anda.');
    }
}
