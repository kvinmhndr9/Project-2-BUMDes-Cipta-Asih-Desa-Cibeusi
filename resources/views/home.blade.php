@extends('layouts.app')

@section('title', 'Beranda')

@push('hero')
{{-- ===================== HERO SECTION (Full-Width, di luar container) ===================== --}}

<section class="hero-section position-relative overflow-hidden" style="z-index: 1;">

    {{-- Video Background: Desktop --}}
    <video class="hero-video d-none d-md-block"
        autoplay muted loop playsinline preload="auto">
        <source src="https://res.cloudinary.com/dgwu1dpep/video/upload/v1780989758/WhatsApp_Video_2026-05-31_at_14.19.37_ndyiam.mp4" type="video/mp4">
    </video>

    {{-- Video Background: Mobile --}}
    <video class="hero-video d-md-none"
        autoplay muted loop playsinline preload="auto">
        <source src="https://res.cloudinary.com/dgwu1dpep/video/upload/v1778514113/background-siasih-mobile_z7f8ph.mp4" type="video/mp4">
    </video>

    {{-- Floating Particles --}}
    <div class="hero-particle"></div>
    <div class="hero-particle"></div>
    <div class="hero-particle"></div>
    <div class="hero-particle"></div>
    <div class="hero-particle"></div>

    {{-- Gradient Overlay (lebih gelap agar teks terbaca) --}}
    <div class="position-absolute top-0 start-0 w-100 h-100"
        style="background: linear-gradient(180deg, rgba(0,10,8,0.30) 0%, rgba(0,20,15,0.55) 45%, rgba(0,8,6,0.82) 100%); z-index: 1;"></div>

    {{-- Teks & Tombol di bawah layar --}}
    <div class="hero-buttons position-absolute start-0 w-100 d-flex flex-column align-items-center gap-4" style="z-index: 2; bottom: 35%;">

        {{-- Blok Teks --}}
        <div class="hero-text-block">
            <h1 class="hero-title">
                <span class="font-sampurasun" style="font-size: 0.45em; display: block; margin-bottom: -0.1em; letter-spacing: -1px;">sampurasun</span>
                sobat wisata!
            </h1>
        </div>

        {{-- Tombol --}}
        <div class="d-flex flex-wrap justify-content-center gap-3" data-aos="fade-up" data-aos-delay="200">
        @auth
            <a href="{{ route('public.wisata.index') }}"
                class="btn fw-bold rounded-pill shadow-lg"
                style="background: #ffffff; color: #04009A; font-size: 1.4rem; padding: 1rem 3.5rem; border: none; transition: transform 0.2s, box-shadow 0.2s;"
                onmouseover="this.style.transform='scale(1.07)'; this.style.boxShadow='0 12px 40px rgba(62,219,240,0.5)'"
                onmouseout="this.style.transform='scale(1)'; this.style.boxShadow=''">Pesan Tiket Sekarang!
            </a>
        @else
            <a href="{{ route('login') }}"
                class="btn fw-bold rounded-pill"
                style="background: rgba(255,255,255,0.15); color: #fff; border: 2.5px solid rgba(255,255,255,0.75); backdrop-filter: blur(12px); font-size: 1.4rem; padding: 1rem 3.5rem; transition: all 0.2s;"
                onmouseover="this.style.background='rgba(255,255,255,0.28)'; this.style.transform='scale(1.05)'"
                onmouseout="this.style.background='rgba(255,255,255,0.15)'; this.style.transform='scale(1)'">
                Masuk
            </a>
            <a href="{{ route('register') }}"
                class="btn fw-bold rounded-pill shadow-lg"
                style="background: #ffffff; color: #04009A; font-size: 1.4rem; padding: 1rem 3.5rem; border: none; transition: transform 0.2s, box-shadow 0.2s;"
                onmouseover="this.style.transform='scale(1.07)'; this.style.boxShadow='0 12px 40px rgba(62,219,240,0.5)'"
                onmouseout="this.style.transform='scale(1)'; this.style.boxShadow=''">
                Daftar Sekarang!
            </a>
        @endauth
        </div>
    </div>
