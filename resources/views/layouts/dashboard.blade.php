<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/png" href="{{ asset('images/logo-cibeusi.png') }}">
    <title>@yield('title', 'Dashboard') - SI-ASIH</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
    @stack('styles')
</head>
<body>

{{-- ── Overlay untuk mobile ── --}}
<div class="sidebar-overlay" id="sidebarOverlay"></div>

<div class="dash-wrapper">

    {{-- ══════════════════════════════════
         SIDEBAR
    ══════════════════════════════════ --}}
    <aside class="dash-sidebar" id="dashSidebar">

        {{-- Brand / Logo --}}
        <a class="sidebar-brand" href="{{ auth()->user()->isAdmin() ? route('admin.dashboard') : route('pengelola.dashboard') }}">
            <img src="{{ asset('images/logo-cibeusi.png') }}"
                 onerror="this.style.display='none';"
                 alt="Logo SI-ASIH">
            <span>
                <span class="brand-text">SI-ASIH</span>
                <span class="brand-sub">BUMDes Cipta Asih</span>
            </span>
        </a>

        {{-- User Info --}}
        <div class="sidebar-user">
            @if(auth()->user()->avatar)
                <img src="{{ auth()->user()->avatar }}" onerror="this.onerror=null; this.src='/images/default-avatar.svg';" alt="Avatar" class="user-avatar-img">
            @else
                <div class="user-avatar-icon d-flex align-items-center justify-content-center bg-white bg-opacity-25">
                    <i class="bi bi-person-fill text-white fs-5"></i>
                </div>
            @endif
            <div>
                <div class="user-name">{{ auth()->user()->name }}</div>
                @php
                    $roleLabel = auth()->user()->isAdmin() ? 'Admin' : (auth()->user()->isPengelolaBumdes() ? 'Pengelola BUMDes' : '');
                @endphp
                @if($roleLabel && strcasecmp(trim(auth()->user()->name), trim($roleLabel)) !== 0)
                    <div class="user-role">{{ $roleLabel }}</div>
                @endif
            </div>
        </div>

        {{-- Navigation Menu --}}
        <nav class="sidebar-nav">
            @if(auth()->user()->isAdmin())
                {{-- ── Menu Admin ── --}}
                <div class="sidebar-section-label">Menu Utama</div>

                <a href="{{ route('admin.dashboard') }}"
                   class="sidebar-nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                    <i class="bi bi-speedometer2"></i> Dashboard
                </a>

                <div class="sidebar-section-label">Validasi Tiket</div>

                <a href="{{ route('admin.validasi.index') }}"
                   class="sidebar-nav-link {{ request()->routeIs('admin.validasi.*') ? 'active' : '' }}">
                    <i class="bi bi-qr-code-scan"></i> Scan QR Tiket
                </a>

                <a href="{{ route('admin.history-validasi') }}"
                   class="sidebar-nav-link {{ request()->routeIs('admin.history-validasi') ? 'active' : '' }}">
                    <i class="bi bi-clock-history"></i> History Validasi
                </a>

                <div class="sidebar-section-label">Laporan</div>

                <a href="{{ route('admin.laporan') }}"
                   class="sidebar-nav-link {{ request()->routeIs('admin.laporan') ? 'active' : '' }}">
                    <i class="bi bi-file-earmark-bar-graph"></i> Laporan Penjualan
                </a>

                <div class="sidebar-section-label">Akun</div>

                <a href="{{ route('password.edit') }}"
                   class="sidebar-nav-link {{ request()->routeIs('password.edit') ? 'active' : '' }}">
                    <i class="bi bi-key"></i> Ubah Kata Sandi
                </a>

            @elseif(auth()->user()->isPengelolaBumdes())
                {{-- ── Menu Pengelola ── --}}
                <div class="sidebar-section-label">Menu Utama</div>

                <a href="{{ route('pengelola.dashboard') }}"
                   class="sidebar-nav-link {{ request()->routeIs('pengelola.dashboard') ? 'active' : '' }}">
                    <i class="bi bi-speedometer2"></i> Dashboard
                </a>

                <div class="sidebar-section-label">Laporan</div>

                <a href="{{ route('pengelola.laporan.index') }}"
                   class="sidebar-nav-link {{ request()->routeIs('pengelola.laporan.*') ? 'active' : '' }}">
                    <i class="bi bi-file-earmark-bar-graph"></i> Laporan Penjualan
                </a>

                <div class="sidebar-section-label">Kelola Data</div>

                <a href="{{ route('pengelola.wisata.index') }}"
                   class="sidebar-nav-link {{ request()->routeIs('pengelola.wisata.*') ? 'active' : '' }}">
                    <i class="bi bi-tree"></i> Kelola Wisata
                </a>

                <a href="{{ route('pengelola.produk-khas.index') }}"
                   class="sidebar-nav-link {{ request()->routeIs('pengelola.produk-khas.*') ? 'active' : '' }}">
                    <i class="bi bi-bag-heart"></i> Produk Khas
                </a>

                <a href="{{ route('pengelola.akun-admin.index') }}"
                   class="sidebar-nav-link {{ request()->routeIs('pengelola.akun-admin.*') ? 'active' : '' }}">
                    <i class="bi bi-people"></i> Kelola Akun Admin
                </a>

                <div class="sidebar-section-label">Akun</div>

                <a href="{{ route('password.edit') }}"
                   class="sidebar-nav-link {{ request()->routeIs('password.edit') ? 'active' : '' }}">
                    <i class="bi bi-key"></i> Ubah Kata Sandi
                </a>
            @endif
        </nav>

        {{-- Logout --}}
        <div class="sidebar-footer">
            <form action="{{ route('logout') }}" method="post">
                @csrf
                <button type="submit" class="btn-logout">
                    <i class="bi bi-box-arrow-left"></i> Logout
                </button>
            </form>
        </div>

    </aside>

    {{-- ══════════════════════════════════
         MAIN CONTENT
    ══════════════════════════════════ --}}
    <div class="dash-main">

        {{-- Topbar (hanya tombol toggle mobile + judul halaman) --}}
        <header class="dash-topbar">
            <div class="topbar-left">
                {{-- Toggle button (mobile only) --}}
                <button class="btn-sidebar-toggle" id="btnSidebarToggle" aria-label="Toggle Sidebar">
                    <i class="bi bi-list"></i>
                </button>
                <span class="topbar-page-title">@yield('title', 'Dashboard')</span>
            </div>
        </header>

        {{-- Area Konten Utama (Flash Alerts + Content disatukan) --}}
        <main class="dash-content">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show shadow-sm border-0 mb-4" role="alert" data-turbo-cache="false">
                    <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show shadow-sm border-0 mb-4" role="alert" data-turbo-cache="false">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            @if(session('info'))
                <div class="alert alert-info alert-dismissible fade show shadow-sm border-0 mb-4" role="alert" data-turbo-cache="false">
                    <i class="bi bi-info-circle-fill me-2"></i>{{ session('info') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @yield('content')
        </main>

    </div>{{-- /.dash-main --}}

</div>{{-- /.dash-wrapper --}}

<script>
(function () {
    var sidebar  = document.getElementById('dashSidebar');
    var overlay  = document.getElementById('sidebarOverlay');
    var btnToggle = document.getElementById('btnSidebarToggle');

    function openSidebar() {
        sidebar.classList.add('sidebar-open');
        overlay.classList.add('active');
        document.body.style.overflow = 'hidden';
    }
    function closeSidebar() {
        sidebar.classList.remove('sidebar-open');
        overlay.classList.remove('active');
        document.body.style.overflow = '';
    }

    if (btnToggle) {
        btnToggle.addEventListener('click', function () {
            sidebar.classList.contains('sidebar-open') ? closeSidebar() : openSidebar();
        });
    }
    if (overlay) {
        overlay.addEventListener('click', closeSidebar);
    }

    // Auto-close sidebar on link click (mobile)
    if (sidebar) {
        sidebar.querySelectorAll('.sidebar-nav-link').forEach(function (link) {
            link.addEventListener('click', function () {
                if (window.innerWidth < 992) closeSidebar();
            });
        });
    }

    // Mobile Table Card data-label injection
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('.table-responsive table').forEach(function (table) {
            var headers = [];
            table.querySelectorAll('thead th').forEach(function (th) {
                headers.push(th.innerText.trim());
            });
            table.querySelectorAll('tbody tr').forEach(function (tr) {
                tr.querySelectorAll('td').forEach(function (td, index) {
                    if (headers[index] && !td.hasAttribute('colspan')) {
                        td.setAttribute('data-label', headers[index]);
                    }
                });
            });
        });

        // Auto-dismiss flash alerts after 8 detik
        setTimeout(function () {
            document.querySelectorAll('.alert:not(.alert-permanent)').forEach(function (alert) {
                if (typeof bootstrap !== 'undefined') {
                    bootstrap.Alert.getOrCreateInstance(alert).close();
                } else {
                    alert.classList.remove('show');
                    setTimeout(function () { alert.remove(); }, 150);
                }
            });
        }, 8000);
    });

    // Bersihkan semua sisa modal Bootstrap sebelum Turbo menyimpan snapshot halaman dashboard
    document.addEventListener('turbo:before-cache', function() {
        document.querySelectorAll('.modal.show').forEach(function(modalEl) {
            var instance = bootstrap.Modal.getInstance(modalEl);
            if (instance) instance.hide();
        });
        document.querySelectorAll('.modal-backdrop').forEach(function(el) { el.remove(); });
        document.body.classList.remove('modal-open');
        document.body.style.removeProperty('overflow');
        document.body.style.removeProperty('padding-right');
    });
})();
</script>

@stack('modals')
@stack('scripts')
</body>
</html>
