@extends('layouts.app')

@section('title', 'Tiket ' . $tiket->kode_tiket)

@section('content')
@if(request('payment') === 'success')
    @if($tiket->status === 'paid')
        <div class="alert alert-success alert-dismissible fade show shadow-sm border-0 mb-4" role="alert" data-turbo-cache="false">
            <span><i class="bi bi-check-circle-fill me-2"></i>Pembayaran berhasil! Tiket Anda sudah aktif.</span>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @else
        <div class="alert alert-info alert-dismissible fade show shadow-sm border-0 mb-4" role="alert" data-turbo-cache="false">
            <span><i class="bi bi-hourglass-split me-2"></i>Pembayaran Anda sedang diproses atau menunggu verifikasi. Harap tunggu beberapa saat atau refresh halaman jika Anda sudah menyelesaikan pembayaran.</span>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
@endif
@if(request('payment') === 'pending')
<div class="alert alert-info alert-dismissible fade show shadow-sm border-0 mb-4" role="alert" data-turbo-cache="false">
    <span><i class="bi bi-hourglass-split me-2"></i>Menunggu pembayaran. Silakan selesaikan pembayaran Anda.</span>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

<div class="row justify-content-center" data-aos="zoom-in">
    <div class="col-md-8 col-lg-6">
        <div class="card shadow-sm border-0">
            <div class="card-body p-4 p-sm-5 text-center">
                <h5 class="text-primary fw-semibold mb-2">Tiket Wisata</h5>
                <p class="mb-1 fw-bold fs-5">{{ $tiket->wisata->nama }}</p>
                <p class="mb-2 text-muted small">Kode: <strong>{{ $tiket->kode_tiket }}</strong></p>
                <p class="mb-2">Jumlah: {{ $tiket->jumlah }} pengunjung</p>
                <div class="mb-2 d-flex align-items-center justify-content-center gap-2">
                    <span>Tanggal berkunjung: {{ $tiket->tanggal_berkunjung->format('d F Y') }}</span>
                    @if(in_array($tiket->status, ['pending', 'paid']))
                        @if($tiket->status === 'pending' || ($tiket->status === 'paid' && $tiket->reschedule_count < 1))
                            <button type="button" class="btn btn-sm btn-outline-secondary py-0 px-2" data-bs-toggle="modal" data-bs-target="#rescheduleModal" title="Ubah Tanggal">
                                <i class="bi bi-pencil-square"></i>
                            </button>
                        @endif
                    @endif
                </div>
                @if($tiket->wisata->hasCamping() && $tiket->camping)
                <p class="mb-2">Keterangan: <strong>{{ $tiket->camping === 'Ya' ? 'Camping' : 'Kunjungan' }}</strong></p>
                @endif
                
                <div class="mb-4 text-muted small text-start bg-light p-3 rounded-3 mt-3">
                    <strong class="text-dark d-block mb-1">Keterangan parkir (dibayar di lokasi):</strong>
                    <ul class="mb-0 ps-3">
                        <li>Motor Kunjungan Rp 10.000 (include pentitipan helm & barang)</li>
                        <li>Motor Camping Rp 15.000 (include pentitipan helm & barang)</li>
                        <li>Mobil Kunjungan Rp 15.000</li>
                        <li>Mobil Camping Rp 25.000</li>
                    </ul>
                </div>

                <div class="mb-4" id="tiket-status-wrap">
                    @if($tiket->status === 'pending')
                        <div class="mb-4">
                            <span class="badge rounded-pill bg-warning text-dark fs-6 px-3 py-2" id="tiket-status-badge">Menunggu Pembayaran</span>
                        </div>
                        <div class="d-flex flex-column flex-sm-row gap-2 justify-content-center">
                            <a href="{{ route('pengunjung.tiket.bayar', $tiket) }}" class="btn btn-primary px-4 fw-medium rounded-pill">Bayar Sekarang</a>
                            <button type="button" class="btn btn-outline-danger px-4 fw-medium rounded-pill" data-bs-toggle="modal" data-bs-target="#batalModal">
                                <i class="bi bi-x-circle me-1"></i> Batalkan Tiket
                            </button>
                        </div>
                    @elseif($tiket->status === 'paid')
                        <span class="badge rounded-pill bg-success fs-6 px-3 py-2 text-white" id="tiket-status-badge">Sudah Dibayar</span>
                    @elseif($tiket->status === 'used')
                        <span class="badge rounded-pill bg-secondary fs-6 px-3 py-2 text-white" id="tiket-status-badge">Sudah Terpakai</span>
                    @elseif($tiket->status === 'cancelled')
                        <span class="badge rounded-pill bg-danger fs-6 px-3 py-2 text-white" id="tiket-status-badge"><i class="bi bi-x-circle me-1"></i>Dibatalkan</span>
                        @if($tiket->cancel_reason)
                            <p class="text-muted small mt-2 mb-0"><i class="bi bi-chat-left-text me-1"></i>Alasan: {{ $tiket->cancel_reason }}</p>
                        @endif
                    @else
                        <span class="badge rounded-pill bg-secondary fs-6 px-3 py-2 text-white" id="tiket-status-badge">{{ ucfirst($tiket->status) }}</span>
                    @endif
                </div>

                @if(in_array($tiket->status, ['paid', 'used']))
                <div class="border rounded-3 p-4 bg-light d-inline-block mb-4">
                    <p class="small fw-medium text-dark mb-3">
                        @if($tiket->status === 'used')
                            QR Tiket Anda
                        @else
                            Tunjukkan QR ini di lokasi wisata
                        @endif
                    </p>
                    @php
                        $tiket->load('wisata');
                        $qrContent = $tiket->qr_content;
                        $qrUrl = route('pengunjung.tiket.qrcode', $tiket);
                        $fallbackUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=' . urlencode($qrContent);
                        $downloadUrl = $qrUrl . '?download=1';
                    @endphp
                    <div id="qr-container">
                        <img id="qr-image"
                             src="{{ $qrUrl }}"
                             alt="QR Tiket {{ $tiket->kode_tiket }}"
                             class="img-fluid mx-auto d-block"
                             style="max-width: 200px"
                             width="200"
                             height="200"
                             onerror="handleQRError(this, '{{ $fallbackUrl }}');">
                        <div id="qr-loading" class="text-muted small mt-2 d-none">Memuat QR code...</div>
                        <div id="qr-error" class="text-danger small mt-2 d-none">Gagal memuat QR. <a href="{{ $fallbackUrl }}" target="_blank" class="text-danger text-decoration-underline">Buka QR di tab baru</a></div>
                        <div class="mt-4">
                            <a href="{{ $downloadUrl }}" class="btn btn-primary fw-medium">
                                <i class="bi bi-download me-2"></i> Download QR
                            </a>
                        </div>
                    </div>
                </div>
                @endif

                @if($tiket->status === 'used')
                <div class="border rounded-3 p-4 bg-light text-start mb-4">
                    <h6 class="fw-semibold text-dark"><i class="bi bi-star-fill text-warning me-2"></i>Ulasan Kehadiran Anda</h6>
                    @if($tiket->review)
                        <div class="mt-3">
                            <div class="d-flex align-items-center mb-2">
                                @for($i = 1; $i <= 5; $i++)
                                    <i class="bi bi-star-fill {{ $i <= $tiket->review->rating ? 'text-warning' : 'text-secondary opacity-25' }} me-1"></i>
                                @endfor
                            </div>
                            <p class="mb-0 text-muted fst-italic">"{{ $tiket->review->comment ?? 'Tidak ada komentar.' }}"</p>
                        </div>
                    @else
                        <p class="small text-muted mb-3">Bagaimana pengalaman wisata Anda? Berikan penilaian agar kami dapat memberikan pelayanan yang lebih baik.</p>
                        <form action="{{ route('pengunjung.review.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="id_tiket" value="{{ $tiket->id }}">
                            <input type="hidden" name="id_wisata" value="{{ $tiket->id_wisata }}">
                            <div class="mb-3">
                                <label class="form-label small fw-medium">Penilaian Rating</label>
                                @include('components.star-rating', ['name' => 'rating', 'value' => old('rating', 5), 'id' => 'rating-tiket-show'])
                            </div>
                            <div class="mb-3">
                                <label class="form-label small fw-medium">Komentar Singkat</label>
                                <textarea name="comment" class="form-control @error('comment') is-invalid @enderror" rows="3" placeholder="Tulis pengalaman Anda di sini...">{{ old('comment') }}</textarea>
                                @error('comment')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="mb-3">
                                <label class="form-label small fw-medium">Foto (Opsional)</label>
                                <input type="file" name="foto" id="fotoUpload" class="form-control @error('foto') is-invalid @enderror" accept="image/jpeg,image/png,image/jpg" onchange="openGlobalCrop(this, { previewContainerId: 'fotoPreview' })">
                                @error('foto')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                <div class="mt-2 d-none" id="previewContainer">
                                    <p class="small text-muted mb-1">Preview Foto:</p>
                                    <img id="fotoPreview" src="" alt="Preview" class="img-thumbnail" style="max-height: 150px;">
                                </div>
                            </div>
                            <div class="text-end">
                                <button type="submit" class="btn btn-primary px-4">Kirim Ulasan</button>
                            </div>
                        </form>
                    @endif
                </div>
                @endif
                
                <div>
                    <a href="{{ route('pengunjung.tiket.my') }}" class="btn btn-outline-primary px-4 fw-medium">{{ request('payment') === 'success' ? 'Kembali ke Tiket Saya' : 'Ke Daftar Tiket Saya' }}</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('modals')