</section>
@endpush

@section('content')

{{-- ===================== TENTANG DESA SECTION ===================== --}}
<div class="row align-items-center mt-5 mb-5 pt-3 pb-3" data-aos="fade-up">
    <div class="col-lg-5 mb-4 mb-lg-0" data-aos="fade-right">
        <img src="https://res.cloudinary.com/dgwu1dpep/image/upload/v1779246617/ChatGPT_Image_May_20_2026_10_10_01_AM_qi2aqo.png" 
             alt="Tentang Desa Wisata Cibeusi" 
             class="img-fluid w-100" 
             style="border-radius: 24px; box-shadow: 0 16px 40px rgba(4,0,154,0.15); object-fit: cover; max-height: 550px;">
    </div>
    <div class="col-lg-7 px-lg-5" data-aos="fade-left">
        <h2 class="display-6 fw-bolder mb-4 text-gradient">
            Tentang Desa Wisata Cibeusi
        </h2>
        <p class="text-muted" style="font-size: 1.05rem; line-height: 1.8; text-align: justify;">
            Desa Cibeusi merupakan salah satu desa wisata unggulan di Kabupaten Subang yang menawarkan pesona alam yang masih asri, kekayaan budaya, dan keramahtamahan warga lokal. Terletak di kawasan pegunungan, desa ini dikelilingi oleh hamparan sawah, aliran sungai yang jernih, dan air terjun menawan yang menjadi daya tarik utama bagi para wisatawan.
        </p>
        <p class="text-muted mb-4" style="font-size: 1.05rem; line-height: 1.8; text-align: justify;">
            Melalui BUMDes Cipta Asih, Desa Cibeusi terus mengembangkan berbagai fasilitas pendukung mulai dari area camping, spot rekreasi, hingga produk UMKM khas yang dapat dinikmati. Kami berkomitmen untuk menjaga kelestarian lingkungan sekaligus meningkatkan perekonomian masyarakat sekitar melalui sektor pariwisata berkelanjutan.
        </p>
    </div>
</div>

{{-- ===================== WISATA SECTION ===================== --}}
<div class="text-center mt-5 mb-4 pt-2" data-aos="fade-up">
    <p class="fw-semibold mb-1 text-uppercase" style="letter-spacing: 2px; font-size: 0.82rem; color: #3EDBF0;">Destinasi Pilihan</p>
    <h2 class="display-6 fw-bolder mb-0">
        <span class="text-gradient">Jelajahi Tempat Wisata</span>
    </h2>
    <div class="mx-auto mt-2" style="width: 60px; height: 4px; background: linear-gradient(90deg, #04009A, #3EDBF0); border-radius: 99px;"></div>
</div>

