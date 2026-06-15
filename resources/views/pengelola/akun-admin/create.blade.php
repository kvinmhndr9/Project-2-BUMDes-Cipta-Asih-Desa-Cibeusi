@extends('layouts.app')

@section('title', 'Tambah Akun Admin')

@section('content')
<div class="d-flex align-items-center gap-3 mb-4">
    <a href="{{ route('pengelola.akun-admin.index') }}" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
        <i class="bi bi-arrow-left me-1"></i>Kembali
    </a>
    <div>
        <h4 class="fs-4 fw-semibold mb-0">Tambah Akun Admin</h4>
        <p class="text-muted small mb-0">Buat akun petugas baru untuk wisata</p>
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-12 col-lg-7">
        <div class="card shadow-sm border-0 rounded-4">
            <div class="card-body p-4">
                <form method="POST" action="{{ route('pengelola.akun-admin.store') }}" id="form-create-admin" novalidate>
                    @csrf

                    {{-- Nama --}}
                    <div class="mb-4">
                        <label for="name" class="form-label fw-semibold">
                            <i class="bi bi-person-fill me-1 text-primary"></i>Nama Lengkap
                        </label>
                        <input type="text"
                               id="name"
                               name="name"
                               class="form-control rounded-3 @error('name') is-invalid @enderror"
                               value="{{ old('name') }}"
                               placeholder="Contoh: Budi Santoso"
                               required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Email --}}
                    <div class="mb-4">
                        <label for="email" class="form-label fw-semibold">
                            <i class="bi bi-envelope-fill me-1 text-primary"></i>Email
                        </label>
                        <input type="email"
                               id="email"
                               name="email"
                               class="form-control rounded-3 @error('email') is-invalid @enderror"
                               value="{{ old('email') }}"
                               placeholder="Contoh: budi@example.com"
                               required>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Password --}}
                    <div class="mb-4">
                        <label for="password" class="form-label fw-semibold">
                            <i class="bi bi-lock-fill me-1 text-primary"></i>Password
                        </label>
                        <div class="input-group">
                            <input type="password"
                                   id="password"
                                   name="password"
                                   class="form-control rounded-start-3 @error('password') is-invalid @enderror"
                                   placeholder="Minimal 8 karakter"
                                   required>
                            <button type="button" class="btn btn-outline-secondary rounded-end-3" id="togglePassword" tabindex="-1">
                                <i class="bi bi-eye-fill" id="iconPassword"></i>
                            </button>
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-text text-muted">Password minimal 8 karakter.</div>
                    </div>

                    {{-- Wisata --}}
                    <div class="mb-4">
                        <label for="id_wisata" class="form-label fw-semibold">
                            <i class="bi bi-geo-alt-fill me-1 text-primary"></i>Wisata yang Ditangani
                        </label>
                        <select id="id_wisata"
                                name="id_wisata"
                                class="form-select rounded-3 @error('id_wisata') is-invalid @enderror"
                                required>
                            <option value="" disabled {{ old('id_wisata') ? '' : 'selected' }}>-- Pilih Wisata --</option>
                            @foreach($wisataList as $wisata)
                                <option value="{{ $wisata->id_wisata }}" {{ old('id_wisata') == $wisata->id_wisata ? 'selected' : '' }}>
                                    {{ $wisata->nama }}
                                </option>
                            @endforeach
                        </select>
                        @error('id_wisata')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <hr class="opacity-10 mb-4">

                    <div class="d-flex gap-3 justify-content-end">
                        <a href="{{ route('pengelola.akun-admin.index') }}" class="btn btn-outline-secondary rounded-pill px-4">
                            Batal
                        </a>
                        <button type="submit" class="btn btn-primary rounded-pill px-5 fw-semibold shadow-sm">
                            <i class="bi bi-check-circle-fill me-2"></i>Buat Akun
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('turbo:load', function () {
    var btn = document.getElementById('togglePassword');
    var inp = document.getElementById('password');
    var ico = document.getElementById('iconPassword');
    if (!btn || !inp) return;
    btn.addEventListener('click', function () {
        if (inp.type === 'password') {
            inp.type = 'text';
            ico.classList.replace('bi-eye-fill', 'bi-eye-slash-fill');
        } else {
            inp.type = 'password';
            ico.classList.replace('bi-eye-slash-fill', 'bi-eye-fill');
        }
    });
});
</script>
@endpush
