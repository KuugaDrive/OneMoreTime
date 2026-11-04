@extends('layouts.app')

@section('title', 'Laporan Laba Bersih')

@section('content')
<!-- KOP LAPORAN UNTUK CETAK PDF (@media print) -->
<div class="print-only-header">
    <div class="kop-title">AGEN RESMI JNE EXPRESS</div>
    <div class="kop-subtitle">LAPORAN REKAPITULASI LABA BERSIH AGEN</div>
    <div class="kop-address">Jl. Tomang Raya No. 11, Jakarta Barat | Telp: (021) 566-7890 | Website: jne.co.id</div>
    <div class="print-period-box">
        Periode: <strong id="printPeriodText">{{ $startDate }} s/d {{ $endDate }}</strong> | Cabang: <strong>{{ $namaCabangKop ?? 'Semua Cabang' }}</strong> | Tanggal Cetak: <strong>{{ date('d-m-Y H:i') }}</strong>
    </div>
</div>

<!-- Header Halaman (Layar Normal) -->
<div class="page-header no-print">
    <div>
        <h1 class="page-title">
            <i class="bi bi-graph-up-arrow text-success"></i> Laporan Laba Bersih
        </h1>
        <p class="page-subtitle">Perhitungan laba bersih agen per bulan-tahun ({{ $cfg['rumus_keterangan'] ?? 'Laba Bersih = Komisi PL (Disc Charge) - Biaya Operasional' }}).</p>
    </div>
    <div class="d-flex align-items-center gap-2">
        <button type="button" class="btn btn-outline-secondary btn-sm px-3 shadow-sm" onclick="window.print()">
            <i class="bi bi-printer me-1"></i> Cetak Cepat
        </button>
        <a href="{{ route('laporan.laba-bersih.pdf', ['tanggal_awal' => $startDate, 'tanggal_akhir' => $endDate, 'id_cab' => $selectedCabang]) }}" id="btnDownloadPdf" class="btn btn-danger btn-sm px-3 shadow-sm">
            <i class="bi bi-file-earmark-pdf-fill me-1"></i> Unduh PDF Resmi
        </a>
    </div>
</div>

<!-- =========================================================================
     FILTER RANGE TANGGAL & CABANG
     ========================================================================= -->
<div class="card-app filter-card mb-4 no-print">
    <div class="card-header bg-light">
        <span class="d-flex align-items-center gap-2 fw-semibold">
            <i class="bi bi-funnel-fill text-primary"></i> Filter Periode Tanggal & Cabang Laporan
        </span>
    </div>
    <div class="card-body">
        <form method="GET" action="{{ route('laporan.laba-bersih') }}" id="filterLabaForm" class="row g-3 align-items-end">
            <div class="col-12 col-md-3">
                <label for="filterCabang" class="form-label">Cabang</label>
                <div class="input-group">
                    <span class="input-group-text bg-light"><i class="bi bi-buildings"></i></span>
                    <select class="form-select" name="id_cab" id="filterCabang">
                        <option value="all" {{ ($selectedCabang ?? 'all') === 'all' ? 'selected' : '' }}>Semua Cabang</option>
                        @foreach($cabangs as $cab)
                        <option value="{{ $cab->id_cab }}" {{ ($selectedCabang ?? '') === $cab->id_cab ? 'selected' : '' }}>
                            {{ $cab->nama_cabang }} ({{ $cab->id_cab }})
                        </option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-12 col-md-3">
                <label for="filterTanggalAwal" class="form-label">Tanggal Awal <span class="text-danger">*</span></label>
                <div class="input-group">
                    <span class="input-group-text bg-light"><i class="bi bi-calendar3"></i></span>
                    <input type="text" class="form-control datepicker" name="tanggal_awal" id="filterTanggalAwal" value="{{ $startDate }}" placeholder="dd-mm-yyyy" required>
                </div>
            </div>
            <div class="col-12 col-md-3">
                <label for="filterTanggalAkhir" class="form-label">Tanggal Akhir <span class="text-danger">*</span></label>
                <div class="input-group">
                    <span class="input-group-text bg-light"><i class="bi bi-calendar3"></i></span>
                    <input type="text" class="form-control datepicker" name="tanggal_akhir" id="filterTanggalAkhir" value="{{ $endDate }}" placeholder="dd-mm-yyyy" required>
                </div>
            </div>
            <div class="col-12 col-md-3">
                <button type="submit" class="btn btn-success btn-sm px-4 w-100" style="padding-top: 0.6rem; padding-bottom: 0.6rem;">
                    <i class="bi bi-filter me-1"></i> Tampilkan Laporan
                </button>
            </div>
        </form>
    </div>
</div>

<!-- =========================================================================
     TABEL REKAP LABA BERSIH PER BULAN-TAHUN
     ========================================================================= -->
@php
    $sumPL = array_sum(array_column($rekaps, 'total_pl'));
    $sumOps = array_sum(array_column($rekaps, 'total_ops'));
    $sumLaba = array_sum(array_column($rekaps, 'laba_bersih'));
@endphp

