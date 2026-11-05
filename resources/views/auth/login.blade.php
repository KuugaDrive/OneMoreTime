@extends('layouts.auth')

@section('title', 'Masuk ke Sistem')

@section('content')
<div class="card card-app shadow-sm border">
    <div class="card-body p-4 p-sm-5">
        <div class="text-center mb-4">
            <h1 class="h5 fw-bold text-dark mb-1" style="font-size: 1.15rem; letter-spacing: -0.2px;">
                {{ config('app.name', 'Sistem Administrasi Keuangan') }}
            </h1>
            <div class="text-secondary small fw-semibold text-uppercase" style="letter-spacing: 0.5px; font-size: 11px;">
                {{ strtoupper(config('app.nama_usaha', 'AGEN RESMI JNE')) }}
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success py-2 px-3 small d-flex align-items-center gap-2 mb-3">
                <i class="bi bi-check-circle flex-shrink-0"></i>
                <div>{{ session('success') }}</div>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger py-2 px-3 small d-flex align-items-center gap-2 mb-3">
                <i class="bi bi-exclamation-circle flex-shrink-0"></i>
                <div>{{ session('error') }}</div>
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger py-2 px-3 small d-flex align-items-center gap-2 mb-3">
                <i class="bi bi-exclamation-circle flex-shrink-0"></i>
                <div>{{ $errors->first() }}</div>
            </div>
        @endif

        <form action="{{ route('login.post') }}" method="POST" id="loginForm" novalidate>
            @csrf
            <div class="mb-3">
                <label for="usernameInput" class="form-label small fw-semibold text-dark">Nama Pengguna</label>
                <input type="text" 
                       name="username" 
                       class="form-control @error('username') is-invalid @enderror" 
                       id="usernameInput" 
                       value="{{ old('username') }}" 
                       placeholder="Masukkan nama pengguna" 
                       required 
                       autofocus 
                       autocomplete="username">
            </div>

            <div class="mb-3">
                <label for="passwordInput" class="form-label small fw-semibold text-dark">Kata Sandi</label>
                <div class="input-group">
                    <input type="password" 
                           name="password" 
                           class="form-control @error('password') is-invalid @enderror" 
                           id="passwordInput" 
                           placeholder="Masukkan kata sandi" 
                           required 
                           autocomplete="current-password">
                    <button class="btn btn-outline-secondary" type="button" id="btnTogglePassword" aria-label="Tampilkan atau sembunyikan kata sandi" style="border-color: var(--border-color, #D9DDE3);">
                        <i class="bi bi-eye" id="iconTogglePassword"></i>
                    </button>
                </div>
            </div>

            <div class="mb-4">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="remember" id="rememberMe">
                    <label class="form-check-label small text-secondary" for="rememberMe">
                        Ingat sesi login
                    </label>
                </div>
            </div>

            <button type="submit" class="btn btn-primary w-100 py-2 fw-medium" id="btnLoginSubmit">
                Masuk
            </button>
        </form>
    </div>

    <div class="card-footer bg-white border-top text-center py-3">
        <span class="text-secondary" style="font-size: 12px;">&copy; {{ date('Y') }} {{ config('app.nama_usaha', 'Agen Resmi JNE') }}</span>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const toggleBtn = document.getElementById('btnTogglePassword');
        const passInput = document.getElementById('passwordInput');
        const passIcon = document.getElementById('iconTogglePassword');

        if (toggleBtn && passInput && passIcon) {
            toggleBtn.addEventListener('click', function () {
                const isPassword = passInput.getAttribute('type') === 'password';
                passInput.setAttribute('type', isPassword ? 'text' : 'password');
                passIcon.className = isPassword ? 'bi bi-eye-slash' : 'bi bi-eye';
            });
        }
    });
</script>
@endpush
