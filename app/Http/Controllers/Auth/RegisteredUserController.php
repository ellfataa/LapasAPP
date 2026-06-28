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
            'nama' => ['required', 'string', 'max:255', 'regex:/^[a-zA-Z\s]+$/'],
            'nomor_induk' => ['required', 'string', 'max:18', 'regex:/^[0-9]+$/', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ], [
            'nama.regex' => 'Format salah: Nama Lengkap hanya boleh berisi huruf dan spasi.',
            'nomor_induk.regex' => 'Format salah: Nomor Induk hanya boleh berisi angka.',
            'nomor_induk.max' => 'Nomor Induk tidak boleh lebih dari 18 digit.',
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