<!-- Modal Reschedule -->
@if(in_array($tiket->status, ['pending', 'paid']))
<div class="modal fade" id="rescheduleModal" tabindex="-1" aria-labelledby="rescheduleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-bottom-0 pb-0">
                <h5 class="modal-title fw-bold" id="rescheduleModalLabel">Ubah Tanggal Kunjungan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('pengunjung.tiket.reschedule', $tiket) }}" method="POST" data-turbo="false">
                @csrf
                @method('PUT')
                <div class="modal-body py-4">
                    @if($tiket->status === 'paid')
                        <div class="alert alert-warning small mb-3">
                            <i class="bi bi-exclamation-triangle-fill me-1"></i> <strong>Perhatian:</strong> Karena tiket ini sudah dibayar, Anda hanya diperbolehkan mengubah jadwal sebanyak <strong>1 kali</strong>.
                        </div>
                    @else
                        <div class="alert alert-info small mb-3">
                            <i class="bi bi-info-circle-fill me-1"></i> Tiket berstatus <strong>Pending</strong> dapat diubah jadwalnya tanpa batasan.
                        </div>
                    @endif
                    
                    <div class="mb-3 text-start">
                        <label for="tanggal_berkunjung" class="form-label fw-medium text-dark small">Pilih Tanggal Baru</label>
                        <input type="date" class="form-control" id="tanggal_berkunjung" name="tanggal_berkunjung" value="{{ $tiket->tanggal_berkunjung->format('Y-m-d') }}" min="{{ date('Y-m-d') }}" required>
                    </div>
                </div>
                <div class="modal-footer border-0 bg-light">
                    <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4">Simpan Jadwal</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

