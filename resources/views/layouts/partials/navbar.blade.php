{{-- ══════════════════════════════════════════════
     NAVBAR — SI-ASIH Public Layout
     Drawer HTML & script ada di akhir app.blade.php
══════════════════════════════════════════════ --}}
<nav class="navbar navbar-expand-lg navbar-dark navbar-app" id="mainNavbar">
    <div class="container">
        @php
            $brandUrl = route('home');
            if(auth()->check()) {
                if(auth()->user()->isAdmin()) $brandUrl = route('admin.dashboard');
                elseif(auth()->user()->isPengelolaBumdes()) $brandUrl = route('pengelola.dashboard');
            }
        @endphp
        <a class="navbar-brand d-flex align-items-center gap-2" href="{{ $brandUrl }}">
            <img src="{{ asset('images/logo-cibeusi.png') }}" onerror="this.style.display='none';" alt="Logo Cibeusi" width="42" height="42" class="rounded-circle object-fit-cover shadow-sm border border-2 border-white bg-white">
            <span class="fs-5 fw-bolder text-white" style="letter-spacing: 1.5px; font-family:'Poppins',sans-serif; text-shadow: 2px 2px 5px rgba(0,0,0,0.2);">SI-ASIH</span>
        </a>

        {{-- Hamburger → opens right drawer (drawer ada di akhir app.blade.php) --}}
        <button id="btnOpenDrawer" type="button" class="navbar-toggler border-0 d-lg-none" aria-label="Buka Menu">
            <span class="navbar-toggler-icon"></span>
        </button>

        {{-- Desktop Nav (lg+) --}}
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto mb-2 mb-lg-0 me-lg-3 align-items-center">
                @if(!auth()->check() || auth()->user()->isPengunjung())
                <li class="nav-item">
                    <a class="nav-link text-white fs-6 px-3 fw-semibold" href="{{ route('home') }}">Beranda</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white fs-6 px-3 fw-semibold" href="{{ route('public.wisata.index') }}">Wisata</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white fs-6 px-3 fw-semibold" href="{{ route('public.produk-khas.index') }}">Produk Khas</a>
                </li>
                @endif

                @guest
                <li class="nav-item ms-lg-3">
                    <a href="{{ route('login') }}" class="btn btn-outline-light px-4 rounded-pill fw-bold" style="border-width: 2px;">Masuk</a>
                </li>
                <li class="nav-item ms-lg-2">
                    <a href="{{ route('register') }}" class="btn btn-light px-4 rounded-pill fw-bold shadow-sm" style="color: #04009A;">Daftar</a>
                </li>
                @endguest
            </ul>

            @auth
            {{-- Desktop dropdown --}}
            <ul class="navbar-nav d-none d-lg-flex align-items-center ms-lg-3">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle text-white fw-bold d-flex align-items-center gap-2"
                       href="#" id="navbarDropdownDesktop" role="button"
                       data-bs-toggle="dropdown" aria-expanded="false">
                        @if(auth()->user()->avatar)
                            <img src="{{ auth()->user()->avatar }}" onerror="this.onerror=null; this.src='/images/default-avatar.svg';" alt="Avatar" class="user-avatar-img"
                                 style="width:28px;height:28px;border-radius:50%;object-fit:cover;border:2px solid rgba(255,255,255,0.6);">
                        @else
                            <i class="bi bi-person-circle fs-5"></i>
                        @endif
                        {{ auth()->user()->name }}
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end border-0 shadow-sm rounded-4 mt-2"
                        aria-labelledby="navbarDropdownDesktop">
                        @if(auth()->user()->isPengunjung())
                        <li><a class="dropdown-item px-4 py-2" href="{{ route('pengunjung.profil.index') }}">Profil</a></li>
                        <li><a class="dropdown-item px-4 py-2" href="{{ route('pengunjung.tiket.my') }}">Tiket Saya</a></li>
                        <li><a class="dropdown-item px-4 py-2" href="{{ route('public.wisata.index') }}">Pesan Tiket</a></li>
                        <li><a class="dropdown-item px-4 py-2" href="{{ route('password.edit') }}">Ubah Kata Sandi</a></li>
                        @elseif(auth()->user()->isAdmin())
                        <li><a class="dropdown-item px-4 py-2" href="{{ route('admin.dashboard') }}">Dashboard Admin</a></li>
                        @elseif(auth()->user()->isPengelolaBumdes())
                        <li><a class="dropdown-item px-4 py-2" href="{{ route('pengelola.dashboard') }}">Dashboard Pengelola</a></li>
                        @endif
                        <li><hr class="dropdown-divider opacity-25 mx-3"></li>
                        <li>
                            <form action="{{ route('logout') }}" method="post">
                                @csrf
                                <button type="submit" class="dropdown-item px-4 py-2 text-danger fw-bold">Logout</button>
                            </form>
                        </li>
                    </ul>
                </li>
            </ul>
            @endauth
        </div>
    </div>
</nav>
