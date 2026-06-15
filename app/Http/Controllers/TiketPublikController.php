<?php

namespace App\Http\Controllers;

use App\Models\Tiket;
use Illuminate\Http\Request;

class TiketPublikController extends Controller
{
    /**
     * Halaman e-tiket publik — dapat diakses siapapun via URL dari QR code.
     * Menampilkan desain tiket fisik premium tanpa perlu login.
     */
    public function show(string $kode)
    {
        $tiket = Tiket::where('kode_tiket', strtoupper(trim($kode)))
            ->with(['wisata', 'user'])
            ->firstOrFail();

        return view('tiket-publik', compact('tiket'));
    }
}
