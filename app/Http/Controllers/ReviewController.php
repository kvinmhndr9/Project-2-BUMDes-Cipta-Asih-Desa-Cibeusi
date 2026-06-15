<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'id_tiket' => 'required|exists:Tiket,id_tiket',
            'id_wisata' => 'required|exists:Wisata,id_wisata',
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg|max:10240',
        ]);

        $tiket = \App\Models\Tiket::findOrFail($request->id_tiket);

        if ($tiket->id_user !== auth()->id()) {
            abort(403);
        }

        if ($tiket->status !== 'used') {
            return back()->with('error', 'Hanya tiket yang sudah digunakan yang dapat diberi ulasan.');
        }

        // Cek apakah sudah ada ulasan
        $existingReview = \App\Models\Review::where('id_tiket', $tiket->id)->first();
        if ($existingReview) {
            return back()->with('error', 'Anda sudah memberikan ulasan untuk tiket ini.');
        }

        $fotoPath = null;
        if ($request->hasFile('foto')) {
            $fotoPath = cloudinary()->upload($request->file('foto')->getRealPath())->getSecurePath();

            // Tambahkan juga ke Galeri Wisata (ditandai source: review — tidak bisa dihapus manual)
            $wisataModel = \App\Models\Wisata::find($request->id_wisata);
            if ($wisataModel) {
                $galleries = $wisataModel->galleries ?? [];
                $galleries[] = [
                    'image'   => $fotoPath,
                    'caption' => 'Foto dari ulasan pengunjung ' . auth()->user()->name,
                    'source'  => 'review',
                ];
                $wisataModel->update(['galleries' => $galleries]);
            }
        }

        \App\Models\Review::create([
            'id_user' => auth()->id(),
            'id_wisata' => $request->id_wisata,
            'id_tiket' => $request->id_tiket,
            'rating' => $request->rating,
            'comment' => $request->comment,
            'foto' => $fotoPath,
        ]);


        return back()->with('success', 'Terima kasih atas ulasan Anda!');
    }

    public function update(Request $request, \App\Models\Review $review)
    {
        if ($review->id_user !== auth()->id()) {
            abort(403);
        }

        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg|max:10240',
        ]);

        $data = [
            'rating' => $request->rating,
            'comment' => $request->comment,
        ];

        if ($request->hasFile('foto')) {
            $data['foto'] = cloudinary()->upload($request->file('foto')->getRealPath())->getSecurePath();

            // Tambahkan juga ke Galeri Wisata jika ada foto baru (ditandai source: review)
            $wisataModel = \App\Models\Wisata::find($review->id_wisata);
            if ($wisataModel) {
                $galleries = $wisataModel->galleries ?? [];
                $galleries[] = [
                    'image'   => $data['foto'],
                    'caption' => 'Foto dari ulasan pengunjung ' . auth()->user()->name,
                    'source'  => 'review',
                ];
                $wisataModel->update(['galleries' => $galleries]);
            }
        }

        $review->update($data);


        return back()->with('success', 'Ulasan berhasil diperbarui!');
    }
}