<div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
    @foreach($wisata as $w)
    @php /** @var \App\Models\Wisata $w */ @endphp
    <div class="col" data-aos="fade-up" data-aos-delay="{{ $loop->iteration * 100 }}">
        <a href="{{ route('public.wisata.show', $w) }}" class="text-decoration-none text-dark d-block h-100">
            <div class="card h-100 border-0 overflow-hidden"
                style="border-radius: 18px; transition: transform 0.3s ease, box-shadow 0.3s ease; box-shadow: 0 4px 20px rgba(0,0,0,0.08);"
                onmouseover="this.style.transform='translateY(-8px)'; this.style.boxShadow='0 16px 40px rgba(0,0,0,0.16)'"
                onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 20px rgba(0,0,0,0.08)'">
                <div class="position-relative">
                    <img src="{{ $w->gambar_url }}" class="card-img-top" alt="{{ $w->nama }}" style="height: 210px; object-fit: cover;">
                    <div class="position-absolute top-0 start-0 w-100 h-100" style="background: linear-gradient(180deg, transparent 50%, rgba(0,0,0,0.45) 100%); border-radius: 18px 18px 0 0;"></div>
                </div>
                <div class="card-body d-flex flex-column p-4">
                    <h5 class="card-title fw-bold text-dark mb-2">{{ $w->nama }}</h5>
                    <p class="card-text text-muted mb-4 small">{{ Str::limit(strip_tags($w->deskripsi), 100) }}</p>
                    <div class="mt-auto d-flex justify-content-between align-items-center">
                        <span class="badge px-3 py-2 fw-semibold rounded-pill" style="background: linear-gradient(135deg, #04009A, #3EDBF0); color: white; font-size: 0.82rem;">
                            @if($w->hasCamping())
                                Rp {{ number_format((float) $w->harga_tiket, 0, ',', '.') }} – Rp {{ number_format($w->harga_camping_efektif, 0, ',', '.') }}
                            @else
                                Rp {{ number_format((float) $w->harga_tiket, 0, ',', '.') }}
                            @endif
                        </span>
                        <span class="fw-bold small" style="color: #3EDBF0;">Lihat Detail <i class="bi bi-arrow-right"></i></span>
                    </div>
                </div>
            </div>
        </a>
    </div>
    @endforeach
</div>

{{-- ===================== PRODUK KHAS SECTION ===================== --}}
<div class="text-center mt-5 mb-4 pt-4" data-aos="fade-up">
    <p class="fw-semibold mb-1 text-uppercase" style="letter-spacing: 2px; font-size: 0.82rem; color: #77ACF1;">Oleh-Oleh Unggulan</p>
    <h2 class="display-6 fw-bolder mb-0">
        <span class="text-gradient">Produk Khas Cibeusi</span>
    </h2>
    <div class="mx-auto mt-2" style="width: 60px; height: 4px; background: linear-gradient(90deg, #3EDBF0, #77ACF1); border-radius: 99px;"></div>
</div>

<div class="row row-cols-1 row-cols-md-2 row-cols-lg-4 g-4 mb-5">
    @forelse($produk as $p)
    @php /** @var \App\Models\ProdukKhas $p */ @endphp
    <div class="col" data-aos="zoom-in-up" data-aos-delay="{{ $loop->iteration * 100 }}">
        <a href="{{ route('public.produk-khas.show', $p) }}" class="text-decoration-none text-dark d-block h-100">
            <div class="card h-100 border-0 overflow-hidden"
                style="border-radius: 16px; transition: transform 0.3s ease, box-shadow 0.3s ease; box-shadow: 0 4px 20px rgba(0,0,0,0.08);"
                onmouseover="this.style.transform='translateY(-8px)'; this.style.boxShadow='0 16px 40px rgba(0,0,0,0.16)'"
                onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 20px rgba(0,0,0,0.08)'">
                <img src="{{ $p->gambar_url }}" class="card-img-top" alt="{{ $p->nama }}" style="height: 180px; object-fit: cover;">
                <div class="card-body d-flex flex-column p-3">
                    <h6 class="card-title fw-bold text-dark mb-1">{{ $p->nama }}</h6>
                    @if($p->wisata)
                        <p class="text-muted mb-2" style="font-size: 0.72rem;">
                            <i class="bi bi-geo-alt-fill me-1 text-success"></i>{{ $p->wisata->nama }}
                        </p>
                    @endif
                    <p class="card-text text-muted small mb-3">{{ Str::limit(strip_tags($p->keterangan), 80) }}</p>
                    <div class="mt-auto d-flex justify-content-end">
                        <span class="fw-bold small" style="color: #77ACF1;">Lihat Detail <i class="bi bi-arrow-right"></i></span>
                    </div>
                </div>
            </div>
        </a>
    </div>
    @empty
    <div class="col-12">
        <p class="text-muted small text-center">Belum ada produk khas yang ditampilkan.</p>
    </div>
    @endforelse
</div>

@include('layouts.partials.footer')
@endsection



