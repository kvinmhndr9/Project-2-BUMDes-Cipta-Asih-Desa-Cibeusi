@extends('layouts.app')

@section('title', 'Profil Saya')

@section('content')
<h4 class="fs-4 fw-semibold mb-4" data-aos="fade-down">Profil Saya</h4>


@if(session('warning'))
<div class="alert alert-warning alert-dismissible fade show" role="alert">
    <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('warning') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

<div class="row g-4">
    <div class="col-lg-4" data-aos="fade-right">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-body text-center p-4">
                <div class="position-relative d-inline-block mb-3">
                    @if($user->avatar)
                        <img src="{{ $user->avatar }}" onerror="this.onerror=null; this.src='/images/default-avatar.svg';" alt="Avatar" class="rounded-circle user-avatar-img" width="100" height="100" style="object-fit:cover;" id="avatarPreview">
                    @else
                        <div id="avatarPlaceholder" class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 100px; height: 100px; font-size: 2.5rem;">
                            {{ strtoupper(substr($user->name, 0, 1)) }}
                        </div>
                        <img src="#" alt="Avatar" class="rounded-circle d-none" width="100" height="100" style="object-fit:cover;" id="avatarPreview">
                    @endif
                    <button type="button" class="btn btn-primary btn-sm rounded-circle position-absolute bottom-0 end-0" style="width: 32px; height: 32px; padding: 0; display: flex; align-items: center; justify-content: center; transform: translate(15%, 15%);" onclick="document.getElementById('avatar').click()" title="Ubah Foto Profil">
                        <i class="bi bi-camera-fill"></i>
                    </button>
                </div>
                <h5 class="fw-bold mb-1">{{ $user->name }}</h5>
                <p class="text-muted small mb-3">{{ $user->email }}</p>
                
                <span class="badge bg-{{ $totalTiketUsed > 5 ? 'success' : 'primary' }} rounded-pill px-3 py-2">
                    {{ $totalTiketUsed > 5 ? 'Pecinta Alam' : 'Petualang Pemula' }}
                </span>
            </div>
            <div class="card-footer bg-light border-0 p-3">
                <div class="row text-center">
                    <div class="col-6 border-end">
                        <h4 class="fw-bold text-primary mb-0">{{ $totalTiketUsed }}</h4>
                        <span class="small text-muted">Kunjungan</span>
                    </div>
                    <div class="col-6">
                        <h4 class="fw-bold text-primary mb-0">{{ $totalTiketBeli }}</h4>
                        <span class="small text-muted">Total Tiket</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-lg-8" data-aos="fade-left">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white border-0 pt-4 pb-0 px-4">
                <h6 class="fw-semibold mb-0"><i class="bi bi-person-lines-fill me-2"></i> Lengkapi Data Diri</h6>
                @if(empty($user->no_hp))
                    <div class="alert alert-danger mt-3 mb-0 small py-2">
                        <i class="bi bi-info-circle me-1"></i> Anda harus melengkapi <strong>Nomor WhatsApp</strong> untuk bisa memesan tiket.
                    </div>
                @endif
            </div>
            <div class="card-body p-4">
                <form action="{{ route('pengunjung.profil.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    
                    <div class="d-none">
                        <input type="file" id="avatar" name="avatar" accept="image/jpeg,image/png,image/jpg" onchange="openGlobalCrop(this, { aspectRatio: 1, onApply: applyProfilCrop })">
                        <input type="hidden" id="avatar_cropped" name="avatar_cropped">
                    </div>
                    @error('avatar')<div class="text-danger small mb-3">{{ $message }}</div>@enderror

                    <div class="mb-3">
                        <label class="form-label fw-medium">Nama Lengkap <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $user->name) }}" required>
                        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-medium">Email <span class="text-muted fw-normal">(Tidak dapat diubah)</span></label>
                        <input type="email" class="form-control bg-light" value="{{ $user->email }}" readonly>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-medium">Nomor WhatsApp <span class="text-danger">*</span></label>
                        <input type="text" name="no_hp" class="form-control @error('no_hp') is-invalid @enderror" value="{{ old('no_hp', $user->no_hp) }}" placeholder="Contoh: 08123456789" required>
                        <div class="form-text">Nomor ini akan digunakan untuk mengirimkan e-tiket Anda secara otomatis via WhatsApp.</div>
                        @error('no_hp')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-medium">Asal Kota</label>
                        <input type="text" name="asal_kota" class="form-control @error('asal_kota') is-invalid @enderror" value="{{ old('asal_kota', $user->asal_kota) }}" placeholder="Contoh: Bandung">
                        <div class="form-text">Opsional. Membantu kami memahami dari mana pengunjung berasal.</div>
                        @error('asal_kota')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    
                    <div class="d-flex justify-content-end mt-4">
                        <button type="submit" class="btn btn-primary px-4 fw-medium">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
        
        <div class="card shadow-sm border-0 mt-4">
            <div class="card-header bg-white border-0 pt-4 pb-0 px-4">
                <h6 class="fw-semibold mb-0"><i class="bi bi-star-fill text-warning me-2"></i> Jejak Ulasan Anda</h6>
            </div>
            <div class="card-body p-4">
                @forelse($reviews as $r)
                    <div class="mb-3 border-bottom pb-3">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="fw-bold">{{ $r->wisata->nama ?? 'Wisata Terhapus' }}</span>
                            <div>
                                <span class="small text-muted me-2">{{ $r->created_at->format('d M Y') }}</span>
                                <button type="button" class="btn btn-sm btn-outline-primary py-0 px-2" data-bs-toggle="modal" data-bs-target="#editReviewModal{{ $r->id }}">
                                    <i class="bi bi-pencil-square"></i> Edit
                                </button>
                            </div>
                        </div>
                        <div class="d-flex align-items-center mb-2">
                            @for($i = 1; $i <= 5; $i++)
                                <i class="bi bi-star-fill {{ $i <= $r->rating ? 'text-warning' : 'text-secondary opacity-25' }} me-1" style="font-size: 0.8rem;"></i>
                            @endfor
                        </div>
                        <p class="mb-0 text-muted small fst-italic">"{{ $r->comment ?? 'Tidak ada komentar' }}"</p>
                    </div>

                    <!-- Modal Edit Review -->
                    @push('modals')
                    <div class="modal fade" id="editReviewModal{{ $r->id }}" tabindex="-1" aria-labelledby="editReviewModalLabel{{ $r->id }}" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <form action="{{ route('pengunjung.review.update', $r->id) }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="editReviewModalLabel{{ $r->id }}">Edit Ulasan: {{ $r->wisata->nama ?? 'Wisata' }}</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="mb-3">
                                            <label class="form-label small fw-medium">Penilaian Rating</label>
                                            @include('components.star-rating', ['name' => 'rating', 'value' => $r->rating, 'id' => 'rating-edit-'.$r->id])
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label small fw-medium">Komentar Singkat</label>
                                            <textarea name="comment" class="form-control" rows="3" placeholder="Tulis pengalaman Anda di sini...">{{ $r->comment }}</textarea>
                                        </div>
                                    </div>
                                    <div class="modal-footer border-0 bg-light">
                                        <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                                        <button type="submit" class="btn btn-primary rounded-pill px-4">Simpan Perubahan</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    @endpush
                @empty
                    <div class="text-center">
                        <p class="text-muted mb-0 small">Anda belum memberikan ulasan wisata apa pun. Kunjungi wisata untuk memberikan rating!</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

