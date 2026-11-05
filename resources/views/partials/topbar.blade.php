@php
    $routeName = Route::currentRouteName() ?? '';
    $breadcrumbCategory = '';
    $breadcrumbItem = 'Beranda';

    if (str_starts_with($routeName, 'master.')) {
        $breadcrumbCategory = 'Master Data';
        $breadcrumbItem = match($routeName) {
            'master.user' => 'Pengguna',
            'master.cabang' => 'Cabang',
            'master.ops' => 'Beban Operasional',
            'master.status' => 'Status',
            default => 'Master'
        };
    } elseif (str_starts_with($routeName, 'transaksi.')) {
        $breadcrumbCategory = 'Transaksi';
        $breadcrumbItem = match($routeName) {
            'transaksi.packing-list' => 'Packing List',
            'transaksi.operasional' => 'Biaya Operasional',
            'transaksi.komisi' => 'Komisi Bulanan',
            default => 'Transaksi'
        };
    } elseif (str_starts_with($routeName, 'laporan.')) {
        $breadcrumbCategory = 'Laporan';
        $breadcrumbItem = match($routeName) {
            'laporan.packing-list' => 'Packing List',
            'laporan.operasional' => 'Biaya Operasional',
            'laporan.laba-bersih' => 'Laba Bersih',
            'laporan.komisi' => 'Komisi Bulanan',
            default => 'Laporan'
        };
    } elseif ($routeName === 'dashboard') {
        $breadcrumbCategory = 'Ringkasan';
        $breadcrumbItem = 'Dashboard';
    }
@endphp

<header class="app-topbar">
    <div class="topbar-left">
        <!-- Toggle button untuk responsive mobile drawer -->
        <button type="button" class="sidebar-toggle-btn d-lg-none" id="sidebarToggleBtn" aria-label="Buka Menu Navigasi">
            <i class="bi bi-list"></i>
        </button>

        <!-- Breadcrumb hierarki -->
        <nav aria-label="breadcrumb" class="d-none d-sm-flex align-items-center">
            <ol class="breadcrumb m-0 small">
                @if($breadcrumbCategory)
                    <li class="breadcrumb-item text-secondary">{{ $breadcrumbCategory }}</li>
                @endif
                <li class="breadcrumb-item active fw-semibold text-dark" aria-current="page">{{ $breadcrumbItem }}</li>
            </ol>
        </nav>
    </div>

    <div class="topbar-right">
        @auth
            <div class="dropdown">
                <button class="btn btn-sm btn-light border dropdown-toggle d-flex align-items-center gap-2 py-1 px-2" type="button" data-bs-toggle="dropdown" aria-expanded="false" id="userMenuDropdown">
                    <span class="fw-medium small text-dark" id="topbarUserName">{{ auth()->user()->username }}</span>
                    @if(auth()->user()->isOwner())
                        <span class="badge badge-role-owner">Owner</span>
                    @else
                        <span class="badge badge-role-admin">Admin</span>
                        @if(auth()->user()->cabang)
                            <span class="text-secondary small d-none d-md-inline">• {{ auth()->user()->cabang->nama_cabang }}</span>
                        @endif
                    @endif
                </button>
                <ul class="dropdown-menu dropdown-menu-end shadow-sm border" aria-labelledby="userMenuDropdown" style="min-width: 220px; font-size: 13px;">
                    <li class="dropdown-header text-uppercase text-secondary fw-semibold" style="font-size: 11px; letter-spacing: 0.5px;">Profil Pengguna</li>
                    <li class="px-3 py-1 text-secondary">
                        <div class="fw-semibold text-dark">{{ auth()->user()->username }}</div>
                        <div class="small">ID: {{ auth()->user()->id_user }}</div>
                        <div class="small">Peran: {{ ucfirst(auth()->user()->role) }}</div>
                        @if(auth()->user()->cabang)
                            <div class="small">Cabang: {{ auth()->user()->cabang->nama_cabang }} ({{ auth()->user()->id_cab }})</div>
                        @endif
                    </li>
                    <li><hr class="dropdown-divider my-2"></li>
                    <li>
                        <form action="{{ route('logout') }}" method="POST" class="m-0 p-0">
                            @csrf
                            <button type="submit" class="dropdown-item text-danger d-flex align-items-center gap-2 py-2">
                                <i class="bi bi-box-arrow-right"></i> Keluar
                            </button>
                        </form>
                    </li>
                </ul>
            </div>
        @endauth
    </div>
</header>
