<!-- Topbar Header -->
<header class="app-topbar">
    <div class="topbar-left">
        <!-- Toggle button untuk responsive mobile sidebar -->
        <button type="button" class="sidebar-toggle-btn d-lg-none" id="sidebarToggleBtn" aria-label="Toggle Sidebar">
            <i class="bi bi-list"></i>
        </button>

        <div class="d-none d-md-flex align-items-center gap-2">
            <span class="fw-semibold text-secondary small">Sistem Operasional & Komisi Agen JNE</span>
            <span class="text-muted">|</span>
            <span class="badge bg-light text-dark border">
                <i class="bi bi-calendar-event me-1 text-primary"></i>
                <span id="topbarCurrentDate">{{ date('d-m-Y') }}</span>
            </span>
        </div>
    </div>

    <div class="topbar-right">
        <!-- User Profile Dropdown -->
        <div class="dropdown">
            <button class="btn btn-sm btn-light border dropdown-toggle d-flex align-items-center gap-2" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center" style="width: 28px; height: 28px; font-size: 0.8rem; font-weight: 700;">
                    <i class="bi bi-person"></i>
                </div>
                <div class="text-start d-none d-sm-block" style="line-height: 1.1;">
                    <div class="fw-bold small" id="topbarUserName">{{ auth()->user()->username ?? 'Pengguna' }}</div>
                    <span id="currentRoleBadge" class="badge {{ (auth()->user() && auth()->user()->isOwner()) ? 'bg-primary' : 'bg-warning text-dark' }}" style="font-size: 0.65rem;">
                        {{ ucfirst(auth()->user()->role ?? 'Guest') }}
                    </span>
                </div>
            </button>
            <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                <li class="dropdown-header">Informasi Pengguna</li>
                <li>
                    <div class="px-3 py-1 small text-muted">
                        <div>Username: <strong>{{ auth()->user()->username ?? '-' }}</strong></div>
                        <div>ID: <strong>{{ auth()->user()->id_user ?? '-' }}</strong></div>
                        <div>Role: <span class="badge {{ (auth()->user() && auth()->user()->isOwner()) ? 'bg-primary' : 'bg-warning text-dark' }}">{{ ucfirst(auth()->user()->role ?? '-') }}</span></div>
                    </div>
                </li>
                <li><hr class="dropdown-divider"></li>
                <li>
                    <form action="{{ route('logout') }}" method="POST" class="m-0 p-0">
                        @csrf
                        <button type="submit" class="dropdown-item text-danger d-flex align-items-center gap-2">
                            <i class="bi bi-box-arrow-right"></i> Keluar (Logout)
                        </button>
                    </form>
                </li>
            </ul>
        </div>
    </div>
</header>
