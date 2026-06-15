@extends('layouts.app')

@section('title', 'Verifikasi Email')



@section('content')
<div class="row justify-content-center">
    <div class="col-12 col-md-8 col-lg-5">
        <div class="card shadow-sm border-0 rounded-3 overflow-hidden">

            {{-- Gradient accent strip (same as image) --}}
            <div class="otp-card-accent"></div>

            <div class="card-body p-4 p-md-5 text-center">

                {{-- Title & subtitle --}}
                <h4 class="card-title fs-4 fw-semibold mb-2 text-center">Verifikasi Email Anda</h4>
                <p class="text-muted small mb-1">Kami telah mengirimkan kode 6 angka ke:</p>
                <p class="fw-semibold text-primary small mb-3">{{ $email }}</p>

                <span class="timer-badge mb-4 d-inline-flex">
                    <i class="bi bi-clock"></i> Kode berlaku 30 menit
                </span>

                {{-- Flash: resent --}}
                @if(session('resent'))
                <div class="alert alert-success border-0 small py-2 px-3 mt-3 mb-0 rounded-3">
                    <i class="bi bi-check-circle me-1"></i>{{ session('resent') }}
                </div>
                @endif

                {{-- Error --}}
                @if($errors->has('code'))
                <div class="alert alert-danger border-0 small py-2 px-3 mt-3 mb-0 rounded-3">
                    <i class="bi bi-exclamation-triangle me-1"></i>{{ $errors->first('code') }}
                </div>
                @endif

                {{-- OTP Form --}}
                <form method="POST" action="{{ route('verification.otp.verify') }}" id="otp-form" class="mt-4">
                    @csrf
                    <input type="hidden" name="code" id="otp-hidden">

                    <div class="otp-input-group mb-4" id="otp-inputs">
                        @for($i = 0; $i < 6; $i++)
                            <input type="text"
                                   inputmode="numeric"
                                   maxlength="1"
                                   pattern="[0-9]"
                                   class="otp-digit {{ $errors->has('code') ? 'is-error' : '' }}"
                                   id="otp-{{ $i }}"
                                   autocomplete="off">
                        @endfor
                    </div>

                    <button type="submit" class="btn-verify" id="verify-btn">
                        VERIFIKASI
                    </button>
                </form>

                {{-- Divider --}}
                <div class="position-relative my-4">
                    <hr class="text-muted">
                </div>

                {{-- Resend --}}
                <p class="small text-muted mb-2">Tidak menerima kode?</p>
                <form method="POST" action="{{ route('verification.otp.resend') }}">
                    @csrf
                    <button type="submit" class="btn btn-outline-secondary btn-sm rounded-pill px-4">
                        <i class="bi bi-arrow-clockwise me-1"></i>Kirim Ulang Kode
                    </button>
                </form>

                <div class="mt-3">
                    <a href="{{ route('login') }}" class="small text-muted text-decoration-none">
                        <i class="bi bi-arrow-left me-1"></i>Kembali ke halaman login
                    </a>
                </div>

            </div>{{-- /card-body --}}
        </div>{{-- /card --}}
    </div>
</div>
@endsection

@push('scripts')
<script>
(function () {
    const inputs  = Array.from(document.querySelectorAll('.otp-digit'));
    const hidden  = document.getElementById('otp-hidden');
    const form    = document.getElementById('otp-form');
    const btn     = document.getElementById('verify-btn');

    function sync() {
        hidden.value = inputs.map(i => i.value).join('');
    }

    function markError(on) {
        inputs.forEach(i => {
            i.classList.toggle('is-error', on);
        });
    }

    inputs.forEach(function (el, idx) {
        el.addEventListener('keydown', function (e) {
            if (e.key === 'Backspace') {
                e.preventDefault();
                if (el.value) {
                    el.value = '';
                } else if (idx > 0) {
                    inputs[idx - 1].value = '';
                    inputs[idx - 1].focus();
                }
                sync();
                markError(false);
                return;
            }
            if (e.key === 'ArrowLeft'  && idx > 0) { inputs[idx - 1].focus(); return; }
            if (e.key === 'ArrowRight' && idx < 5) { inputs[idx + 1].focus(); return; }
            if (!/^[0-9]$/.test(e.key) && !['Tab', 'Enter'].includes(e.key)) {
                e.preventDefault();
            }
        });

        el.addEventListener('input', function () {
            el.value = el.value.replace(/\D/g, '').slice(-1);
            sync();
            markError(false);
            if (el.value && idx < 5) inputs[idx + 1].focus();
            // Auto-submit when all 6 filled
            if (inputs.every(i => i.value !== '')) {
                btn.disabled = true;
                btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Memverifikasi...';
                setTimeout(() => form.submit(), 120);
            }
        });

        el.addEventListener('paste', function (e) {
            e.preventDefault();
            const paste = (e.clipboardData || window.clipboardData)
                .getData('text').replace(/\D/g, '').slice(0, 6);
            paste.split('').forEach(function (ch, i) {
                if (inputs[i]) inputs[i].value = ch;
            });
            sync();
            const next = inputs.findIndex(i => !i.value);
            (inputs[next >= 0 ? next : 5]).focus();
            if (inputs.every(i => i.value !== '')) {
                btn.disabled = true;
                btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Memverifikasi...';
                setTimeout(() => form.submit(), 120);
            }
        });

        el.addEventListener('focus', () => markError(false));
    });

    // Focus first empty box on page load
    const first = inputs.findIndex(i => !i.value);
    if (inputs.length) inputs[first >= 0 ? first : 0].focus();
})();
</script>
@endpush
