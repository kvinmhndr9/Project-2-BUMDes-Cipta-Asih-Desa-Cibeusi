@extends('layouts.app')

@section('title', 'Lupa Kata Sandi')



@section('content')
<div class="row justify-content-center">
    <div class="col-12 col-md-8 col-lg-5">
        <div class="card shadow-sm border-0 rounded-3">
            <div class="card-body p-4 p-md-5">
                <div class="text-center mb-4">
                    <h4 class="fw-semibold mb-1" style="background: linear-gradient(90deg, #00b4d8, #2d6a4f); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;">Lupa Kata Sandi?</h4>
                    <p class="text-muted small mb-0">
                        Masukkan email Anda dan kami akan mengirimkan<br>link untuk mereset kata sandi.
                    </p>
                </div>

                {{-- Pesan sukses setelah email terkirim --}}
                @if(session('success'))
                    <div class="alert alert-success alert-permanent d-flex align-items-start gap-2 rounded-3">
                        <i class="bi bi-check-circle-fill mt-1 flex-shrink-0"></i>
                        <div>
                            <strong>Email terkirim!</strong><br>
                            <span class="small">{{ session('success') }} Periksa folder spam jika tidak ada di inbox.</span>
                        </div>
                    </div>
                @else
                    <form method="POST" action="{{ route('password.email') }}">
                        @csrf
                        <div class="mb-4">
                            <label class="form-label fw-medium">Alamat Email</label>
                            <input type="email" name="email"
                                class="form-control @error('email') is-invalid @enderror"
                                value="{{ old('email') }}"
                                placeholder="nama@email.com"
                                required autofocus>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <button type="submit" class="btn btn-primary w-100 py-2 fw-medium">
                            Kirim Link Reset
                        </button>
                    </form>
                @endif

                <div class="text-center mt-4">
                    <a href="{{ route('login') }}" class="text-muted small text-decoration-none">
                        <i class="bi bi-arrow-left me-1"></i> Kembali ke Login
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
