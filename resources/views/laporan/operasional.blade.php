@extends('layouts.app')

@section('title', 'Laporan Operasional')

@section('content')
<!-- KOP LAPORAN UNTUK CETAK PDF (@media print) -->
<div class="print-only-header">
    <div class="kop-title">AGEN RESMI JNE EXPRESS</div>
    <div class="kop-subtitle">LAPORAN BIAYA OPERASIONAL CABANG</div>
    <div class="kop-address">Jl. Tomang Raya No. 11, Jakarta Barat | Telp: (021) 566-7890 | Website: jne.co.id</div>
    <div class="print-period-box">
        Periode: <strong id="printPeriodText">{{ $startDate }} s/d {{ $endDate }}</strong> | Cabang: <strong>{{ $namaCabangKop ?? 'Semua Cabang' }}</strong> | Tanggal Cetak: <strong>{{ date('d-m-Y H:i') }}</strong>
    </div>
</div>

<!-- Header Halaman (Layar Normal) -->
<div class="page-header no-print">
    <div>
        <h1 class="page-title">
            <i class="bi bi-file-earmark-spreadsheet-fill text-danger"></i> Laporan Biaya Operasional
        </h1>
        <p class="page-subtitle">Rincian pengeluaran operasional cabang dan rekapitulasi total beban per bulan-tahun.</p>
    </div>
    <div class="d-flex align-items-center gap-2">
        <button type="button" class="btn btn-outline-secondary btn-sm px-3 shadow-sm" onclick="window.print()">
            <i class="bi bi-printer me-1"></i> Cetak Cepat
        </button>
        <a href="{{ route('laporan.operasional.pdf', ['tanggal_awal' => $startDate, 'tanggal_akhir' => $endDate, 'id_cab' => $selectedCabang]) }}" id="btnDownloadPdf" class="btn btn-danger btn-sm px-3 shadow-sm">
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
        <form method="GET" action="{{ route('laporan.operasional') }}" id="filterOpsForm" class="row g-3 align-items-end">
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
                <button type="submit" class="btn btn-danger btn-sm px-4 w-100" style="padding-top: 0.6rem; padding-bottom: 0.6rem;">
                    <i class="bi bi-filter me-1"></i> Tampilkan Laporan
                </button>
            </div>
        </form>
    </div>
</div>

<!-- =========================================================================
     TABEL DETAIL TRANSAKSI OPERASIONAL
     ========================================================================= -->
@php
    $sumNominal = array_sum(array_column($details, 'nominal'));
@endphp

<div class="card-app mb-4">
    <div class="card-header">
        <span class="d-flex align-items-center gap-2 fw-bold text-dark">
            <i class="bi bi-list-check text-danger"></i> Detail Pengeluaran Biaya Operasional
        </span>
        <span class="badge bg-light text-dark border no-print">{{ count($details) }} baris pengeluaran</span>
    </div>
    <div class="table-responsive">
        <table class="table table-app align-middle mb-0" id="reportOpsDetailTable">
            <thead>
                <tr>
                    <th style="width: 50px;">No</th>
                    <th>Tanggal Trx</th>
                    <th>Kode Trx Ops</th>
                    <th>Cabang</th>
                    <th>Beban Operasional</th>
                    <th class="text-end">Nominal Pengeluaran</th>
                    <th>Keterangan Biaya</th>
                </tr>
            </thead>
            <tbody>
                @forelse($details as $index => $row)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $row['tanggal_trx_ops'] }}</td>
                    <td><span class="badge bg-light text-dark border">{{ $row['kode_trx_ops'] }}</span></td>
                    <td><span class="badge bg-light text-primary border">{{ $row['nama_cabang'] }}</span></td>
                    <td><span class="badge bg-light text-primary border">{{ $row['beban_ops'] }}</span></td>
                    <td class="text-end fw-bold text-danger">Rp {{ number_format($row['nominal'], 2, ',', '.') }}</td>
                    <td><span class="text-truncate d-inline-block" style="max-width: 250px;">{{ $row['ket_trx_ops'] }}</span></td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center py-3 text-muted">Tidak ada transaksi biaya operasional pada rentang filter ini.</td>
                </tr>
                @endforelse
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="5" class="text-end fw-bold text-uppercase">Grand Total Pengeluaran:</td>
                    <td class="text-end text-danger fw-bold">Rp {{ number_format($sumNominal, 2, ',', '.') }}</td>
                    <td></td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>

<!-- =========================================================================
     TABEL REKAP PER BULAN - TAHUN
     ========================================================================= -->
<div class="card-app">
    <div class="card-header">
        <span class="d-flex align-items-center gap-2 fw-bold text-dark">
            <i class="bi bi-calendar2-range text-danger"></i> Rekapitulasi Operasional per Bulan-Tahun
        </span>
    </div>
    <div class="table-responsive">
        <table class="table table-app align-middle mb-0">
            <thead>
                <tr>
                    <th style="width: 50px;">No</th>
                    <th>Bulan - Tahun</th>
                    <th class="text-center">Jumlah Transaksi</th>
                    <th class="text-end">Total Nominal Beban</th>
                </tr>
            </thead>
            <tbody>
                @forelse($rekaps as $idx => $rk)
                <tr>
                    <td>{{ $idx + 1 }}</td>
                    <td class="fw-bold">{{ $rk['bulan_tahun'] }}</td>
                    <td class="text-center"><span class="badge bg-secondary">{{ $rk['jumlah_trx'] }} trx</span></td>
                    <td class="text-end text-danger fw-bold">Rp {{ number_format($rk['total_nominal'], 2, ',', '.') }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="text-center py-3 text-muted">Tidak ada data rekapitulasi pada filter ini.</td>
                </tr>
                @endforelse
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="2" class="text-end fw-bold text-uppercase">Grand Total Operasional:</td>
                    <td class="text-center fw-bold">{{ array_sum(array_column($rekaps, 'jumlah_trx')) }} trx</td>
                    <td class="text-end text-danger fw-bold">Rp {{ number_format(array_sum(array_column($rekaps, 'total_nominal')), 2, ',', '.') }}</td>
                </tr>
            </tfoot>
        </table>
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
                btnPdf.href = `{{ route('laporan.operasional.pdf') }}?tanggal_awal=${encodeURIComponent(inputAwal.value)}&tanggal_akhir=${encodeURIComponent(inputAkhir.value)}&id_cab=${encodeURIComponent(cabVal)}`;
            }
        };
        if (inputAwal) inputAwal.addEventListener('change', updateLinks);
        if (inputAkhir) inputAkhir.addEventListener('change', updateLinks);
        if (selectCab) selectCab.addEventListener('change', updateLinks);
    });
</script>
@endpush
