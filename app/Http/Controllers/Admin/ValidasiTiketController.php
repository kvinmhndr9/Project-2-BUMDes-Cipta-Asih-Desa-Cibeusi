<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tiket;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ValidasiTiketController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:admin']);
    }

    public function index()
    {
        return view('admin.validasi-index');
    }

    public function cari(Request $request)
    {
        if (!$request->filled('kode')) {
            return redirect()->route('admin.validasi.index')->with('error', 'Silakan masukkan atau scan kode tiket terlebih dahulu.');
        }

        $request->validate(['kode' => 'required|string|max:255']);
        $input = trim($request->kode);

        // ── Ekstrak kode dari URL jika QR berisi URL halaman e-tiket publik ──
        // Contoh: https://domain.com/tiket/SI-MQHGYPQM → SI-MQHGYPQM
        if (filter_var($input, FILTER_VALIDATE_URL) || str_contains($input, '/tiket/')) {
            $parsed = parse_url($input, PHP_URL_PATH);
            $segments = array_filter(explode('/', $parsed));
            // Ambil segmen terakhir sebagai kode tiket
            $input = end($segments) ?: $input;
        }

        $kode = strtoupper($input);

        $tiket = Tiket::with(['wisata', 'user'])
            ->where('kode_tiket', $kode)
            ->first();

        if (! $tiket) {
            return redirect()->route('admin.validasi.index')->with('error', 'Tiket dengan kode ' . $kode . ' tidak ditemukan.');
        }

        $user = Auth::user();
        if ($tiket->id_wisata !== $user->id_wisata) {
            return redirect()->route('admin.validasi.index')->with('error', 'Tiket ini bukan untuk wisata Anda (' . $user->wisata->nama . ').');
        }

        return view('admin.validasi-detail', compact('tiket'));
    }

    public function validasi(Tiket $tiket)
    {
        $user = Auth::user();
        if ($tiket->id_wisata !== $user->id_wisata) {
            abort(403);
        }

        if ($tiket->status === 'used') {
            return redirect()->route('admin.validasi.index')->with('error', 'Tiket ini sudah divalidasi sebelumnya.');
        }
        if ($tiket->status !== 'paid') {
            return redirect()->route('admin.validasi.index')->with('error', 'Hanya tiket yang sudah dibayar yang dapat divalidasi. Status saat ini: ' . ucfirst($tiket->status));
        }

        /** @var Carbon $tanggalBerkunjung */
        $tanggalBerkunjung = Carbon::parse($tiket->tanggal_berkunjung);

        if ($tanggalBerkunjung->toDateString() !== now()->toDateString()) {
            return redirect()->route('admin.validasi.index')->with('error', 'Tiket ini hanya berlaku untuk tanggal ' . $tanggalBerkunjung->translatedFormat('d F Y') . '. Validasi gagal karena bukan untuk hari ini.');
        }

        $tiket->update([
            'status' => 'used',
            'used_at' => now(),
        ]);


        return redirect()->route('admin.validasi.index')->with('success', 'Tiket ' . $tiket->kode_tiket . ' berhasil divalidasi.');
    }

    public function history(Request $request)
    {
        $user = Auth::user();
        $wisata = $user->wisata;

        if (!$wisata) {
            return redirect()->route('home')->with('error', 'Admin tidak terhubung ke wisata.');
        }

        $periode = $request->get('periode', 'hari');
        $tanggal = $request->get('tanggal', now()->format('Y-m-d'));

        $query = Tiket::with(['user', 'wisata'])
            ->where('id_wisata', $wisata->id)
            ->where('status', 'used');

        if ($periode === 'hari') {
            $date = Carbon::parse($tanggal);
            $query->whereDate('used_at', $date);
            $label = $date->translatedFormat('l, d F Y');
        } elseif ($periode === 'minggu') {
            $date = Carbon::parse($tanggal);
            $start = $date->copy()->startOfWeek();
            $end   = $date->copy()->endOfWeek();
            $query->whereBetween('used_at', [$start, $end]);
            $label = 'Minggu ' . $start->format('d/m') . ' - ' . $end->format('d/m/Y');
        } else {
            $date = Carbon::parse($tanggal);
            $query->whereMonth('used_at', $date->month)->whereYear('used_at', $date->year);
            $label = $date->translatedFormat('F Y');
        }

        $data         = $query->orderBy('used_at', 'desc')->get();
        $totalTiket   = $data->sum('jumlah');
        $totalValidasi = $data->count();

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'label'         => $label,
                'totalTiket'    => $totalTiket,
                'totalValidasi' => $totalValidasi,
                'data'          => $data->map(function ($d) {
                    return [
                        'waktu_validasi' => $d->used_at?->format('d/m/Y H:i') ?? '-',
                        'kode_tiket'     => $d->kode_tiket,
                        'pemesan'        => $d->user->name ?? '-',
                        'jumlah'         => $d->jumlah,
                        'tanggal_kunjungan' => $d->tanggal_berkunjung?->format('d/m/Y') ?? '-',
                    ];
                }),
            ]);
        }

        return view('admin.history-validasi', compact(
            'wisata', 'periode', 'tanggal', 'label', 'data', 'totalTiket', 'totalValidasi'
        ));
    }
}
