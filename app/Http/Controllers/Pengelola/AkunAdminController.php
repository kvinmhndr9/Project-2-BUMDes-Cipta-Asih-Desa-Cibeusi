<?php

namespace App\Http\Controllers\Pengelola;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Wisata;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class AkunAdminController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:pengelola_bumdes']);
    }

    /**
     * Tampilkan daftar semua akun admin.
     */
    public function index()
    {
        $admins = User::where('role', 'admin')
            ->with('wisata')
            ->orderBy('name')
            ->get();

        return view('pengelola.akun-admin.index', compact('admins'));
    }

    /**
     * Tampilkan form tambah admin baru.
     */
    public function create()
    {
        $wisataList = Wisata::orderBy('nama')->get();
        return view('pengelola.akun-admin.create', compact('wisataList'));
    }

    /**
     * Simpan akun admin baru ke database.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'       => ['required', 'string', 'max:100'],
            'email'      => ['required', 'email', 'max:150', 'unique:User,email'],
            'password'   => ['required', Password::min(8)],
            'id_wisata'  => ['required', 'exists:Wisata,id_wisata'],
        ], [
            'name.required'      => 'Nama wajib diisi.',
            'email.required'     => 'Email wajib diisi.',
            'email.unique'       => 'Email sudah digunakan oleh akun lain.',
            'password.required'  => 'Password wajib diisi.',
            'id_wisata.required' => 'Wisata wajib dipilih.',
            'id_wisata.exists'   => 'Wisata tidak ditemukan.',
        ]);

        User::create([
            'name'      => $request->name,
            'email'     => $request->email,
            'password'  => Hash::make($request->password),
            'role'      => 'admin',
            'id_wisata' => $request->id_wisata,
        ]);

        return redirect()->route('pengelola.akun-admin.index')
            ->with('success', 'Akun admin "' . $request->name . '" berhasil dibuat.');
    }

    /**
     * Tampilkan form edit akun admin.
     */
    public function edit(User $akunAdmin)
    {
        // Pastikan yang diedit hanya user berole admin
        abort_if($akunAdmin->role !== 'admin', 404);

        $wisataList = Wisata::orderBy('nama')->get();
        return view('pengelola.akun-admin.edit', compact('akunAdmin', 'wisataList'));
    }

    /**
     * Update data akun admin.
     */
    public function update(Request $request, User $akunAdmin)
    {
        abort_if($akunAdmin->role !== 'admin', 404);

        $request->validate([
            'name'      => ['required', 'string', 'max:100'],
            'email'     => ['required', 'email', 'max:150', 'unique:User,email,' . $akunAdmin->id_user . ',id_user'],
            'password'  => ['nullable', Password::min(8)],
            'id_wisata' => ['required', 'exists:Wisata,id_wisata'],
        ], [
            'name.required'      => 'Nama wajib diisi.',
            'email.required'     => 'Email wajib diisi.',
            'email.unique'       => 'Email sudah digunakan oleh akun lain.',
            'id_wisata.required' => 'Wisata wajib dipilih.',
            'id_wisata.exists'   => 'Wisata tidak ditemukan.',
        ]);

        $data = [
            'name'      => $request->name,
            'email'     => $request->email,
            'id_wisata' => $request->id_wisata,
        ];

        // Hanya update password jika diisi
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $akunAdmin->update($data);

        return redirect()->route('pengelola.akun-admin.index')
            ->with('success', 'Akun admin "' . $akunAdmin->name . '" berhasil diperbarui.');
    }

    /**
     * Hapus akun admin.
     */
    public function destroy(User $akunAdmin)
    {
        abort_if($akunAdmin->role !== 'admin', 404);

        $name = $akunAdmin->name;
        $akunAdmin->delete();

        return redirect()->route('pengelola.akun-admin.index')
            ->with('success', 'Akun admin "' . $name . '" berhasil dihapus.');
    }
}
