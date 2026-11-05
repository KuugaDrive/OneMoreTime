@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="page-header d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
    <div>
        <h1 class="page-title">Dashboard Ringkasan</h1>
        <p class="page-subtitle">Ringkasan performa komisi harian packing list, pengeluaran operasional, dan estimasi laba bersih.</p>
    </div>
    <div class="d-flex align-items-center gap-2">
        <!-- Filter Cabang -->
        <form method="GET" action="{{ route('dashboard') }}" class="d-flex align-items-center gap-2 m-0">
            <div class="input-group input-group-sm" style="min-width: 220px;">
                <span class="input-group-text bg-white border-end-0 text-secondary"><i class="bi bi-buildings"></i></span>
                <select name="id_cab" class="form-select form-select-sm border-start-0 ps-0" onchange="this.form.submit()">
                    <option value="all" {{ empty($idCab) || $idCab === 'all' ? 'selected' : '' }}>Semua Cabang</option>
                    @foreach($cabangs as $cab)
                    <option value="{{ $cab->id_cab }}" {{ ($idCab ?? '') === $cab->id_cab ? 'selected' : '' }}>
                        {{ $cab->nama_cabang }} ({{ $cab->id_cab }})
                    </option>
                    @endforeach
                </select>
            </div>
        </form>
        <a href="{{ route('transaksi.packing-list') }}" class="btn btn-primary btn-sm px-3 text-nowrap">
            <i class="bi bi-plus me-1"></i> Packing List
        </a>
    </div>
</div>

<!-- =========================================================================
     RINGKASAN METRIK KEUANGAN
     ========================================================================= -->
<div class="row g-3 mb-4">
    <!-- Kartu 1: Total Komisi PL (Disc Charge) -->
    <div class="col-12 col-sm-6 col-xl-4">
        <div class="metric-card-flat">
            <div class="metric-title">Komisi PL (Disc Charge)</div>
            <div class="metric-value text-dark" id="cardTotalKomisiPL">
                Rp {{ number_format($summary['total_komisi_pl'], 2, ',', '.') }}
            </div>
            <div class="metric-note">
                Dari {{ $summary['total_trx_pl'] }} transaksi ({{ $summary['total_paket'] }} paket)
            </div>
        </div>
    </div>

    <!-- Kartu 2: Total Grand Total PL (Setoran ke JNE) -->
    <div class="col-12 col-sm-6 col-xl-4">
        <div class="metric-card-flat">
            <div class="metric-title">Setoran ke JNE (Grand Total)</div>
            <div class="metric-value text-dark" id="cardTotalGrandTotalPL">
                Rp {{ number_format($summary['total_grand_total_pl'], 2, ',', '.') }}
            </div>
            <div class="metric-note">
                Total bruto dikurangi diskon &amp; e-voucher
            </div>
        </div>
    </div>

    <!-- Kartu 3: Total Operasional -->
    <div class="col-12 col-sm-6 col-xl-4">
        <div class="metric-card-flat">
            <div class="metric-title">Biaya Operasional</div>
            <div class="metric-value text-dark" id="cardTotalOps">
                Rp {{ number_format($summary['total_operasional'], 2, ',', '.') }}
            </div>
            <div class="metric-note">
                Dari {{ $summary['total_trx_ops'] }} catatan pengeluaran
            </div>
        </div>
    </div>

    <!-- Kartu 4: Laba Bersih -->
    <div class="col-12 col-sm-6 col-xl-6">
        <div class="metric-card-flat">
            <div class="metric-title">Estimasi Laba Bersih</div>
            <div class="metric-value {{ $summary['laba_bersih'] >= 0 ? 'text-success' : 'text-danger' }}" id="cardLabaBersih">
                Rp {{ number_format($summary['laba_bersih'], 2, ',', '.') }}
            </div>
            <div class="metric-note">
                {{ $summary['config']['rumus_keterangan'] ?? 'Laba Bersih = Komisi PL - Biaya Operasional' }}
            </div>
        </div>
    </div>

    <!-- Kartu 5: Total Komisi Bulanan -->
    <div class="col-12 col-sm-6 col-xl-6">
        <div class="metric-card-flat">
            <div class="metric-title">Komisi Bulanan Ditransfer</div>
            <div class="metric-value text-dark" id="cardTotalKomisi">
                Rp {{ number_format($summary['total_komisi'], 2, ',', '.') }}
            </div>
            <div class="metric-note">
                {{ !empty($summary['config']['include_komisi']) ? 'Termasuk dalam perhitungan laba bersih' : 'Tidak dihitung dalam rumus laba bersih' }}
            </div>
        </div>
    </div>
