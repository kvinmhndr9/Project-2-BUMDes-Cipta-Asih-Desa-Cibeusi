<?php

namespace App\Http\Controllers\Pengunjung;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $totalTiketUsed = \App\Models\Tiket::where('id_user', $user->id)
                                ->where('status', 'used')
                                ->count();
        $totalTiketBeli = \App\Models\Tiket::where('id_user', $user->id)->count();
        
        $totalUlasan = \App\Models\Review::where('id_user', $user->id)->count();
        $reviews = \App\Models\Review::where('id_user', $user->id)->with('wisata')->latest()->take(5)->get();
        
        return view('pengunjung.profil', compact('user', 'totalTiketUsed', 'totalTiketBeli', 'totalUlasan', 'reviews'));
    }

    public function update(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();
        
        $validated = $request->validate([
            'name'           => 'required|string|max:255',
            'no_hp'          => 'required|string|max:20',
            'asal_kota'      => 'nullable|string|max:100',
            'avatar'         => 'nullable|image|mimes:jpeg,png,jpg|max:10240',
            'avatar_cropped' => 'nullable|string',   // base64 dari Cropper.js
        ]);

        $data = [
            'name'      => $validated['name'],
            'no_hp'     => $validated['no_hp'],
            'asal_kota' => $validated['asal_kota'] ?? null,
        ];

        if (!empty($validated['avatar_cropped'])) {
            // ── Mode crop base64 -> Cloudinary ──
            $avatarUrl = cloudinary()->upload($validated['avatar_cropped'])->getSecurePath();
            $data['avatar'] = $avatarUrl;

        } elseif ($request->hasFile('avatar')) {
            // ── Mode upload biasa -> Cloudinary ──
            $avatarUrl = cloudinary()->upload($request->file('avatar')->getRealPath())->getSecurePath();
            $data['avatar'] = $avatarUrl;
        }

        $user->update($data);

        return back()->with('success', 'Profil berhasil diperbarui.');
    }

    /**
     * Update avatar saja via AJAX (dari crop modal).
     * Menerima base64 image, simpan ke disk, kembalikan URL baru sebagai JSON.
     */
    public function updateAvatar(Request $request)
    {
        $request->validate([
            'avatar_cropped' => 'required|string',
        ]);

        /** @var \App\Models\User $user */
        $user   = auth()->user();
        $base64 = $request->input('avatar_cropped');

        try {
            $avatarUrl = cloudinary()->upload($base64)->getSecurePath();
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Gagal mengupload ke server Cloudinary.'], 422);
        }

        $user->update(['avatar' => $avatarUrl]);

        return response()->json([
            'success'    => true,
            'message'    => 'Foto profil berhasil diperbarui.',
            'avatar_url' => $avatarUrl . '?t=' . time(),
        ]);
    }
}
