<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/png" href="{{ asset('images/logo-cibeusi.png') }}">
    <title>@yield('title', 'SI-ASIH') - BUMDes Cipta Asih</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,400;0,500;0,600;0,700;0,800;0,900;1,400&family=Pacifico&display=swap" rel="stylesheet">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
    @stack('styles')

</head>
<body class="d-flex flex-column min-vh-100" style="overflow-x: hidden;">
    {{-- Navbar di luar pageWrapper agar position:fixed tidak terhalang overflow:hidden --}}
    @include('layouts.partials.navbar')

    {{-- Wrapper: seluruh konten halaman dikunci di z-index rendah --}}
    <div id="pageWrapper" style="position:relative;z-index:1;display:flex;flex-direction:column;min-height:100vh;">

    @stack('hero')
    <main class="flex-grow-1 container pt-4 pb-4">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert" data-turbo-cache="false">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert" data-turbo-cache="false">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        @if(session('info'))
            <div class="alert alert-info alert-dismissible fade show" role="alert" data-turbo-cache="false">
                {{ session('info') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        
        @yield('content')
    </main>



    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        document.addEventListener("turbo:load", function() {
            // ── Navbar: transparan di atas hero video, solid setelah melewati hero ──
            (function () {
                var nav = document.getElementById('mainNavbar');
                if (!nav) return;
                // Bersihkan scroll listener lama jika ada
                if (window._siasihNavScroll) {
                    window.removeEventListener('scroll', window._siasihNavScroll);
                    window._siasihNavScroll = null;
                }
                var heroSection = document.querySelector('.hero-section');
                if (!heroSection) {
                    // Halaman non-hero: langsung solid biru
                    nav.classList.add('navbar-solid');
                    document.body.classList.add('no-hero-page');
                } else {
                    // Halaman hero: transparan di atas video, solid setelah scroll melewati hero
                    document.body.classList.remove('no-hero-page');
                    window._siasihNavScroll = function () {
                        var heroBottom = heroSection.offsetTop + heroSection.offsetHeight - nav.offsetHeight;
                        if (window.scrollY >= heroBottom) {
                            nav.classList.add('navbar-solid');
                        } else {
                            nav.classList.remove('navbar-solid');
                        }
                    };
                    window.addEventListener('scroll', window._siasihNavScroll, { passive: true });
                    window._siasihNavScroll(); // cek posisi awal
                }
            })();

            if (typeof AOS !== 'undefined') {
                AOS.init({
                    duration: 800,
                    once: true,
                    offset: 100,
                });
            }
            
            setTimeout(function() {
                var alerts = document.querySelectorAll('.alert:not(.alert-permanent)');
                alerts.forEach(function(alert) {
                    if (typeof bootstrap !== 'undefined') {
                        var bsAlert = bootstrap.Alert.getOrCreateInstance(alert);
                        bsAlert.close();
                    } else {
                        alert.classList.remove('show');
                        setTimeout(function() { alert.remove(); }, 150);
                    }
                });
            }, 10000); // 10 detik

            // Mobile Responsive Tables Logic
            document.querySelectorAll('.table-responsive table').forEach(function(table) {
                var headers = [];
                // Get header texts
                table.querySelectorAll('thead th').forEach(function(th) {
                    headers.push(th.innerText.trim());
                });
                
                // Apply to tbody rows
                table.querySelectorAll('tbody tr').forEach(function(tr) {
                    tr.querySelectorAll('td').forEach(function(td, index) {
                        if (headers[index] && !td.hasAttribute('colspan')) {
                            td.setAttribute('data-label', headers[index]);
                        }
                    });
                });

                // Apply to tfoot rows
                table.querySelectorAll('tfoot tr').forEach(function(tr) {
                    tr.querySelectorAll('td, th').forEach(function(td, index) {
                        if (headers[index] && td.tagName.toLowerCase() === 'td' && !td.hasAttribute('colspan')) {
                            td.setAttribute('data-label', headers[index]);
                        }
                    });
                });
            });
        });
    </script>



    </div>{{-- /#pageWrapper --}}

    {{-- ══ MODALS SLOT — di luar pageWrapper agar tidak terjebak stacking context z-index:1 ══ --}}
    @stack('modals')

    {{-- ══ RIGHT-SIDE DRAWER (di luar pageWrapper agar selalu di atas) ══ --}}
    <div id="navDrawerOverlay" style="display:none;position:fixed;inset:0;background:rgba(4,0,154,0.5);z-index:9998;will-change:opacity;"></div>

    {{-- Drawer --}}
    <div id="navDrawer" style="position:fixed;top:0;right:0;width:280px;max-width:85vw;height:100vh;height:100dvh;background:linear-gradient(180deg,#02006B 0%,#04009A 60%,#0600c0 100%);z-index:9999;transform:translateX(100%) translateZ(0);-webkit-transform:translateX(100%) translateZ(0);transition:transform 0.32s cubic-bezier(0.4,0,0.2,1);display:flex;flex-direction:column;box-shadow:-6px 0 30px rgba(4,0,154,0.3);">

        {{-- Header --}}
        <div style="padding:18px 20px 14px;border-bottom:1px solid rgba(255,255,255,0.1);display:flex;align-items:center;justify-content:space-between;flex-shrink:0;">
            <div class="d-flex align-items-center gap-2">
                <img src="{{ asset('images/logo-cibeusi.png') }}" onerror="this.style.display='none';" style="width:34px;height:34px;border-radius:50%;object-fit:cover;border:2px solid rgba(255,255,255,0.5);background:#fff;">
                <span style="font-family:'Poppins',sans-serif;font-size:1rem;font-weight:800;color:#fff;letter-spacing:1.5px;">SI-ASIH</span>
            </div>
            <button id="btnCloseDrawer" style="background:none;border:none;color:rgba(255,255,255,0.75);font-size:1.4rem;line-height:1;cursor:pointer;padding:4px 8px;border-radius:8px;transition:color 0.2s;">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>

        {{-- Nav Body --}}
        <nav style="flex:1;padding:8px 0;overflow-y:auto;">

            @auth
            {{-- ── 1. USER INFO ─────────────────────────────── --}}
            <div style="margin:10px 14px 4px;padding:12px 14px;background:rgba(255,255,255,0.08);border-radius:12px;display:flex;align-items:center;gap:10px;">
                @if(auth()->user()->avatar)
                    <img src="{{ auth()->user()->avatar }}" onerror="this.onerror=null; this.src='/images/default-avatar.svg';" class="user-avatar-img" style="width:36px;height:36px;border-radius:50%;object-fit:cover;border:2px solid #3EDBF0;flex-shrink:0;">
                @else
                    <div style="width:36px;height:36px;border-radius:50%;background:rgba(255,255,255,0.15);display:flex;align-items:center;justify-content:center;border:2px solid #77ACF1;flex-shrink:0;">
                        <i class="bi bi-person-fill" style="color:#fff;"></i>
                    </div>
                @endif
                <div style="min-width:0;">
                    <div style="font-family:'Poppins',sans-serif;font-size:0.85rem;font-weight:700;color:#fff;line-height:1.2;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ auth()->user()->name }}</div>
                    @php
                        $roleLabel = auth()->user()->isPengunjung() ? 'Pengunjung' : (auth()->user()->isAdmin() ? 'Admin' : (auth()->user()->isPengelolaBumdes() ? 'Pengelola BUMDes' : ''));
                    @endphp
                    @if($roleLabel && strcasecmp(trim(auth()->user()->name), trim($roleLabel)) !== 0)
                        <div style="font-size:0.7rem;color:#C0FEFC;margin-top:2px;">{{ $roleLabel }}</div>
                    @endif
                </div>
            </div>

            {{-- ── 2. MENU SAYA (Pengunjung) ────────────────── --}}
            @if(auth()->user()->isPengunjung())
            <div style="padding:10px 20px 4px;font-size:0.65rem;font-weight:700;color:rgba(255,255,255,0.4);text-transform:uppercase;letter-spacing:1.5px;margin-top:4px;">Menu Saya</div>
            <a href="{{ route('pengunjung.profil.index') }}" class="nav-drawer-link" style="display:flex;align-items:center;gap:12px;padding:10px 20px;color:rgba(255,255,255,0.9);font-family:'Poppins',sans-serif;font-size:0.88rem;font-weight:600;text-decoration:none;transition:all 0.18s;">
                <i class="bi bi-person-circle" style="width:20px;text-align:center;color:#77ACF1;"></i> Profil
            </a>
            <a href="{{ route('pengunjung.tiket.my') }}" class="nav-drawer-link" style="display:flex;align-items:center;gap:12px;padding:10px 20px;color:rgba(255,255,255,0.9);font-family:'Poppins',sans-serif;font-size:0.88rem;font-weight:600;text-decoration:none;transition:all 0.18s;">
                <i class="bi bi-ticket-perforated" style="width:20px;text-align:center;color:#77ACF1;"></i> Tiket Saya
            </a>
            <a href="{{ route('public.wisata.index') }}" class="nav-drawer-link" style="display:flex;align-items:center;gap:12px;padding:10px 20px;color:rgba(255,255,255,0.9);font-family:'Poppins',sans-serif;font-size:0.88rem;font-weight:600;text-decoration:none;transition:all 0.18s;">
                <i class="bi bi-bag-plus" style="width:20px;text-align:center;color:#77ACF1;"></i> Pesan Tiket
            </a>
            <a href="{{ route('password.edit') }}" class="nav-drawer-link" style="display:flex;align-items:center;gap:12px;padding:10px 20px;color:rgba(255,255,255,0.9);font-family:'Poppins',sans-serif;font-size:0.88rem;font-weight:600;text-decoration:none;transition:all 0.18s;">
                <i class="bi bi-key" style="width:20px;text-align:center;color:#77ACF1;"></i> Ubah Kata Sandi
            </a>
            @elseif(auth()->user()->isAdmin())
            <div style="padding:10px 20px 4px;font-size:0.65rem;font-weight:700;color:rgba(255,255,255,0.4);text-transform:uppercase;letter-spacing:1.5px;margin-top:4px;">Dashboard</div>
            <a href="{{ route('admin.dashboard') }}" class="nav-drawer-link" style="display:flex;align-items:center;gap:12px;padding:10px 20px;color:rgba(255,255,255,0.9);font-family:'Poppins',sans-serif;font-size:0.88rem;font-weight:600;text-decoration:none;transition:all 0.18s;">
                <i class="bi bi-speedometer2" style="width:20px;text-align:center;color:#77ACF1;"></i> Buka Dashboard
            </a>
            @elseif(auth()->user()->isPengelolaBumdes())
            <div style="padding:10px 20px 4px;font-size:0.65rem;font-weight:700;color:rgba(255,255,255,0.4);text-transform:uppercase;letter-spacing:1.5px;margin-top:4px;">Dashboard</div>
            <a href="{{ route('pengelola.dashboard') }}" class="nav-drawer-link" style="display:flex;align-items:center;gap:12px;padding:10px 20px;color:rgba(255,255,255,0.9);font-family:'Poppins',sans-serif;font-size:0.88rem;font-weight:600;text-decoration:none;transition:all 0.18s;">
                <i class="bi bi-speedometer2" style="width:20px;text-align:center;color:#77ACF1;"></i> Buka Dashboard
            </a>
            @endif
            @endauth

            {{-- ── 3. JELAJAHI ──────────────────────────────── --}}
            @if(!auth()->check() || auth()->user()->isPengunjung())
            <div style="padding:10px 20px 4px;font-size:0.65rem;font-weight:700;color:rgba(255,255,255,0.4);text-transform:uppercase;letter-spacing:1.5px;margin-top:4px;">Jelajahi</div>
            <a href="{{ route('home') }}" class="nav-drawer-link" style="display:flex;align-items:center;gap:12px;padding:10px 20px;color:rgba(255,255,255,0.9);font-family:'Poppins',sans-serif;font-size:0.88rem;font-weight:600;text-decoration:none;transition:all 0.18s;">
                <i class="bi bi-house-fill" style="width:20px;text-align:center;color:#3EDBF0;"></i> Beranda
            </a>
            <a href="{{ route('public.wisata.index') }}" class="nav-drawer-link" style="display:flex;align-items:center;gap:12px;padding:10px 20px;color:rgba(255,255,255,0.9);font-family:'Poppins',sans-serif;font-size:0.88rem;font-weight:600;text-decoration:none;transition:all 0.18s;">
                <i class="bi bi-tree-fill" style="width:20px;text-align:center;color:#3EDBF0;"></i> Wisata
            </a>
            <a href="{{ route('public.produk-khas.index') }}" class="nav-drawer-link" style="display:flex;align-items:center;gap:12px;padding:10px 20px;color:rgba(255,255,255,0.9);font-family:'Poppins',sans-serif;font-size:0.88rem;font-weight:600;text-decoration:none;transition:all 0.18s;">
                <i class="bi bi-bag-heart-fill" style="width:20px;text-align:center;color:#3EDBF0;"></i> Produk Khas
            </a>
            @endif

            {{-- ── 4. AKUN (GUEST) ─────────────────────────── --}}
            @guest
            <div style="padding:10px 20px 4px;font-size:0.65rem;font-weight:700;color:rgba(255,255,255,0.4);text-transform:uppercase;letter-spacing:1.5px;margin-top:4px;">Akun</div>
            <a href="{{ route('login') }}" class="nav-drawer-link" style="display:flex;align-items:center;gap:12px;padding:10px 20px;color:rgba(255,255,255,0.9);font-family:'Poppins',sans-serif;font-size:0.88rem;font-weight:600;text-decoration:none;transition:all 0.18s;">
                <i class="bi bi-box-arrow-in-right" style="width:20px;text-align:center;color:#77ACF1;"></i> Masuk
            </a>
            <a href="{{ route('register') }}" class="nav-drawer-link" style="display:flex;align-items:center;gap:12px;padding:10px 20px;color:rgba(255,255,255,0.9);font-family:'Poppins',sans-serif;font-size:0.88rem;font-weight:600;text-decoration:none;transition:all 0.18s;">
                <i class="bi bi-person-plus-fill" style="width:20px;text-align:center;color:#77ACF1;"></i> Daftar
            </a>
            @endguest
        </nav>

        {{-- Footer Logout --}}
        @auth
        <div style="padding:12px 14px;border-top:1px solid rgba(255,255,255,0.08);background:rgba(0,0,0,0.15);flex-shrink:0;">
            <form action="{{ route('logout') }}" method="post">
                @csrf
                <button type="submit" style="display:flex;align-items:center;justify-content:center;gap:8px;width:100%;padding:12px 16px;background:#ff3b3b;color:#ffffff;border:none;box-shadow:0 6px 20px rgba(255,59,59,0.4);border-radius:12px;font-family:'Poppins',sans-serif;font-size:1rem;font-weight:700;cursor:pointer;transition:all 0.2s;text-transform:uppercase;letter-spacing:1px;">
                    <i class="bi bi-box-arrow-left"></i> Logout
                </button>
            </form>
        </div>
        @endauth
    </div>

    <script>
    (function() {
        function getDrawer()  { return document.getElementById('navDrawer'); }
        function getOverlay() { return document.getElementById('navDrawerOverlay'); }

        function openDrawer() {
            var d = getDrawer(), o = getOverlay();
            if (!d) return;
            d.style.webkitTransform = 'translateX(0) translateZ(0)';
            d.style.transform       = 'translateX(0) translateZ(0)';
            o.style.display    = 'block';
            // Gunakan class, BUKAN style.overflow langsung
            // (overflow:hidden pada body memotong position:fixed di mobile)
            document.body.classList.add('drawer-open');
        }
        function closeDrawer() {
            var d = getDrawer(), o = getOverlay();
            if (!d) return;
            d.style.webkitTransform = 'translateX(100%) translateZ(0)';
            d.style.transform       = 'translateX(100%) translateZ(0)';
            o.style.display    = 'none';
            document.body.classList.remove('drawer-open');
        }

        // Delegated events (safe with Turbo)
        document.addEventListener('click', function(e) {
            if (e.target.closest('#btnOpenDrawer'))  { openDrawer();  return; }
            if (e.target.closest('#btnCloseDrawer')) { closeDrawer(); return; }
            if (e.target.closest('#navDrawerOverlay')) { closeDrawer(); return; }
            // Tutup drawer saat link/tombol di dalam drawer diklik
            if (e.target.closest('#navDrawer a, #navDrawer button[type="submit"]')) { closeDrawer(); }
        });

        // Hover effect untuk drawer links
        document.addEventListener('mouseover', function(e) {
            var link = e.target.closest('.nav-drawer-link');
            if (link && link.closest('#navDrawer')) {
                link.style.background   = 'rgba(255,255,255,0.1)';
                link.style.paddingLeft  = '26px';
            }
        });
        document.addEventListener('mouseout', function(e) {
            var link = e.target.closest('.nav-drawer-link');
            if (link && link.closest('#navDrawer')) {
                link.style.background  = 'transparent';
                link.style.paddingLeft = '20px';
            }
        });

        // Tutup drawer saat Turbo mulai navigasi
        document.addEventListener('turbo:before-visit', closeDrawer);

        // Bersihkan semua sisa modal Bootstrap sebelum Turbo menyimpan snapshot halaman
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
