@extends('layouts.app')

@section('title', 'Dashboard Ringkasan')

@section('content')
<div class="page-header flex-wrap gap-2">
    <div>
        <h1 class="page-title">
            <i class="bi bi-speedometer2 text-primary"></i> Dashboard Ringkasan
        </h1>
        <p class="page-subtitle">Ringkasan performa komisi harian packing list, biaya operasional, dan estimasi laba bersih agen.</p>
    </div>
    <div class="d-flex align-items-center gap-2">
        <!-- Filter Cabang Sederhana (BAGIAN A.8) -->
        <form method="GET" action="{{ route('dashboard') }}" class="d-flex align-items-center gap-2 m-0">
            <div class="input-group input-group-sm">
                <span class="input-group-text bg-white"><i class="bi bi-buildings"></i></span>
                <select name="id_cab" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="all" {{ empty($idCab) || $idCab === 'all' ? 'selected' : '' }}>Semua Cabang</option>
                    @foreach($cabangs as $cab)
                    <option value="{{ $cab->id_cab }}" {{ ($idCab ?? '') === $cab->id_cab ? 'selected' : '' }}>
                        {{ $cab->nama_cabang }} ({{ $cab->id_cab }})
                    </option>
                    @endforeach
                </select>
            </div>
        </form>
        <a href="{{ route('transaksi.packing-list') }}" class="btn btn-primary btn-sm px-3 shadow-sm text-nowrap">
            <i class="bi bi-plus-circle me-1"></i> Transaksi PL
        </a>
    </div>
</div>

<!-- =========================================================================
     RINGKASAN STATISTIK
     ========================================================================= -->
<div class="row g-3 mb-4">
    <!-- Kartu 1: Total Komisi PL (Disc Charge) -->
    <div class="col-12 col-sm-6 col-xl-4">
        <div class="stat-card stat-card-blue">
            <div class="stat-icon"><i class="bi bi-wallet2"></i></div>
            <div class="stat-title">Total Komisi PL (Disc Charge)</div>
            <div class="stat-value" id="cardTotalKomisiPL">Rp {{ number_format($summary['total_komisi_pl'], 2, ',', '.') }}</div>
            <div class="stat-desc">
                <i class="bi bi-check-circle me-1"></i> Komisi agen dari {{ $summary['total_trx_pl'] }} transaksi ({{ $summary['total_paket'] }} paket)
            </div>
        </div>
    </div>

    <!-- Kartu 2: Total Grand Total PL (Setoran ke JNE) -->
    <div class="col-12 col-sm-6 col-xl-4">
        <div class="stat-card stat-card-cyan">
            <div class="stat-icon"><i class="bi bi-box-seam"></i></div>
            <div class="stat-title">Total Grand Total PL (Setoran ke JNE)</div>
            <div class="stat-value" id="cardTotalGrandTotalPL">Rp {{ number_format($summary['total_grand_total_pl'], 2, ',', '.') }}</div>
            <div class="stat-desc">
                <i class="bi bi-arrow-up-right-circle me-1"></i> Estimasi setoran ke JNE (Total &minus; Disc &minus; eVoucher)
            </div>
        </div>
    </div>

    <!-- Kartu 3: Total Operasional (Owner Only) -->
    <div class="col-12 col-sm-6 col-xl-4">
        <div class="stat-card stat-card-rose">
            <div class="stat-icon"><i class="bi bi-receipt-cutoff"></i></div>
            <div class="stat-title">Total Biaya Operasional</div>
            <div class="stat-value" id="cardTotalOps">Rp {{ number_format($summary['total_operasional'], 2, ',', '.') }}</div>
            <div class="stat-desc">
                <i class="bi bi-arrow-down-circle me-1"></i> Dari {{ $summary['total_trx_ops'] }} catatan operasional
            </div>
        </div>
    </div>

    <!-- Kartu 4: Laba Bersih (Owner Only) -->
    <div class="col-12 col-sm-6 col-xl-6">
        <div class="stat-card stat-card-emerald">
            <div class="stat-icon"><i class="bi bi-graph-up-arrow"></i></div>
            <div class="stat-title">Estimasi Laba Bersih</div>
            <div class="stat-value" id="cardLabaBersih">Rp {{ number_format($summary['laba_bersih'], 2, ',', '.') }}</div>
            <div class="stat-desc">
                <i class="bi bi-calculator me-1"></i> {{ $summary['config']['rumus_keterangan'] ?? 'Laba Bersih = Komisi PL (Disc Charge) - Biaya Operasional' }}
            </div>
        </div>
    </div>

    <!-- Kartu 5: Total Komisi Bulanan Ditransfer (Owner Only) -->
    <div class="col-12 col-sm-6 col-xl-6">
        <div class="stat-card stat-card-amber">
            <div class="stat-icon"><i class="bi bi-bank"></i></div>
            <div class="stat-title">Total Komisi Bulanan</div>
            <div class="stat-value" id="cardTotalKomisi">Rp {{ number_format($summary['total_komisi'], 2, ',', '.') }}</div>
            <div class="stat-desc">
                <i class="bi bi-info-circle me-1"></i> Akumulasi pencairan transfer pusat {{ !empty($summary['config']['include_komisi']) ? '(Dihitung ke Laba Bersih)' : '(Tidak dihitung ke Laba Bersih)' }}
            </div>
        </div>
    </div>
