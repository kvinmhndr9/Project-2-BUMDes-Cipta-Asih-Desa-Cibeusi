@extends('layouts.app')

@section('title', 'Reset Kata Sandi')



@section('content')
<div class="row justify-content-center">
    <div class="col-12 col-md-8 col-lg-5">
        <div class="card shadow-sm border-0 rounded-3">
            <div class="card-body p-4 p-md-5">
                <div class="text-center mb-4">
                    <h4 class="fw-semibold mb-1" style="background: linear-gradient(90deg, #00b4d8, #2d6a4f); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;">Buat Kata Sandi Baru</h4>
                    <p class="text-muted small mb-0">Pastikan kata sandi baru Anda kuat dan mudah diingat.</p>
                </div>

                <form method="POST" action="{{ route('password.update.reset') }}">
                    @csrf

                    {{--
                        Token dikirim sebagai hidden field.
                        Token ini dibuat oleh Laravel saat user klik link di email,
                        dan akan divalidasi di controller untuk memastikan request sah.
                    --}}
                    <input type="hidden" name="token" value="{{ $token }}">

                    {{-- Email disembunyikan — sudah terisi otomatis dari link reset --}}
                    <input type="hidden" name="email" value="{{ old('email', $email) }}">

                    <div class="mb-3">
                        <label class="form-label fw-medium">Kata Sandi Baru</label>
                        <div class="password-wrapper">
                            <input type="password" name="password" id="new_password"
                                class="form-control @error('password') is-invalid @enderror"
                                placeholder="Minimal 8 karakter" required>
                            <button type="button" class="toggle-password" onclick="togglePassword('new_password', this)" tabindex="-1">
                                <i class="bi bi-eye-slash"></i>
                            </button>
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-medium">Konfirmasi Kata Sandi Baru</label>
                        <div class="password-wrapper">
                            <input type="password" name="password_confirmation" id="new_password_conf"
                                class="form-control"
                                placeholder="Ulangi kata sandi baru" required>
                            <button type="button" class="toggle-password" onclick="togglePassword('new_password_conf', this)" tabindex="-1">
                                <i class="bi bi-eye-slash"></i>
                            </button>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-success w-100 py-2 fw-medium">
                        Simpan Kata Sandi Baru
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function togglePassword(inputId, btn) {
    const input = document.getElementById(inputId);
    const icon = btn.querySelector('i');
    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.replace('bi-eye-slash', 'bi-eye');
    } else {
        input.type = 'password';
        icon.classList.replace('bi-eye', 'bi-eye-slash');
    }
}
</script>
@endpush
