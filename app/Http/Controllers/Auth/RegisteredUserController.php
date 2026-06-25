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

        // 3. JANGAN auto-login. Langsung lempar ke halaman login dengan pesan sukses.
        return redirect()->route('login')->with('success', 'Registrasi akun berhasil! Silakan Login menggunakan Identitas dan Password Anda.');
    }