</div>

<!-- =========================================================================
     TABEL RINGKASAN PER CABANG (BAGIAN A.8)
     ========================================================================= -->
<div class="card-app mb-4">
    <div class="card-header bg-white">
        <span class="d-flex align-items-center gap-2 text-primary fw-bold">
            <i class="bi bi-buildings-fill"></i>
            Ringkasan Keuangan per Cabang
        </span>
    </div>
    <div class="table-responsive">
        <table class="table table-app align-middle mb-0">
            <thead>
                <tr>
                    <th style="width: 50px;">No</th>
                    <th>Kode Cabang</th>
                    <th>Nama Cabang</th>
                    <th class="text-end">Komisi PL (Disc Charge)</th>
                    <th class="text-end">Grand Total (Setoran JNE)</th>
                    <th class="text-end">Total Operasional</th>
                    <th class="text-end">Laba Bersih</th>
                </tr>
            </thead>
            <tbody>
                @forelse($ringkasanCabang as $idx => $rc)
                <tr>
                    <td>{{ $idx + 1 }}</td>
                    <td><span class="badge bg-light text-dark border fw-bold">{{ $rc['id_cab'] }}</span></td>
                    <td class="fw-semibold">{{ $rc['nama_cabang'] }}</td>
                    <td class="text-end text-primary fw-bold">Rp {{ number_format($rc['total_komisi_pl'], 2, ',', '.') }}</td>
                    <td class="text-end text-secondary">Rp {{ number_format($rc['total_grand_total_pl'], 2, ',', '.') }}</td>
                    <td class="text-end text-danger fw-bold">Rp {{ number_format($rc['total_ops'], 2, ',', '.') }}</td>
                    <td class="text-end fw-bold {{ $rc['laba_bersih'] >= 0 ? 'text-success' : 'text-danger' }}">
                        Rp {{ number_format($rc['laba_bersih'], 2, ',', '.') }}
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center py-3 text-muted">Belum ada data cabang</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- =========================================================================
     TABEL AKTIVITAS TERKINI
     ========================================================================= -->
<div class="row g-4">
    <!-- Transaksi Packing List Terkini -->
    <div class="col-12 col-lg-7">
        <div class="card-app">
            <div class="card-header">
                <span class="d-flex align-items-center gap-2">
                    <i class="bi bi-clock-history text-primary"></i>
                    Transaksi Packing List Terakhir
                </span>
                <a href="{{ route('transaksi.packing-list') }}" class="btn btn-sm btn-outline-primary py-0 px-2" style="font-size: 0.78rem;">
                    Lihat Semua
                </a>
            </div>
            <div class="table-responsive">
                <table class="table table-app mb-0">
                    <thead>
                        <tr>
                            <th>Kode Trx</th>
                            <th>Cabang</th>
                            <th>Tanggal</th>
                            <th>Jml Paket</th>
                            <th class="text-end">Grand Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentPL as $pl)
                        <tr>
                            <td><span class="badge bg-light text-dark border">{{ $pl['kode_trx_pl'] }}</span></td>
                            <td><span class="badge bg-light text-primary border">{{ $pl['nama_cabang'] }}</span></td>
                            <td>{{ $pl['tanggal_pl'] }}</td>
                            <td><span class="badge bg-secondary">{{ $pl['jumlah_paket'] }} paket</span></td>
                            <td class="text-end fw-bold text-primary">Rp {{ number_format($pl['grand_total'], 2, ',', '.') }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-3 text-muted">Belum ada data transaksi PL</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Transaksi Operasional Terkini -->
    <div class="col-12 col-lg-5">
        <div class="card-app">
            <div class="card-header">
                <span class="d-flex align-items-center gap-2">
                    <i class="bi bi-receipt text-danger"></i>
                    Biaya Operasional Terakhir
                </span>
                <a href="{{ route('transaksi.operasional') }}" class="btn btn-sm btn-outline-danger py-0 px-2" style="font-size: 0.78rem;">
                    Lihat Semua
                </a>
            </div>
            <div class="table-responsive">
                <table class="table table-app mb-0">
                    <thead>
                        <tr>
                            <th>Kode Trx</th>
                            <th>Cabang</th>
                            <th>Beban Ops</th>
                            <th class="text-end">Nominal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentOps as $ops)
                        <tr>
                            <td><span class="badge bg-light text-dark border">{{ $ops['kode_trx_ops'] }}</span></td>
                            <td><span class="badge bg-light text-primary border">{{ $ops['nama_cabang'] }}</span></td>
                            <td>{{ $ops['beban_ops'] }}</td>
                            <td class="text-end fw-bold text-danger">Rp {{ number_format($ops['nominal'], 2, ',', '.') }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center py-3 text-muted">Belum ada data transaksi operasional</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
