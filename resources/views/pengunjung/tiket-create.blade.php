@extends('layouts.app')

@section('title', 'Pesan Tiket - ' . $wisata->nama)

@push('styles')
<style>
/* ─── Wrapper ─────────────────────────────────── */
.tiket-wrapper {
    display: flex;
    justify-content: center;
    padding: 1rem 1rem 2rem;
}

/* ─── Card ────────────────────────────────────── */
.tiket-card {
    width: 100%;
    max-width: 620px;
    background: #fff;
    border-radius: 18px;
    box-shadow: 0 6px 32px rgba(4,0,154,0.13);
    overflow: hidden;
}

/* ─── Header ──────────────────────────────────── */
.tiket-card-header {
    background: linear-gradient(135deg, #02006B 0%, #04009A 55%, #0a3fc0 100%);
    padding: 1.1rem 1.5rem 1rem;
    position: relative;
    overflow: hidden;
}
.tiket-card-header::after {
    content: '';
    position: absolute;
    width: 180px; height: 180px;
    background: radial-gradient(circle, rgba(62,219,240,0.18) 0%, transparent 70%);
    top: -50px; right: -50px;
    pointer-events: none;
}
.tiket-badge {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    background: rgba(255,255,255,0.15);
    border: 1px solid rgba(255,255,255,0.25);
    border-radius: 50px;
    padding: 2px 10px;
    font-size: 0.68rem;
    font-weight: 600;
    color: #C0FEFC;
    margin-bottom: 6px;
}
.tiket-card-header h1 {
    font-size: 1.15rem;
    font-weight: 700;
    color: #fff;
    margin: 0 0 3px;
    line-height: 1.3;
}
.tiket-breadcrumb {
    font-size: 0.72rem;
    color: rgba(255,255,255,0.5);
    margin: 0;
}
.tiket-breadcrumb a {
    color: rgba(255,255,255,0.5);
    text-decoration: none;
}
.gradient-bar {
    height: 3px;
    background: linear-gradient(90deg, #04009A, #77ACF1, #3EDBF0, #C0FEFC);
}

/* ─── Body ────────────────────────────────────── */
.tiket-card-body {
    padding: 1.25rem 1.5rem 1.25rem;
}

/* ─── Section title ───────────────────────────── */
.sec-title {
    font-size: 0.65rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 1.5px;
    color: #04009A;
    display: flex;
    align-items: center;
    gap: 6px;
    margin-bottom: 0.75rem;
}
.sec-title::after {
    content: '';
    flex: 1;
    height: 1px;
    background: linear-gradient(90deg, rgba(4,0,154,0.2), transparent);
}

/* ─── Label & input ───────────────────────────── */
.field-label {
    font-size: 0.78rem;
    font-weight: 600;
    color: #374151;
    margin-bottom: 5px;
    display: block;
}
.field-input {
    width: 100%;
    padding: 9px 12px;
    border: 1.5px solid #D1D5DB;
    border-radius: 9px;
    font-size: 0.87rem;
    color: #111827;
    background: #F9FAFB;
    transition: border-color .2s, box-shadow .2s, background .2s;
    -webkit-appearance: none;
    appearance: none;
}
.field-input:focus {
    outline: none;
    border-color: #04009A;
    background: #fff;
    box-shadow: 0 0 0 3px rgba(4,0,154,0.1);
}
.field-input.is-invalid { border-color:#EF4444; background:#FEF2F2; }
.invalid-msg {
    font-size: 0.73rem;
    color: #DC2626;
    margin-top: 4px;
}

/* ─── Qty stepper ─────────────────────────────── */
.qty-wrap {
    display: flex;
    align-items: center;
    border: 1.5px solid #D1D5DB;
    border-radius: 9px;
    background: #F9FAFB;
    transition: border-color .2s, box-shadow .2s;
    width: 100%;
}
.qty-wrap:focus-within {
    border-color: #04009A;
    background: #fff;
    box-shadow: 0 0 0 3px rgba(4,0,154,0.1);
}
.qty-btn {
    min-width: 38px;
    width: 38px;
    height: 42px;
    flex-shrink: 0;
    background: transparent;
    border: none;
    font-size: 1.1rem;
    font-weight: 700;
    color: #04009A;
    cursor: pointer;
    transition: background .15s;
    display: flex;
    align-items: center;
    justify-content: center;
    -webkit-user-select: none;
    user-select: none;
    padding: 0;
}
.qty-btn:hover { background: rgba(4,0,154,0.08); }
.qty-btn:first-child { border-radius: 7px 0 0 7px; }
.qty-btn:last-child  { border-radius: 0 7px 7px 0; }
.qty-num {
    flex: 1;
    min-width: 0;
    border: none;
    background: transparent;
    text-align: center;
    font-size: 0.95rem;
    font-weight: 700;
    color: #111827;
    padding: 9px 4px;
    border-left: 1px solid #E5E7EB;
    border-right: 1px solid #E5E7EB;
}
.qty-num:focus { outline: none; }
.qty-num::-webkit-outer-spin-button,
.qty-num::-webkit-inner-spin-button { -webkit-appearance: none; appearance: none; margin: 0; }
.qty-num[type=number] { -moz-appearance: textfield; appearance: textfield; }

/* ─── Camping options ─────────────────────────── */
.option-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 8px;
}
.option-input { display: none; }
.option-label {
    border: 1.5px solid #D1D5DB;
    border-radius: 10px;
    padding: 10px 11px;
    cursor: pointer;
    transition: all .2s;
    background: #F9FAFB;
    display: flex;
    align-items: center;
    gap: 9px;
    user-select: none;
}
.option-label:hover { border-color: #77ACF1; background: #EFF6FF; }
.option-input:checked + .option-label {
    border-color: #04009A;
    background: linear-gradient(135deg, #EFF6FF, #F0F9FF);
    box-shadow: 0 0 0 3px rgba(4,0,154,0.1);
}
.option-icon {
    width: 36px; height: 36px;
    border-radius: 9px;
    display: flex; align-items: center; justify-content: center;
    font-size: 1.05rem;
    flex-shrink: 0;
    transition: all .2s;
}
.icon-visit { background:#DBEAFE; color:#2563EB; }
.icon-camp  { background:#DCFCE7; color:#16A34A; }
.option-input:checked + .option-label .icon-visit { background:#2563EB; color:#fff; }
.option-input:checked + .option-label .icon-camp  { background:#16A34A; color:#fff; }
.option-name  { font-size: 0.82rem; font-weight: 700; color: #111827; line-height: 1.2; }
.option-price { font-size: 0.7rem; color: #6B7280; margin-top: 1px; }

/* ─── Parkir ──────────────────────────────────── */
.parkir-box {
    background: #F0F4FF;
    border: 1.5px solid #C7D7FD;
    border-radius: 10px;
    padding: 10px 13px;
}
.parkir-note {
    font-size: 0.72rem; font-weight: 600; color: #4338CA;
    margin-bottom: 7px;
    display: flex; align-items: center; gap: 5px;
}
.parkir-row {
    display: flex; align-items: center; gap: 7px;
    font-size: 0.76rem; color: #374151;
    padding: 4px 0;
    border-bottom: 1px solid rgba(99,102,241,0.08);
}
.parkir-row:last-child { border-bottom: none; }
.parkir-row i { color:#6366F1; width:13px; text-align:center; flex-shrink:0; }
.parkir-row strong { color:#111827; }

/* ─── Summary ─────────────────────────────────── */
.summary-box {
    background: linear-gradient(135deg, #02006B 0%, #04009A 55%, #0a3fc0 100%);
    border-radius: 12px;
    padding: 1rem 1.25rem;
    position: relative; overflow: hidden;
}
.summary-box::before {
    content: '';
    position: absolute;
    width: 130px; height: 130px;
    background: radial-gradient(circle, rgba(62,219,240,0.15) 0%, transparent 70%);
    top: -35px; right: -35px;
    pointer-events: none;
}
.summary-row {
    display: flex; justify-content: space-between; align-items: center;
    font-size: 0.8rem; color: rgba(255,255,255,0.7);
    padding: 3px 0;
    position: relative;
}
.summary-total-row {
    display: flex; justify-content: space-between; align-items: flex-end;
    padding-top: 10px; margin-top: 7px;
    border-top: 1px solid rgba(255,255,255,0.15);
    position: relative;
}
.summary-total-label {
    font-size: 0.65rem; text-transform: uppercase;
    letter-spacing: 1px; color: rgba(255,255,255,0.5);
    margin-bottom: 1px;
}
.summary-total-amount {
    font-size: 1.4rem; font-weight: 800;
    color: #3EDBF0; letter-spacing: -0.5px;
}
.summary-via-label { font-size: 0.68rem; color: rgba(62,219,240,0.85); font-weight: 600; }
.summary-via-sub   { font-size: 0.65rem; color: rgba(255,255,255,0.4); margin-top: 1px; }

/* ─── Tombol ──────────────────────────────────── */
.action-row { display: flex; gap: 8px; margin-top: 1rem; }
.btn-batal {
    flex: 1;
    display: flex; align-items: center; justify-content: center; gap: 5px;
    padding: 10px 14px;
    border: 1.5px solid #D1D5DB; border-radius: 10px;
    background: transparent; color: #6B7280;
    font-size: 0.84rem; font-weight: 600;
    text-decoration: none; transition: all .2s; cursor: pointer;
}
.btn-batal:hover { border-color:#EF4444; color:#EF4444; background:#FEF2F2; }
.btn-pesan {
    flex: 2;
    display: flex; align-items: center; justify-content: center; gap: 6px;
    padding: 10px 18px;
    border: none; border-radius: 10px;
    background: linear-gradient(45deg, #04009A, #3EDBF0);
    color: #fff; font-size: 0.87rem; font-weight: 700;
    cursor: pointer; transition: all .25s;
    box-shadow: 0 4px 14px rgba(4,0,154,0.35);
}
.btn-pesan:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 22px rgba(4,0,154,0.45);
}
.btn-pesan:active { transform: translateY(0); }
.secure-note {
    text-align: center; font-size: 0.7rem;
    color: #9CA3AF; margin-top: 8px; margin-bottom: 0;
}

/* ─── Responsive ──────────────────────────────── */
@media (max-width: 480px) {
    .tiket-card-header { padding: .9rem 1.1rem .85rem; }
    .tiket-card-body   { padding: 1rem 1.1rem; }
    .tiket-card-header h1 { font-size: 1rem; }
}
</style>
@endpush

@section('content')
@php $isCurug = $wisata->hasCamping(); @endphp

<div class="tiket-wrapper">
    <div class="tiket-card" data-aos="fade-up">

        {{-- Header --}}
        <div class="tiket-card-header">
            <div class="tiket-badge">
                <i class="bi bi-ticket-perforated-fill"></i> Pemesanan Tiket
            </div>
            <h1>{{ $wisata->nama }}</h1>
            <p class="tiket-breadcrumb">
                <a href="{{ route('pengunjung.dashboard') }}">Wisata</a> &rsaquo; Pesan Tiket
            </p>
        </div>
        <div class="gradient-bar"></div>

        {{-- Body --}}
        <div class="tiket-card-body">
            <form action="{{ route('pengunjung.tiket.store') }}" method="post" id="form-tiket">
                @csrf
                <input type="hidden" name="id_wisata" value="{{ $wisata->id }}">

                {{-- 1. Jenis (khusus Curug) --}}
                @if($isCurug)
                <div class="mb-3">
                    <div class="sec-title"><i class="bi bi-tags-fill"></i> Jenis Kunjungan</div>
                    <div class="option-grid">
                        <input type="radio" class="option-input" name="camping"
                            id="opt-kunjungan" value="Tidak"
                            {{ old('camping') !== 'Ya' ? 'checked' : '' }}>
                        <label class="option-label" for="opt-kunjungan">
                            <div class="option-icon icon-visit"><i class="bi bi-person-walking"></i></div>
                            <div>
                                <div class="option-name">Kunjungan</div>
                                <div class="option-price">Rp {{ number_format($wisata->harga_tiket, 0, ',', '.') }} / tiket</div>
                            </div>
                        </label>
                        <input type="radio" class="option-input" name="camping"
                            id="opt-camping" value="Ya"
                            {{ old('camping') === 'Ya' ? 'checked' : '' }}>
                        <label class="option-label" for="opt-camping">
                            <div class="option-icon icon-camp"><i class="bi bi-fire"></i></div>
                            <div>
                                <div class="option-name">Camping</div>
                                <div class="option-price">Rp {{ number_format($wisata->harga_camping_efektif, 0, ',', '.') }} / tiket</div>
                            </div>
                        </label>
                    </div>
                    @error('camping')
                    <div class="invalid-msg"><i class="bi bi-exclamation-circle"></i> {{ $message }}</div>
                    @enderror
                </div>
                @endif

                {{-- 2. Jumlah & Tanggal (2 kolom) --}}
                <div class="mb-3">
                    <div class="sec-title"><i class="bi bi-calendar2-check-fill"></i> Detail Kunjungan</div>
                    <div class="row g-2">
                        <div class="col-5">
                            <label class="field-label" for="qty-num">Jumlah Tiket</label>
                            <div class="qty-wrap">
                                <button type="button" class="qty-btn" id="btn-minus" aria-label="Kurangi">
                                    <i class="bi bi-dash-lg"></i>
                                </button>
                                <input type="number" class="qty-num" id="qty-num"
                                    name="jumlah" value="{{ old('jumlah', 1) }}" min="1">
                                <button type="button" class="qty-btn" id="btn-plus" aria-label="Tambah">
                                    <i class="bi bi-plus-lg"></i>
                                </button>
                            </div>
                            @error('jumlah')
                            <div class="invalid-msg"><i class="bi bi-exclamation-circle"></i> {{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-7">
                            <label class="field-label" for="tgl-input">Tanggal Berkunjung</label>
                            <input type="date" id="tgl-input" name="tanggal_berkunjung"
                                class="field-input @error('tanggal_berkunjung') is-invalid @enderror"
                                value="{{ old('tanggal_berkunjung') }}"
                                min="{{ date('Y-m-d') }}">
                            @error('tanggal_berkunjung')
                            <div class="invalid-msg"><i class="bi bi-exclamation-circle"></i> {{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                {{-- 3. Info Parkir --}}
                <div class="mb-3">
                    <div class="sec-title"><i class="bi bi-p-circle-fill"></i> Info Parkir</div>
                    <div class="parkir-box">
                        <div class="parkir-note">
                            <i class="bi bi-info-circle-fill"></i> Biaya parkir dibayar di lokasi wisata
                        </div>
                        <div class="parkir-row">
                            <i class="bi bi-scooter"></i>
                            <span>Motor Kunjungan — <strong>Rp 10.000</strong>
                                <span style="color:#9CA3AF;">(termasuk helm &amp; barang)</span>
                            </span>
                        </div>
                        <div class="parkir-row">
                            <i class="bi bi-scooter"></i>
                            <span>Motor Camping — <strong>Rp 15.000</strong>
                                <span style="color:#9CA3AF;">(termasuk helm &amp; barang)</span>
                            </span>
                        </div>
                        <div class="parkir-row">
                            <i class="bi bi-car-front-fill"></i>
                            <span>Mobil Kunjungan — <strong>Rp 15.000</strong></span>
                        </div>
                        <div class="parkir-row">
                            <i class="bi bi-car-front-fill"></i>
                            <span>Mobil Camping — <strong>Rp 25.000</strong></span>
                        </div>
                    </div>
                </div>

                {{-- 4. Ringkasan --}}
                <div class="mb-3">
                    <div class="sec-title"><i class="bi bi-receipt-cutoff"></i> Ringkasan Pembayaran</div>
                    <div class="summary-box">
                        <div class="summary-row">
                            <span>Harga Tiket (<span id="s-qty">1</span>x)</span>
                            <span id="s-price" style="color:#fff;font-weight:600;">Rp 0</span>
                        </div>
                        <div class="summary-total-row">
                            <div>
                                <div class="summary-total-label">Total Pembayaran</div>
                                <div class="summary-total-amount" id="s-total">Rp 0</div>
                            </div>
                            <div style="text-align:right;">
                                <div class="summary-via-label">Via Midtrans</div>
                                <div class="summary-via-sub">Parkir dibayar di lokasi</div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Tombol --}}
                <div class="action-row">
                    <a href="{{ route('public.wisata.show', $wisata->slug) }}" class="btn-batal">
                        Batal
                    </a>
                    <button type="submit" class="btn-pesan">
                        Pesan Sekarang
                    </button>
                </div>
                <p class="secure-note">
                    <i class="bi bi-shield-check" style="color:#10B981;"></i>
                    Pembayaran aman diproses via Midtrans
                </p>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
@php
    $hargaCampingVal = $wisata->harga_camping_efektif;
    $isCurugJs       = $isCurug ? 'true' : 'false';
@endphp
<script>
(function () {
    var BASE_PRICE = parseInt("{{ (int) $wisata->harga_tiket }}", 10);
    var CAMP_PRICE = parseInt("{{ $hargaCampingVal }}", 10) || 0;
    var IS_CURUG   = "{{ $isCurugJs }}" === "true";

    function fmt(n) {
        return 'Rp ' + n.toLocaleString('id-ID');
    }
    function getPrice() {
        if (!IS_CURUG) return BASE_PRICE;
        var r = document.querySelector('input[name="camping"]:checked');
        return (r && r.value === 'Ya') ? CAMP_PRICE : BASE_PRICE;
    }
    function recalc() {
        var inp  = document.getElementById('qty-num');
        var sQty = document.getElementById('s-qty');
        var sPrc = document.getElementById('s-price');
        var sTot = document.getElementById('s-total');
        if (!inp || !sQty) return;
        var qty   = Math.max(1, parseInt(inp.value) || 1);
        var price = getPrice();
        sQty.textContent = qty;
        sPrc.textContent = fmt(price * qty);
        sTot.textContent = fmt(price * qty);
    }

    function init() {
        var form = document.getElementById('form-tiket');
        if (!form || form.dataset.qtyReady) return;
        form.dataset.qtyReady = '1';

        var inp     = document.getElementById('qty-num');
        var btnMin  = document.getElementById('btn-minus');
        var btnPlus = document.getElementById('btn-plus');
        if (!inp) return;

        /* Qty stepper */
        btnMin && btnMin.addEventListener('click', function () {
            var v = parseInt(inp.value) || 1;
            if (v > 1) { inp.value = v - 1; recalc(); }
        });
        btnPlus && btnPlus.addEventListener('click', function () {
            inp.value = (parseInt(inp.value) || 1) + 1;
            recalc();
        });
        inp.addEventListener('input', recalc);

        /* Camping toggle — listener pada radio (change+click) DAN label (click) */
        if (IS_CURUG) {
            document.querySelectorAll('input[name="camping"]').forEach(function (r) {
                r.addEventListener('change', recalc);
                r.addEventListener('click', recalc);
            });
            document.querySelectorAll('.option-label').forEach(function (lbl) {
                lbl.addEventListener('click', function () {
                    setTimeout(recalc, 0);
                });
            });
        }
        recalc();
    }

    document.addEventListener('turbo:load', init);
    setTimeout(init, 100);
})();
</script>
@endpush
