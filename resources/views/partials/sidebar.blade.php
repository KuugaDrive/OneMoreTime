<!-- Sidebar Navigation -->
<aside class="app-sidebar" id="appSidebar">
    <!-- Brand / Identitas Usaha -->
    <div class="sidebar-brand-box">
        @if(config('app.logo_path') && file_exists(public_path(config('app.logo_path'))))
            <img src="{{ asset(config('app.logo_path')) }}" alt="Logo" class="sidebar-brand-logo" style="object-fit: contain;">
        @else
            <div class="sidebar-brand-logo">
                {{ strtoupper(substr(config('app.nama_usaha', 'A'), 0, 1)) }}
            </div>
        @endif
        <div class="sidebar-brand-text">
            <div class="sidebar-brand-title">{{ config('app.name', 'Sistem Administrasi') }}</div>
            <div class="sidebar-brand-subtitle">{{ config('app.nama_usaha', 'Nama Usaha') }}</div>
        </div>
    </div>

    <!-- Navigation Menu -->
    <nav class="sidebar-nav-container">
        @if(auth()->check() && auth()->user()->isOwner())
            <!-- Dashboard Utama -->
            <ul class="sidebar-nav-list mb-2">
                <li class="sidebar-nav-item">
                    <a href="{{ route('dashboard') }}" class="sidebar-nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                        <i class="bi bi-grid-1x2"></i>
                        <span>Dashboard</span>
                    </a>
                </li>
            </ul>

            <!-- =========================================================================
                 1. MASTER DATA (Khusus Owner)
                 ========================================================================= -->
            <div class="sidebar-section-title">Master Data</div>
            <ul class="sidebar-nav-list mb-2">
                <li class="sidebar-nav-item">
                    <a href="{{ route('master.user') }}" class="sidebar-nav-link {{ request()->routeIs('master.user') ? 'active' : '' }}">
                        <i class="bi bi-people"></i>
                        <span>Pengguna</span>
                    </a>
                </li>
                <li class="sidebar-nav-item">
                    <a href="{{ route('master.cabang') }}" class="sidebar-nav-link {{ request()->routeIs('master.cabang') ? 'active' : '' }}">
                        <i class="bi bi-buildings"></i>
                        <span>Cabang</span>
                    </a>
                </li>
                <li class="sidebar-nav-item">
                    <a href="{{ route('master.ops') }}" class="sidebar-nav-link {{ request()->routeIs('master.ops') ? 'active' : '' }}">
                        <i class="bi bi-tag"></i>
                        <span>Beban Operasional</span>
                    </a>
                </li>
                <li class="sidebar-nav-item">
                    <a href="{{ route('master.status') }}" class="sidebar-nav-link {{ request()->routeIs('master.status') ? 'active' : '' }}">
                        <i class="bi bi-shield-check"></i>
                        <span>Status Pengguna</span>
                    </a>
                </li>
            </ul>
        @endif

        <!-- =========================================================================
             2. TRANSAKSI
             ========================================================================= -->
        <div class="sidebar-section-title">Transaksi</div>
        <ul class="sidebar-nav-list mb-2">
            <li class="sidebar-nav-item">
                <a href="{{ route('transaksi.packing-list') }}" class="sidebar-nav-link {{ request()->routeIs('transaksi.packing-list') ? 'active' : '' }}">
                    <i class="bi bi-box-seam"></i>
                    <span>Packing List</span>
                </a>
            </li>

            @if(auth()->check() && auth()->user()->isOwner())
                <li class="sidebar-nav-item">
                    <a href="{{ route('transaksi.operasional') }}" class="sidebar-nav-link {{ request()->routeIs('transaksi.operasional') ? 'active' : '' }}">
                        <i class="bi bi-receipt"></i>
                        <span>Biaya Operasional</span>
                    </a>
                </li>
                <li class="sidebar-nav-item">
                    <a href="{{ route('transaksi.komisi') }}" class="sidebar-nav-link {{ request()->routeIs('transaksi.komisi') ? 'active' : '' }}">
                        <i class="bi bi-cash-stack"></i>
                        <span>Komisi Bulanan</span>
                    </a>
                </li>
            @endif
        </ul>

        @if(auth()->check() && auth()->user()->isOwner())
            <!-- =========================================================================
                 3. LAPORAN (Khusus Owner)
                 ========================================================================= -->
            <div class="sidebar-section-title">Laporan</div>
            <ul class="sidebar-nav-list">
                <li class="sidebar-nav-item">
                    <a href="{{ route('laporan.packing-list') }}" class="sidebar-nav-link {{ request()->routeIs('laporan.packing-list') ? 'active' : '' }}">
                        <i class="bi bi-file-earmark-text"></i>
                        <span>Laporan Packing List</span>
                    </a>
                </li>
                <li class="sidebar-nav-item">
                    <a href="{{ route('laporan.operasional') }}" class="sidebar-nav-link {{ request()->routeIs('laporan.operasional') ? 'active' : '' }}">
                        <i class="bi bi-file-earmark-spreadsheet"></i>
                        <span>Laporan Operasional</span>
                    </a>
                </li>
                <li class="sidebar-nav-item">
                    <a href="{{ route('laporan.laba-bersih') }}" class="sidebar-nav-link {{ request()->routeIs('laporan.laba-bersih') ? 'active' : '' }}">
                        <i class="bi bi-graph-up"></i>
                        <span>Laporan Laba Bersih</span>
                    </a>
                </li>
                <li class="sidebar-nav-item">
                    <a href="{{ route('laporan.komisi') }}" class="sidebar-nav-link {{ request()->routeIs('laporan.komisi') ? 'active' : '' }}">
                        <i class="bi bi-journal-check"></i>
                        <span>Laporan Komisi</span>
                    </a>
                </li>
            </ul>
        @endif
    </nav>

    <!-- Sidebar Footer -->
    <div class="sidebar-footer-box">
        <div class="text-truncate me-2">
            <div class="text-white small fw-semibold text-truncate">{{ auth()->user()->username ?? 'Pengguna' }}</div>
            <div style="font-size: 0.72rem; color: var(--sidebar-text);">
                {{ ucfirst(auth()->user()->role ?? '-') }}
                @if(auth()->user() && auth()->user()->isAdmin() && auth()->user()->cabang)
                    &middot; {{ auth()->user()->cabang->nama_cabang }}
                @endif
            </div>
        </div>
        <form action="{{ route('logout') }}" method="POST" class="m-0 p-0">
            @csrf
            <button type="submit" class="btn btn-sm btn-outline-secondary text-white py-1 px-2" style="border-color: rgba(255,255,255,0.2);" title="Keluar">
                <i class="bi bi-box-arrow-right"></i>
            </button>
        </form>
    </div>
</aside>
