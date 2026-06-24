<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        // 1. Validasi Input
        $request->validate([
            'nama' => ['required', 'string', 'max:255'],
            'nomor_induk' => ['required', 'string', 'max:100', 'unique:'.User::class],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'role' => ['required', 'in:admin,pengawas,narapidana'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        // 2. Simpan Data ke Database
        $user = User::create([
            'nama' => $request->nama,
            'nomor_induk' => $request->nomor_induk,
            'email' => $request->email,
            'role' => $request->role,
            'password' => Hash::make($request->password),
        ]);

        event(new Registered($user));

        // 3. Login otomatis setelah register
        Auth::login($user);

        // 4. Arahkan ke dashboard sesuai role
        if ($user->role === 'admin' || $user->role === 'pengawas') {
            return redirect('/petugas/dashboard');
        }

        return redirect('/narapidana/dashboard');
    }
}