</div>

<!-- =========================================================================
     TABEL RINGKASAN PER CABANG
     ========================================================================= -->
<div class="card-app mb-4">
    <div class="card-header py-2 px-3">
        <span class="fw-semibold text-dark" style="font-size: 13px;">Ringkasan Keuangan per Cabang</span>
    </div>
    <div class="table-responsive">
        <table class="table table-app align-middle mb-0">
            <thead>
                <tr>
                    <th style="width: 45px;">No</th>
                    <th>Kode</th>
                    <th>Nama Cabang</th>
                    <th class="text-end">Komisi PL (Disc Charge)</th>
                    <th class="text-end">Setoran ke JNE</th>
                    <th class="text-end">Operasional</th>
                    <th class="text-end">Laba Bersih</th>
                </tr>
            </thead>
            <tbody>
                @forelse($ringkasanCabang as $idx => $rc)
                <tr>
                    <td class="row-number text-secondary">{{ $idx + 1 }}</td>
                    <td><span class="font-monospace text-dark fw-medium">{{ $rc['id_cab'] }}</span></td>
                    <td class="fw-medium text-dark">{{ $rc['nama_cabang'] }}</td>
                    <td class="text-end tabular-nums">Rp {{ number_format($rc['total_komisi_pl'], 2, ',', '.') }}</td>
                    <td class="text-end tabular-nums text-secondary">Rp {{ number_format($rc['total_grand_total_pl'], 2, ',', '.') }}</td>
                    <td class="text-end tabular-nums text-secondary">Rp {{ number_format($rc['total_ops'], 2, ',', '.') }}</td>
                    <td class="text-end tabular-nums fw-semibold {{ $rc['laba_bersih'] >= 0 ? 'text-success' : 'text-danger' }}">
                        Rp {{ number_format($rc['laba_bersih'], 2, ',', '.') }}
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center py-3 text-secondary">Belum ada data cabang</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- =========================================================================
     TABEL AKTIVITAS TERKINI
     ========================================================================= -->
<div class="row g-3">
    <!-- Transaksi Packing List Terkini -->
    <div class="col-12 col-lg-7">
        <div class="card-app h-100">
            <div class="card-header d-flex justify-content-between align-items-center py-2 px-3">
                <span class="fw-semibold text-dark" style="font-size: 13px;">Packing List Terkini</span>
                <a href="{{ route('transaksi.packing-list') }}" class="btn btn-sm btn-outline-secondary py-0 px-2" style="font-size: 12px;">
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
                            <th>Paket</th>
                            <th class="text-end">Grand Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentPL as $pl)
                        <tr>
                            <td><span class="font-monospace text-dark">{{ $pl['kode_trx_pl'] }}</span></td>
                            <td><span class="text-dark">{{ $pl['nama_cabang'] }}</span></td>
                            <td class="tabular-nums">{{ $pl['tanggal_pl'] }}</td>
                            <td class="tabular-nums">{{ $pl['jumlah_paket'] }}</td>
                            <td class="text-end tabular-nums fw-medium text-dark">Rp {{ number_format($pl['grand_total'], 2, ',', '.') }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-3 text-secondary">Belum ada transaksi</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Transaksi Operasional Terkini -->
    <div class="col-12 col-lg-5">
        <div class="card-app h-100">
            <div class="card-header d-flex justify-content-between align-items-center py-2 px-3">
                <span class="fw-semibold text-dark" style="font-size: 13px;">Biaya Operasional Terkini</span>
                <a href="{{ route('transaksi.operasional') }}" class="btn btn-sm btn-outline-secondary py-0 px-2" style="font-size: 12px;">
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
                            <td><span class="font-monospace text-dark">{{ $ops['kode_trx_ops'] }}</span></td>
                            <td><span class="text-dark">{{ $ops['nama_cabang'] }}</span></td>
                            <td><span class="text-secondary text-truncate d-inline-block" style="max-width: 130px;">{{ $ops['beban_ops'] }}</span></td>
                            <td class="text-end tabular-nums fw-medium text-dark">Rp {{ number_format($ops['nominal'], 2, ',', '.') }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center py-3 text-secondary">Belum ada pengeluaran</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