{{-- Modal Batalkan Tiket (hanya untuk pending) --}}
@if($tiket->status === 'pending')
<div class="modal fade" id="batalModal" tabindex="-1" aria-labelledby="batalModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-bottom-0 pb-0">
                <h5 class="modal-title fw-bold text-danger" id="batalModalLabel">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>Batalkan Tiket?
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('pengunjung.tiket.cancel', $tiket) }}" method="POST" data-turbo="false">
                @csrf
                @method('DELETE')
                <div class="modal-body py-4">
                    <div class="alert alert-warning small mb-3">
                        <i class="bi bi-info-circle-fill me-1"></i>
                        <strong>Perhatian:</strong> Pembatalan tiket bersifat <strong>permanen</strong> dan tidak dapat dibatalkan kembali.
                    </div>
                    <div class="mb-3 text-start">
                        <label for="cancel_reason" class="form-label fw-medium text-dark small">Alasan Pembatalan <span class="text-muted">(opsional)</span></label>
                        <input type="text" class="form-control" id="cancel_reason" name="cancel_reason"
                               placeholder="Cth: Tidak jadi pergi, ada keperluan mendadak, dll."
                               maxlength="255">
                    </div>
                </div>
                <div class="modal-footer border-0 bg-light">
                    <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn btn-danger rounded-pill px-4"><i class="bi bi-x-circle me-1"></i> Ya, Batalkan Tiket</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

