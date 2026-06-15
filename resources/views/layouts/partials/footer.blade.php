{{-- ══════════════════════════════════════════════
     INFO CARD — SI-ASIH (Lokasi, Kontak, Cuaca)
══════════════════════════════════════════════ --}}
<div class="my-5" data-aos="fade-up">
    {{-- Outer Card --}}
    <div class="info-card-container">
        <div class="row align-items-stretch">

            {{-- Kiri: Peta Lokasi dengan Wrapper Card Putih --}}
            <div class="col-lg-6 mb-4 mb-lg-0 d-flex">
                <div class="info-card-map-wrapper w-100">
                    <iframe
                        src="https://maps.google.com/maps?q=Parkiran+Curug+Cibarebeuy+7M3G%2B585,+Cibeusi,+Kec.+Ciater,+Kabupaten+Subang,+Jawa+Barat+41281&t=&z=15&ie=UTF8&iwloc=&output=embed"
                        width="100%" height="100%"
                        style="border:0; min-height:300px; display:block; border-radius: 16px;"
                        allowfullscreen="" loading="lazy">
                    </iframe>
                </div>
            </div>

            {{-- Kanan: Contact Info + Cuaca --}}
            <div class="col-lg-6 d-flex flex-column justify-content-center px-4 px-lg-5 py-2">

                {{-- Contact Information --}}
                <h6 class="footer-section-title">CONTACT INFORMATION</h6>
                <ul class="footer-contact-list">
                    <li>
                        <i class="bi bi-geo-alt-fill"></i>
                        <span>Jl. Cibeusi, Nagrak, Kec. Ciater, Kabupaten Subang, Jawa Barat 41281</span>
                    </li>
                    <li>
                        <i class="bi bi-instagram"></i>
                        <a href="https://instagram.com/bumdes_cibeusi" target="_blank" rel="noopener" class="footer-social-link">@bumdes_cibeusi</a>
                    </li>
                    <li>
                        <i class="bi bi-tiktok"></i>
                        <a href="https://tiktok.com/@bumdes_cibeusi" target="_blank" rel="noopener" class="footer-social-link">@bumdes_cibeusi</a>
                    </li>
                    <li>
                        <i class="bi bi-facebook"></i>
                        <a href="https://www.facebook.com/people/BumDes-Cibeusi/61586067389424/#" target="_blank" rel="noopener" class="footer-social-link">BumDes Cibeusi</a>
                    </li>
                </ul>

                <hr class="footer-divider" style="opacity: 1; border-top: 1px solid rgba(62, 219, 240, 0.3);">

                {{-- Logo Team & Cuaca Container --}}
                <div class="row mt-3 g-0">
                    {{-- Kiri: Logo Tim & Designed By --}}
                    <div class="col-sm-6 d-flex flex-column align-items-center pe-sm-4 pb-3 pb-sm-0 footer-team-col">
                        <h6 class="footer-section-title">Designed By</h6>
                        <div class="d-flex flex-column align-items-center justify-content-between flex-grow-1">
                            <img src="https://res.cloudinary.com/dgwu1dpep/image/upload/v1779259336/miracle13_1_pb6zzs.png" 
                                 alt="Miracle 13 Logo" 
                                 style="height: 65px; width: auto; object-fit: contain;">
                            <span class="font-sampurasun" style="color: #ffffff; font-size: 1.1rem; letter-spacing: 0.5px;">Miracle</span>
                        </div>
                    </div>

                    {{-- Kanan: Cuaca --}}
                    <div class="col-sm-6 d-flex flex-column align-items-center ps-sm-4 pt-3 pt-sm-0">
                        <h6 class="footer-section-title">CUACA CIBEUSI</h6>
                        <div id="footer-weather-loader" class="footer-weather-loading d-flex align-items-center justify-content-center">
                            <div class="spinner-border spinner-border-sm text-info me-2" role="status" style="width:14px;height:14px;border-width:2px;"></div>
                            <small>Memuat cuaca...</small>
                        </div>
                        <div id="footer-weather-data" class="d-none w-100">
                            <div class="footer-weather-widget d-flex align-items-start justify-content-center">
                                <i id="footer-bmkg-icon" class="bi bi-cloud-sun text-info" style="margin-top: 4px;"></i>
                                <div class="ms-2 text-start">
                                    <span id="footer-bmkg-suhu" class="footer-temp">--°</span>
                                    <span id="footer-bmkg-kondisi" class="footer-cond">Kondisi</span>
                                    <div class="footer-weather-details mt-2">
                                        <div><i class="bi bi-droplet-half"></i> <span id="footer-bmkg-hum">--%</span></div>
                                        <div><i class="bi bi-clock-fill"></i> <span id="footer-clock">--:--:--</span> WIB</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>{{-- /.col --}}
        </div>{{-- /.row --}}

        {{-- Centered Copyright text inside the card --}}
        <div class="text-center mt-4 pt-3 mb-0" style="color: rgba(255, 255, 255, 0.75); font-size: 0.75rem; letter-spacing: 0.5px; border-top: 1px solid rgba(255, 255, 255, 0.15);">
            <span>SI-ASIH &copy; {{ date('Y') }}</span>
            <span class="mx-2">·</span>
            <span>BUMDes Cipta Asih Desa Cibeusi</span>
        </div>
    </div>{{-- /.info-card-container --}}
