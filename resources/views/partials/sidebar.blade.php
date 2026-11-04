<!-- Sidebar Navigation -->
<aside class="app-sidebar">
    <!-- Brand / Logo -->
    <div class="sidebar-brand">
        <div class="sidebar-brand-icon">
            <i class="bi bi-box-seam-fill"></i>
        </div>
        <div>
            <div class="sidebar-brand-text">AGEN JNE</div>
            <div class="sidebar-brand-subtitle">Komisi & Operasional</div>
        </div>
    </div>

    <!-- Navigation Menu -->
    <nav class="sidebar-nav">
        @if(auth()->check() && auth()->user()->isOwner())
        <!-- Dashboard (Khusus Owner) -->
        <div class="sidebar-header" data-role="owner-only">Utama</div>
        <li class="nav-item" data-role="owner-only">
            <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <i class="bi bi-speedometer2"></i>
                <span>Dashboard</span>
            </a>
        </li>

        <!-- =========================================================================
             1. MASTER (Khusus Owner)
             ========================================================================= -->
        <div class="sidebar-header" data-role="owner-only">Master Data (Owner)</div>
        <li class="nav-item" data-role="owner-only">
            <a href="{{ route('master.user') }}" class="nav-link {{ request()->routeIs('master.user') ? 'active' : '' }}">
                <i class="bi bi-people-fill"></i>
                <span>Master User</span>
            </a>
        </li>
        <li class="nav-item" data-role="owner-only">
            <a href="{{ route('master.cabang') }}" class="nav-link {{ request()->routeIs('master.cabang') ? 'active' : '' }}">
                <i class="bi bi-buildings-fill"></i>
                <span>Master Cabang</span>
            </a>
        </li>
        <li class="nav-item" data-role="owner-only">
            <a href="{{ route('master.ops') }}" class="nav-link {{ request()->routeIs('master.ops') ? 'active' : '' }}">
                <i class="bi bi-tools"></i>
                <span>Master Ops</span>
            </a>
        </li>
        <li class="nav-item" data-role="owner-only">
            <a href="{{ route('master.status') }}" class="nav-link {{ request()->routeIs('master.status') ? 'active' : '' }}">
                <i class="bi bi-check-circle-fill"></i>
                <span>Master Status</span>
            </a>
        </li>
        @endif

        <!-- =========================================================================
             2. TRANSAKSI
             - Packing List: Owner dan Admin
             - Operasional & Komisi: Khusus Owner
             ========================================================================= -->
        <div class="sidebar-header">Transaksi</div>
        <li class="nav-item">
            <a href="{{ route('transaksi.packing-list') }}" class="nav-link {{ request()->routeIs('transaksi.packing-list') ? 'active' : '' }}">
                <i class="bi bi-clipboard-check-fill"></i>
                <span>Packing List (PL)</span>
            </a>
        </li>

        @if(auth()->check() && auth()->user()->isOwner())
        <li class="nav-item" data-role="owner-only">
            <a href="{{ route('transaksi.operasional') }}" class="nav-link {{ request()->routeIs('transaksi.operasional') ? 'active' : '' }}">
                <i class="bi bi-receipt-cutoff"></i>
                <span>Operasional</span>
            </a>
        </li>
        <li class="nav-item" data-role="owner-only">
            <a href="{{ route('transaksi.komisi') }}" class="nav-link {{ request()->routeIs('transaksi.komisi') ? 'active' : '' }}">
                <i class="bi bi-cash-stack"></i>
                <span>Komisi Bulanan</span>
            </a>
        </li>

        <!-- =========================================================================
             3. LAPORAN (Khusus Owner)
             ========================================================================= -->
        <div class="sidebar-header" data-role="owner-only">Laporan (Owner)</div>
        <li class="nav-item" data-role="owner-only">
            <a href="{{ route('laporan.packing-list') }}" class="nav-link {{ request()->routeIs('laporan.packing-list') ? 'active' : '' }}">
                <i class="bi bi-file-earmark-bar-graph-fill"></i>
                <span>Lap. Packing List</span>
            </a>
        </li>
        <li class="nav-item" data-role="owner-only">
            <a href="{{ route('laporan.operasional') }}" class="nav-link {{ request()->routeIs('laporan.operasional') ? 'active' : '' }}">
                <i class="bi bi-file-earmark-spreadsheet-fill"></i>
                <span>Lap. Operasional</span>
            </a>
        </li>
        <li class="nav-item" data-role="owner-only">
            <a href="{{ route('laporan.laba-bersih') }}" class="nav-link {{ request()->routeIs('laporan.laba-bersih') ? 'active' : '' }}">
                <i class="bi bi-graph-up-arrow"></i>
                <span>Lap. Laba Bersih</span>
            </a>
        </li>
        <li class="nav-item" data-role="owner-only">
            <a href="{{ route('laporan.komisi') }}" class="nav-link {{ request()->routeIs('laporan.komisi') ? 'active' : '' }}">
                <i class="bi bi-wallet-fill"></i>
                <span>Lap. Komisi Bulanan</span>
            </a>
        </li>
        @endif
    </nav>

    <!-- Sidebar Footer User Status -->
    <div class="sidebar-footer">
        <div class="d-flex align-items-center justify-content-between">
            <div class="text-truncate">
                <div class="small fw-semibold text-white">{{ auth()->user()->username ?? 'Pengguna' }}</div>
                <div class="text-muted" style="font-size: 0.72rem;">Role: {{ ucfirst(auth()->user()->role ?? '-') }}</div>
            </div>
            <form action="{{ route('logout') }}" method="POST" class="m-0 p-0">
                @csrf
                <button type="submit" class="btn btn-sm btn-outline-danger py-1 px-2" title="Keluar">
                    <i class="bi bi-box-arrow-right"></i>
                </button>
            </form>
        </div>
    </div>
</aside>