<x-crop-modal />
@endpush

@push('scripts')
<script>
(function() {
    var REALTIME_INTERVAL = 5000;
    var statusWrap = document.getElementById('tiket-status-wrap');
    var showUrl = '{{ route("pengunjung.tiket.show", $tiket) }}';
    var pollingPaused = false; // Pause polling saat modal terbuka

    // Pause polling saat ada modal yang terbuka agar tidak mengganggu interaksi
    document.addEventListener('show.bs.modal', function() { pollingPaused = true; });
    document.addEventListener('hidden.bs.modal', function() { pollingPaused = false; });

    if (statusWrap && showUrl) {
        var currentStatus = '{{ $tiket->status }}';
        setInterval(function() {
            if (pollingPaused) return; // Jangan fetch saat modal terbuka
            fetch(showUrl, { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } })
                .then(function(r) { return r.json(); })
                .then(function(data) {
                    if (data.status && data.status !== currentStatus) {
                        currentStatus = data.status;
                        var badge = document.getElementById('tiket-status-badge');
                        if (data.status === 'paid') {
                            if (badge) badge.outerHTML = '<span class="badge rounded-pill bg-success fs-6 px-3 py-2 text-white" id="tiket-status-badge">Sudah Dibayar</span>';
                            location.reload();
                        } else if (data.status === 'used') {
                            if (badge) badge.outerHTML = '<span class="badge rounded-pill bg-secondary fs-6 px-3 py-2 text-white" id="tiket-status-badge">Sudah Terpakai</span>';
                            location.reload();
                        } else if (badge) {
                            badge.className = 'badge rounded-pill bg-warning text-dark fs-6 px-3 py-2';
                            badge.textContent = data.status === 'pending' ? 'Menunggu Pembayaran' : data.status;
                        }
                    }
                })
                .catch(function() {});
        }, REALTIME_INTERVAL);
    }

    // Bersihkan semua sisa modal Bootstrap sebelum Turbo menyimpan snapshot halaman
    document.addEventListener('turbo:before-cache', function() {
        document.querySelectorAll('.modal.show').forEach(function(modalEl) {
            var instance = bootstrap.Modal.getInstance(modalEl);
            if (instance) instance.hide();
        });
        // Paksa hapus backdrop yang mungkin tertinggal
        document.querySelectorAll('.modal-backdrop').forEach(function(el) { el.remove(); });
        document.body.classList.remove('modal-open');
        document.body.style.removeProperty('overflow');
        document.body.style.removeProperty('padding-right');
    });
})();
function handleQRError(img, fallbackUrl) {
    var qrError = document.getElementById('qr-error');
    var qrLoading = document.getElementById('qr-loading');

    if (qrLoading) qrLoading.classList.add('d-none');

    if (img.src !== fallbackUrl) {
        img.src = fallbackUrl;
        if (qrLoading) qrLoading.classList.remove('d-none');
        img.onerror = function() {
            if (qrLoading) qrLoading.classList.add('d-none');
            if (qrError) { qrError.classList.remove('d-none'); }
            img.style.display = 'none';
        };
        img.onload = function() {
            if (qrLoading) qrLoading.classList.add('d-none');
            if (qrError) { qrError.classList.add('d-none'); }
        };
    } else {
        if (qrLoading) qrLoading.classList.add('d-none');
        if (qrError) { qrError.classList.remove('d-none'); }
        img.style.display = 'none';
    }
}

document.addEventListener("turbo:load", function() {
    var qrImg = document.getElementById('qr-image');
    var qrLoading = document.getElementById('qr-loading');
    if (qrImg && qrLoading) {
        qrImg.onload = function() {
            qrLoading.classList.add('d-none');
        };
        qrImg.onerror = function() {
            qrLoading.classList.add('d-none');
        };
    }
});
</script>
@endpush