</div>

<script>
(function () {
    var _footerClockInterval = null;

    function initFooterWeather() {
        var loader  = document.getElementById('footer-weather-loader');
        var dataDiv = document.getElementById('footer-weather-data');
        if (!loader || !dataDiv) return;

        // Jam real-time
        if (_footerClockInterval) clearInterval(_footerClockInterval);
        function updateClock() {
            var el = document.getElementById('footer-clock');
            if (!el) return;
            var now = new Date();
            el.textContent =
                String(now.getHours()).padStart(2, '0') + ':' +
                String(now.getMinutes()).padStart(2, '0') + ':' +
                String(now.getSeconds()).padStart(2, '0');
        }
        updateClock();
        _footerClockInterval = setInterval(updateClock, 1000);

        // Icon color
        function iconColorClass(icon) {
            if (icon.includes('sun'))      return 'text-warning';
            if (icon.includes('rain') || icon.includes('drizzle')) return 'text-info';
            if (icon.includes('lightning')) return 'text-danger';
            if (icon.includes('fog'))      return 'text-secondary';
            return 'text-info';
        }

        // Fetch BMKG — gunakan cache sessionStorage selama 10 menit
        var CUACA_CACHE_KEY = 'siasih_cuaca_cache';
        var CUACA_CACHE_TTL = 10 * 60 * 1000; // 10 menit

        function renderCuaca(data) {
            document.getElementById('footer-bmkg-suhu').textContent    = data.suhu;
            document.getElementById('footer-bmkg-kondisi').textContent = data.kondisi;
            document.getElementById('footer-bmkg-hum').textContent     = data.kelembaban;
            var iconEl = document.getElementById('footer-bmkg-icon');
            iconEl.className = 'bi ' + data.icon + ' ' + iconColorClass(data.icon);
            loader.classList.add('d-none');
            dataDiv.classList.remove('d-none');
        }

        // Cek cache dulu sebelum fetch
        try {
            var cached = JSON.parse(sessionStorage.getItem(CUACA_CACHE_KEY));
            if (cached && (Date.now() - cached.ts) < CUACA_CACHE_TTL) {
                renderCuaca(cached.data);
                return; // Tidak perlu fetch ulang
            }
        } catch (e) { /* abaikan error parsing */ }

        fetch('{{ route("api.cuaca-bmkg") }}', { headers: { 'Accept': 'application/json' } })
            .then(function (r) { return r.json(); })
            .then(function (result) {
                if (result.success && result.data) {
                    // Simpan ke cache
                    try {
                        sessionStorage.setItem(CUACA_CACHE_KEY, JSON.stringify({
                            ts: Date.now(),
                            data: result.data
                        }));
                    } catch (e) { /* abaikan jika storage penuh */ }
                    renderCuaca(result.data);
                } else {
                    loader.innerHTML = '<small style="color:rgba(192,254,252,0.6)">Data cuaca tidak tersedia.</small>';
                }
            })
            .catch(function () {
                loader.innerHTML = '<small style="color:rgba(192,254,252,0.6)">Cuaca tidak dapat dimuat.</small>';
            });
    }

    document.addEventListener('turbo:load', initFooterWeather);
    initFooterWeather();

    document.addEventListener('turbo:before-cache', function () {
        if (_footerClockInterval) clearInterval(_footerClockInterval);
    });
})();
</script>
