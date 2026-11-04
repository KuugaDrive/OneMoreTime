@extends('layouts.auth')

@section('title', 'Login Masuk')

@section('content')
<div class="container" style="max-width: 440px;">
    <div class="card border-0 shadow-lg rounded-4 overflow-hidden">
        <div class="bg-primary text-white p-4 text-center" style="background: linear-gradient(135deg, #1e3a8a, #0f172a) !important;">
            <div class="d-inline-flex align-items-center justify-content-center bg-danger text-white rounded-3 mb-2 shadow" style="width: 50px; height: 50px;">
                <i class="bi bi-box-seam-fill fs-3"></i>
            </div>
            <h4 class="fw-bold mb-1">AGEN RESMI JNE</h4>
            <p class="small text-light opacity-75 mb-0">Sistem Pencatatan Komisi & Operasional</p>
        </div>

        <div class="card-body p-4 p-md-4">
            @if(session('success'))
                <div class="alert alert-success py-2 px-3 small d-flex align-items-center gap-2 mb-3">
                    <i class="bi bi-check-circle-fill fs-5"></i>
                    <div>{{ session('success') }}</div>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger py-2 px-3 small d-flex align-items-center gap-2 mb-3">
                    <i class="bi bi-exclamation-octagon-fill fs-5"></i>
                    <div>{{ session('error') }}</div>
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger py-2 px-3 small d-flex align-items-center gap-2 mb-3">
                    <i class="bi bi-exclamation-triangle-fill fs-5"></i>
                    <div>{{ $errors->first() }}</div>
                </div>
            @endif

            <form action="{{ route('login.post') }}" method="POST" id="loginForm">
                @csrf
                <div class="mb-3">
                    <label for="usernameInput" class="form-label">Username / Akun</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light text-muted"><i class="bi bi-person-fill"></i></span>
                        <input type="text" name="username" class="form-control" id="usernameInput" value="{{ old('username', 'owner_utama') }}" placeholder="Masukkan username" required autofocus>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="passwordInput" class="form-label">Kata Sandi (Password)</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light text-muted"><i class="bi bi-lock-fill"></i></span>
                        <input type="password" name="password" class="form-control" id="passwordInput" value="password" placeholder="Masukkan kata sandi" required>
                    </div>
                </div>

                <div class="mb-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="remember" id="rememberMe">
                            <label class="form-check-label small text-muted" for="rememberMe">
                                Ingat Saya
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Pilihan Cepat Akun Demo -->
                <div class="mb-4 p-2 bg-light rounded-3 border">
                    <div class="small fw-semibold text-secondary mb-1">Pilihan Cepat Akun:</div>
                    <div class="btn-group w-100" role="group">
                        <button type="button" class="btn btn-sm btn-outline-primary" id="btnQuickOwner">
                            <i class="bi bi-shield-check me-1"></i> Owner
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-secondary" id="btnQuickAdmin">
                            <i class="bi bi-person-badge me-1"></i> Admin
                        </button>
                    </div>
                    <div class="text-muted" style="font-size: 0.72rem; margin-top: 4px;">
                        Password default: <code>password</code>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary w-100 py-2 fw-semibold shadow-sm">
                    <i class="bi bi-box-arrow-in-right me-1"></i> Masuk ke Sistem
                </button>
            </form>
        </div>

        <div class="card-footer bg-light text-center py-3 border-0">
            <span class="text-muted small">&copy; {{ date('Y') }} Sistem Operasional Agen JNE</span>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.getElementById('btnQuickOwner').addEventListener('click', function () {
        document.getElementById('usernameInput').value = 'owner_utama';
        document.getElementById('passwordInput').value = 'password';
    });

    document.getElementById('btnQuickAdmin').addEventListener('click', function () {
        document.getElementById('usernameInput').value = 'admin_operasional';
        document.getElementById('passwordInput').value = 'password';
    });
</script>
@endpush