@push('modals')
<x-crop-modal />
@endpush

@push('scripts')
<script>
function showToast(message, type = 'success') {
    const id = 'toast_' + Date.now();
    const icon = type === 'success'
        ? '<i class="bi bi-check-circle-fill me-2"></i>'
        : '<i class="bi bi-x-circle-fill me-2"></i>';
    const bg = type === 'success' ? 'bg-success' : 'bg-danger';
    const html = `
        <div id="${id}" class="toast align-items-center text-white ${bg} border-0 shadow"
             role="alert" aria-live="assertive" data-bs-autohide="true" data-bs-delay="3500">
            <div class="d-flex">
                <div class="toast-body fw-semibold">${icon}${message}</div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        </div>`;
    let container = document.getElementById('toastContainer');
    if (!container) {
        container = document.createElement('div');
        container.id = 'toastContainer';
        container.className = 'toast-container position-fixed bottom-0 end-0 p-3';
        container.style.zIndex = '9999';
        document.body.appendChild(container);
    }
    container.insertAdjacentHTML('beforeend', html);
    const el = document.getElementById(id);
    new bootstrap.Toast(el).show();
    el.addEventListener('hidden.bs.toast', () => el.remove());
}

function applyProfilCrop(blob, base64) {
    // Update all user avatar images on the page immediately with the base64 preview
    document.querySelectorAll('.user-avatar-img').forEach(img => {
        img.src = base64;
        img.classList.remove('d-none');
    });

    const placeholder = document.getElementById('avatarPlaceholder');
    if (placeholder) placeholder.classList.add('d-none');

    document.getElementById('avatar').value = '';

    fetch('{{ route("pengunjung.profil.avatar") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json',
        },
        body: JSON.stringify({ avatar_cropped: base64 }),
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            // Update all user avatar images on the page with the final Cloudinary URL
            document.querySelectorAll('.user-avatar-img').forEach(img => {
                img.src = data.avatar_url;
            });
            showToast(data.message, 'success');
        } else {
            showToast(data.message || 'Gagal menyimpan foto.', 'error');
        }
    })
    .catch(() => {
        showToast('Terjadi kesalahan jaringan. Coba lagi.', 'error');
    });
}
</script>
@endpush

@endsection
