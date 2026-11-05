@extends('layouts.app')

@section('title', 'Laporan Biaya Operasional')

@section('content')
<div class="page-header d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4 no-print">
    <div>
        <h1 class="page-title">Laporan Biaya Operasional</h1>
        <p class="page-subtitle">Rincian pengeluaran operasional cabang dan rekapitulasi total beban per periode.</p>
    </div>
    <div class="d-flex align-items-center gap-2">
        <button type="button" class="btn btn-outline-secondary btn-sm px-3" onclick="window.print()">
            <i class="bi bi-printer me-1"></i> Cetak
        </button>
        <a href="{{ route('laporan.operasional.pdf', ['tanggal_awal' => $startDate, 'tanggal_akhir' => $endDate, 'id_cab' => $selectedCabang]) }}" id="btnDownloadPdf" class="btn btn-primary btn-sm px-3">
            <i class="bi bi-download me-1"></i> Unduh PDF
        </a>
    </div>
</div>

<!-- =========================================================================
     FILTER RANGE TANGGAL & CABANG
     ========================================================================= -->
<div class="card-app mb-4 no-print">
    <div class="card-header py-2 px-3">
        <span class="fw-semibold text-dark" style="font-size: 13px;">Filter Periode &amp; Cabang</span>
    </div>
    <div class="card-body p-3">
        <form method="GET" action="{{ route('laporan.operasional') }}" id="filterOpsForm" class="row g-2 align-items-end">
            <div class="col-12 col-md-3">
                <label for="filterCabang" class="form-label small fw-semibold text-dark">Cabang</label>
                <select class="form-select form-select-sm" name="id_cab" id="filterCabang">
                    <option value="all" {{ ($selectedCabang ?? 'all') === 'all' ? 'selected' : '' }}>Semua Cabang</option>
                    @foreach($cabangs as $cab)
                    <option value="{{ $cab->id_cab }}" {{ ($selectedCabang ?? '') === $cab->id_cab ? 'selected' : '' }}>
                        {{ $cab->nama_cabang }} ({{ $cab->id_cab }})
                    </option>
                    @endforeach
                </select>
            </div>
            <div class="col-12 col-md-3">
                <label for="filterTanggalAwal" class="form-label small fw-semibold text-dark">Tanggal Awal <span class="text-danger">*</span></label>
                <input type="text" class="form-control form-control-sm datepicker" name="tanggal_awal" id="filterTanggalAwal" value="{{ $startDate }}" placeholder="dd-mm-yyyy" required>
            </div>
            <div class="col-12 col-md-3">
                <label for="filterTanggalAkhir" class="form-label small fw-semibold text-dark">Tanggal Akhir <span class="text-danger">*</span></label>
                <input type="text" class="form-control form-control-sm datepicker" name="tanggal_akhir" id="filterTanggalAkhir" value="{{ $endDate }}" placeholder="dd-mm-yyyy" required>
            </div>
            <div class="col-12 col-md-3">
                <button type="submit" class="btn btn-primary btn-sm w-100">
                    Tampilkan Laporan
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
    <div class="card-header d-flex justify-content-between align-items-center py-2 px-3">
        <span class="fw-semibold text-dark" style="font-size: 13px;">1. Rincian Pengeluaran Biaya Operasional</span>
        <span class="text-secondary small">{{ count($details) }} baris data</span>
    </div>
    <div class="table-responsive">
        <table class="table table-app align-middle mb-0" id="reportOpsDetailTable">
            <thead>
                <tr>
                    <th style="width: 45px;">No</th>
                    <th>Tanggal</th>
                    <th>Kode Trx</th>
                    <th>Cabang</th>
                    <th>Beban Operasional</th>
                    <th class="text-end">Nominal Pengeluaran</th>
                    <th>Keterangan</th>
                </tr>
            </thead>
            <tbody>
                @forelse($details as $index => $row)
                <tr>
                    <td class="row-number text-secondary">{{ $index + 1 }}</td>
                    <td class="tabular-nums">{{ $row['tanggal_trx_ops'] }}</td>
                    <td><span class="font-monospace text-dark fw-medium">{{ $row['kode_trx_ops'] }}</span></td>
                    <td><span class="text-dark">{{ $row['nama_cabang'] }}</span></td>
                    <td><span class="text-dark">{{ $row['beban_ops'] }}</span></td>
                    <td class="text-end tabular-nums fw-medium text-dark">Rp {{ number_format($row['nominal'], 2, ',', '.') }}</td>
                    <td><span class="text-truncate d-inline-block text-secondary" style="max-width: 260px;">{{ $row['ket_trx_ops'] }}</span></td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center py-3 text-secondary">Tidak ada pengeluaran operasional pada rentang filter ini.</td>
                </tr>
                @endforelse
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="5" class="text-end fw-semibold">TOTAL RINCIAN:</td>
                    <td class="text-end tabular-nums fw-bold text-dark">Rp {{ number_format($sumNominal, 2, ',', '.') }}</td>
                    <td></td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>

<!-- =========================================================================
     TABEL REKAP OPERASIONAL PER BULAN - TAHUN
     ========================================================================= -->
<div class="card-app mb-4">
    <div class="card-header py-2 px-3">
        <span class="fw-semibold text-dark" style="font-size: 13px;">2. Rekapitulasi Beban per Bulan-Tahun</span>
    </div>
    <div class="table-responsive">
        <table class="table table-app align-middle mb-0">
            <thead>
                <tr>
                    <th style="width: 45px;">No</th>
                    <th>Bulan - Tahun</th>
                    <th class="text-center">Jumlah Transaksi</th>
                    <th class="text-end">Total Nominal Beban</th>
                </tr>
            </thead>
            <tbody>
                @forelse($rekaps as $idx => $rk)
                <tr>
                    <td class="row-number text-secondary">{{ $idx + 1 }}</td>
                    <td class="fw-semibold text-dark">{{ $rk['bulan_tahun'] }}</td>
                    <td class="text-center tabular-nums">{{ $rk['jumlah_trx'] }} trx</td>
                    <td class="text-end tabular-nums fw-medium text-dark">Rp {{ number_format($rk['total_nominal'], 2, ',', '.') }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="text-center py-3 text-secondary">Tidak ada data rekapitulasi pada filter ini.</td>
                </tr>
                @endforelse
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="2" class="text-end fw-semibold">TOTAL REKAPITULASI:</td>
                    <td class="text-center tabular-nums fw-semibold">{{ array_sum(array_column($rekaps, 'jumlah_trx')) }} trx</td>
                    <td class="text-end tabular-nums fw-bold text-dark">Rp {{ number_format(array_sum(array_column($rekaps, 'total_nominal')), 2, ',', '.') }}</td>
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
