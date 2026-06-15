<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>E-Tiket {{ $tiket->kode_tiket }} — SI-ASIH</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,400;0,500;0,600;0,700;0,800;1,400&display=swap" rel="stylesheet">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            min-height: 100vh;
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 50%, #0f2027 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .ticket-wrapper {
            width: 100%;
            max-width: 480px;
        }

        /* ──────────────────────────────────────────────────────────────
         * TIKET UTAMA — menggunakan gambar desain asli sebagai background
         * ────────────────────────────────────────────────────────────── */
        .ticket-card {
            position: relative;
            width: 100%;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 32px 64px rgba(0,0,0,0.5), 0 0 0 1px rgba(255,255,255,0.08);
        }

        /* Gambar desain tiket sebagai background */
        .ticket-bg-img {
            width: 100%;
            height: auto;
            display: block;
        }

        /* Overlay panel info — diposisikan di atas gambar, area kiri (stub/sisi putih) */
        .ticket-info-overlay {
            position: absolute;
            top: 0; left: 0;
            width: 34%;        /* lebar stub putih di gambar desain ≈ 34% */
            height: 100%;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: flex-start;
            padding: 8% 4% 6%;
            gap: 6px;
        }

        .info-kode {
            font-size: 0.60rem;
            font-weight: bold;
            letter-spacing: 1.5px;
            color: #0f172a;
            text-align: center;
            font-family: 'Courier New', monospace;
            word-break: break-all;
            line-height: 1.4;
            margin-top: 29px;
            margin-left: -20px;
            text-shadow: 0.3px 0.3px 0 #0f172a;
        }

        .info-label {
            font-size: 0.5rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #64748b;
            text-align: center;
            margin-top: 2px;
        }

        .info-value {
            font-size: 0.62rem;
            font-weight: 700;
            color: #1e293b;
            text-align: center;
            line-height: 1.3;
        }

        .info-divider {
            width: 80%;
            border: none;
            border-top: 1px dashed #cbd5e1;
            margin: 3px 0;
        }

        /* Frame kotak tanggal berlaku */
        .date-frame {
            background: linear-gradient(135deg, #0ea5e9, #2563eb);
            border-radius: 8px;
            padding: 5px 8px;
            text-align: center;
            width: 90%;
            box-shadow: 0 2px 8px rgba(14,165,233,0.35);
        }
        .date-frame .date-frame-label {
            font-size: 0.42rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            color: rgba(255,255,255,0.85);
            margin-bottom: 2px;
        }
        .date-frame .date-frame-value {
            font-size: 0.65rem;
            font-weight: 800;
            color: #fff;
            letter-spacing: 0.5px;
            line-height: 1.2;
        }

        /* QR code di area stub */
        .stub-qr {
            background: #fff;
            border: 2px solid #e2e8f0;
            border-radius: 8px;
            padding: 6px;
            margin: 4px 0;
        }
        .stub-qr img {
            width: 90px;
            height: 90px;
            display: block;
        }

        /* Status badge kecil */
        .stub-status {
            display: inline-flex;
            align-items: center;
            gap: 3px;
            font-size: 0.55rem;
            font-weight: 700;
            padding: 3px 8px;
            border-radius: 20px;
            text-align: center;
        }
        .s-paid      { background: #dcfce7; color: #16a34a; }
        .s-used      { background: #f1f5f9; color: #64748b; }
        .s-pending   { background: #fef9c3; color: #ca8a04; }
        .s-cancelled { background: #fee2e2; color: #dc2626; }

        /* TERPAKAI stamp */
        .stamp-used {
            position: absolute;
            top: 50%; left: 42%;
            transform: translate(-50%, -50%) rotate(-25deg);
            font-size: 1rem;
            font-weight: 900;
            color: rgba(100,116,139,0.3);
            border: 3px solid rgba(100,116,139,0.25);
            padding: 3px 8px;
            border-radius: 6px;
            letter-spacing: 2px;
            pointer-events: none;
            white-space: nowrap;
        }

        /* ── Aksi bawah tiket ──────────────────────────────────────── */
        .ticket-actions {
            display: flex;
            gap: 10px;
            margin-top: 16px;
        }
        .btn-ticket {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            padding: 10px;
            border-radius: 12px;
            font-size: 0.8rem;
            font-weight: 600;
            cursor: pointer;
            border: none;
            text-decoration: none;
            transition: opacity 0.2s;
        }
        .btn-ticket:hover { opacity: 0.85; }
        .btn-primary-t  { background: linear-gradient(to right, #0ea5e9, #10b981); color: #fff; }
        .btn-outline-t  { background: rgba(255,255,255,0.08); color: #fff; border: 1px solid rgba(255,255,255,0.2); }

        .ticket-footer {
            text-align: center;
            margin-top: 20px;
            color: rgba(255,255,255,0.45);
            font-size: 0.7rem;
            line-height: 1.6;
        }

        /* Banner tanggal berlaku di atas tiket */
        .info-banner {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            background: rgba(255, 255, 255, 0.07);
            border: 1px solid rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(8px);
            border-radius: 12px;
            padding: 10px 18px;
            margin-bottom: 12px;
            text-align: center;
        }
        .info-banner-text {
            font-size: 0.78rem;
            color: rgba(255,255,255,0.75);
            font-weight: 500;
        }
        .info-banner-date {
            font-size: 0.85rem;
            font-weight: 700;
            color: #fff;
        }

        @media print {
            body { background: #fff; padding: 0; }
            .ticket-actions, .ticket-footer { display: none; }
        }

        @media (max-width: 400px) {
            .info-kode { 
                font-size: 0.5rem; 
                margin-top: 22px; /* <--- Tambahkan ini: sesuaikan posisinya (turunkan atau naikkan nilainya) */
                margin-left: -16px; /* <--- Tambahkan ini: sesuaikan posisinya (geser kiri/kanan) */
            }
            .info-value  { font-size: 0.55rem; }
            .stub-qr img { width: 72px; height: 72px; }
        }

    </style>
</head>
<body>

@php
    /** Pilih background gambar desain tiket berdasarkan slug wisata & jenis tiket */
    $slug        = $tiket->wisata->slug ?? '';
    $isCamping   = ($tiket->camping ?? '') === 'Ya';
    $slugsCurug  = \App\Models\Wisata::SLUGS_CURUG_CIBAREBEUY;          // ['curug-cibarebeuy','curug-cibareubeuy']
    $slugsBukit  = ['bukit-panineunganspot-foto', 'bukit-panineungan'];  // slug aktual DB
    $slugsPuncak = ['puncak-pasir-ipis', 'pasir-ipis', 'tracking-pasir-ipis']; // slug aktual DB

    if (in_array($slug, $slugsCurug)) {
        // Curug: bedakan tiket Kunjungan dan Camping
        $bgImg = $isCamping
            ? asset('images/Tiket_Curug_Camping.png')
            : asset('images/Tiket_Curug_Kunjungan.png');
    } elseif (in_array($slug, $slugsBukit)) {
        $bgImg = asset('images/Tiket_Bukit.png');
    } elseif (in_array($slug, $slugsPuncak)) {
        $bgImg = asset('images/Tiket_Puncak.png');
    } else {
        // Fallback: cocokkan berdasarkan kata kunci nama wisata
        $namaLower = strtolower($tiket->wisata->nama ?? '');
        if (str_contains($namaLower, 'curug')) {
            $bgImg = $isCamping
                ? asset('images/Tiket_Curug_Camping.png')
                : asset('images/Tiket_Curug_Kunjungan.png');
        } elseif (str_contains($namaLower, 'bukit')) {
            $bgImg = asset('images/Tiket_Bukit.png');
        } elseif (str_contains($namaLower, 'puncak') || str_contains($namaLower, 'pasir')) {
            $bgImg = asset('images/Tiket_Puncak.png');
        } else {
            $bgImg = asset('images/Tiket_Curug_Kunjungan.png'); // default
        }
    }

    $qrSrc     = '';  // tidak digunakan di overlay
@endphp

<div class="ticket-wrapper">

    {{-- ── BANNER TANGGAL BERLAKU ──────────────────────────── --}}
    <div class="info-banner">
        <div>
            <div class="info-banner-text">Tiket berlaku untuk tanggal</div>
            <div class="info-banner-date">{{ \Carbon\Carbon::parse($tiket->tanggal_berkunjung)->translatedFormat('d F Y') }}</div>
        </div>
    </div>

    {{-- ── TIKET ────────────────────────────────────────────────── --}}
    <div class="ticket-card" id="ticket-card">

        {{-- Gambar desain tiket asli sebagai latar --}}
        <img src="{{ $bgImg }}" alt="Desain Tiket {{ $tiket->wisata->nama }}" class="ticket-bg-img" draggable="false">

        {{-- Info overlay di atas area stub (kiri) --}}
        <div class="ticket-info-overlay">

            {{-- Kode Tiket --}}
            <div class="info-kode">{{ $tiket->kode_tiket }}</div>

            {{-- Stempel Terpakai --}}
            @if($tiket->status === 'used')
                <div class="stamp-used" style="color: rgba(220, 38, 38, 0.6); border-color: rgba(220, 38, 38, 0.4);">TERPAKAI</div>
            @endif
        </div>

    </div>

    {{-- Tombol Aksi --}}
    @if($tiket->status === 'paid')
    <div class="ticket-actions">
        <button class="btn-ticket btn-outline-t" onclick="window.print()">
            <i class="bi bi-printer"></i> Cetak
        </button>
        <a href="{{ route('pengunjung.tiket.qrcode', $tiket) }}?download=1" class="btn-ticket btn-primary-t">
            <i class="bi bi-download"></i> Unduh QR
        </a>
    </div>
    @endif

    <div class="ticket-footer">
        <p>Harap simpan tiket ini. Berlaku untuk tanggal kunjungan yang tertera.</p>
        <p>© {{ date('Y') }} SI-ASIH · BUMDes Cipta Asih Desa Cibeusi</p>
    </div>

</div>

</body>
</html>