<div class="card-app mb-4">
    <div class="card-header">
        <span class="d-flex align-items-center gap-2 fw-bold text-dark">
            <i class="bi bi-cash-coin text-success"></i> Rekapitulasi Laba Bersih per Bulan-Tahun
        </span>
        <span class="badge bg-light text-dark border no-print">{{ count($rekaps) }} periode</span>
    </div>
    <div class="table-responsive">
        <table class="table table-app align-middle mb-0" id="reportLabaBersihTable">
            <thead>
                <tr>
                    <th style="width: 50px;">No</th>
                    <th>Bulan - Tahun</th>
                    <th class="text-end">{{ $cfg['pl_column_label'] ?? 'Pendapatan Komisi (Disc Charge)' }}</th>
                    <th class="text-end">Total Biaya Operasional</th>
                    <th class="text-end">Laba Bersih</th>
                    <th class="text-center" style="width: 120px;">Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($rekaps as $index => $row)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td class="fw-bold fs-6">{{ $row['bulan_tahun'] }}</td>
                    <td class="text-end fw-semibold text-primary">Rp {{ number_format($row['total_pl'], 2, ',', '.') }}</td>
                    <td class="text-end fw-semibold text-danger">Rp {{ number_format($row['total_ops'], 2, ',', '.') }}</td>
                    <td class="text-end fw-bold {{ $row['laba_bersih'] >= 0 ? 'text-success' : 'text-danger' }} fs-6">
                        Rp {{ number_format($row['laba_bersih'], 2, ',', '.') }}
                    </td>
                    <td class="text-center">
                        @if($row['laba_bersih'] >= 0)
                            <span class="badge bg-success-subtle text-success border border-success px-2 py-1">
                                <i class="bi bi-arrow-up-right me-1"></i>Surplus
                            </span>
                        @else
                            <span class="badge bg-danger-subtle text-danger border border-danger px-2 py-1">
                                <i class="bi bi-arrow-down-right me-1"></i>Defisit
                            </span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center py-3 text-muted">Tidak ada transaksi PL maupun Operasional pada rentang filter ini.</td>
                </tr>
                @endforelse
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="2" class="text-end text-uppercase fw-bold">Grand Total Keseluruhan:</td>
                    <td class="text-end text-primary fw-bold fs-6">Rp {{ number_format($sumPL, 2, ',', '.') }}</td>
                    <td class="text-end text-danger fw-bold fs-6">Rp {{ number_format($sumOps, 2, ',', '.') }}</td>
                    <td class="text-end fw-bold {{ $sumLaba >= 0 ? 'text-success' : 'text-danger' }} fs-5">
                        Rp {{ number_format($sumLaba, 2, ',', '.') }}
                    </td>
                    <td class="text-center">
                        <span class="badge {{ $sumLaba >= 0 ? 'bg-success' : 'bg-danger' }} px-2 py-1">
                            {{ $sumLaba >= 0 ? 'Profit Bersih' : 'Defisit Bersih' }}
                        </span>
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>

<!-- =========================================================================
     INFORMASI RUMUS PERHITUNGAN
     ========================================================================= -->
<div class="card-app no-print">
    <div class="card-header bg-light">
        <span class="fw-bold small text-secondary">
            <i class="bi bi-info-circle-fill text-primary me-1"></i> Catatan Rumus Finansial
        </span>
    </div>
    <div class="card-body py-3">
        <div class="alert alert-info py-2 px-3 mb-2 d-flex align-items-center gap-2">
            <i class="bi bi-calculator-fill"></i>
            <span class="small fw-semibold">{{ $cfg['rumus_keterangan'] ?? 'Laba Bersih = Komisi PL (Disc Charge) - Biaya Operasional' }}</span>
        </div>
        <ul class="mb-0 small text-muted">
            <li><strong>{{ $cfg['pl_column_label'] ?? 'Pendapatan Komisi (Disc Charge)' }}:</strong> 
                @if(($cfg['basis'] ?? '') === 'grand_total')
                    Akumulasi nilai Grand Total PL dari transaksi harian.
                @else
                    Akumulasi komisi agen (Discount Charge pada dokumen Packing List JNE) dari transaksi harian.
                @endif
            </li>
            <li><strong>Total Biaya Operasional:</strong> Akumulasi seluruh pengeluaran beban operasional cabang pada periode terkait.</li>
            @if(!empty($cfg['include_komisi']))
            <li><strong>Komisi Bulanan:</strong> Akumulasi penerimaan komisi bulanan dari JNE pusat ditambahkan ke Laba Bersih sesuai konfigurasi.</li>
            @endif
        </ul>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const inputAwal = document.getElementById('filterTanggalAwal');
        const inputAkhir = document.getElementById('filterTanggalAkhir');
        const selectCab = document.getElementById('filterCabang');
        const btnPdf = document.getElementById('btnDownloadPdf');

        const updateLinks = () => {
            if (inputAwal && inputAkhir && btnPdf) {
                const cabVal = selectCab ? selectCab.value : 'all';
                btnPdf.href = `{{ route('laporan.laba-bersih.pdf') }}?tanggal_awal=${encodeURIComponent(inputAwal.value)}&tanggal_akhir=${encodeURIComponent(inputAkhir.value)}&id_cab=${encodeURIComponent(cabVal)}`;
            }
        };
        if (inputAwal) inputAwal.addEventListener('change', updateLinks);
        if (inputAkhir) inputAkhir.addEventListener('change', updateLinks);
        if (selectCab) selectCab.addEventListener('change', updateLinks);
    });
</script>
@endpush
