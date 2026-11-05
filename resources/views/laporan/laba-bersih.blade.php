@extends('layouts.app')

@section('title', 'Laporan Laba Bersih')

@section('content')
<div class="page-header d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4 no-print">
    <div>
        <h1 class="page-title">Laporan Laba Bersih</h1>
        <p class="page-subtitle">Perhitungan laba bersih agen per bulan-tahun ({{ $cfg['rumus_keterangan'] ?? 'Laba Bersih = Komisi PL - Biaya Operasional' }}).</p>
    </div>
    <div class="d-flex align-items-center gap-2">
        <button type="button" class="btn btn-outline-secondary btn-sm px-3" onclick="window.print()">
            <i class="bi bi-printer me-1"></i> Cetak
        </button>
        <a href="{{ route('laporan.laba-bersih.pdf', ['tanggal_awal' => $startDate, 'tanggal_akhir' => $endDate, 'id_cab' => $selectedCabang]) }}" id="btnDownloadPdf" class="btn btn-primary btn-sm px-3">
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
        <form method="GET" action="{{ route('laporan.laba-bersih') }}" id="filterLabaForm" class="row g-2 align-items-end">
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
     TABEL REKAP LABA BERSIH PER BULAN-TAHUN
     ========================================================================= -->
@php
    $sumPL = array_sum(array_column($rekaps, 'total_pl'));
    $sumOps = array_sum(array_column($rekaps, 'total_ops'));
    $sumLaba = array_sum(array_column($rekaps, 'laba_bersih'));
@endphp

<div class="card-app mb-4">
    <div class="card-header d-flex justify-content-between align-items-center py-2 px-3">
        <span class="fw-semibold text-dark" style="font-size: 13px;">Rekapitulasi Laba Bersih per Bulan-Tahun</span>
        <span class="text-secondary small">{{ count($rekaps) }} periode</span>
    </div>
    <div class="table-responsive">
        <table class="table table-app align-middle mb-0" id="reportLabaBersihTable">
            <thead>
                <tr>
                    <th style="width: 45px;">No</th>
                    <th>Bulan - Tahun</th>
                    <th class="text-end">{{ $cfg['pl_column_label'] ?? 'Pendapatan Komisi (Disc Charge)' }}</th>
                    <th class="text-end">Total Biaya Operasional</th>
                    <th class="text-end">Laba Bersih</th>
                    <th class="text-center" style="width: 100px;">Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($rekaps as $index => $row)
                <tr>
                    <td class="row-number text-secondary">{{ $index + 1 }}</td>
                    <td class="fw-semibold text-dark">{{ $row['bulan_tahun'] }}</td>
                    <td class="text-end tabular-nums">Rp {{ number_format($row['total_pl'], 2, ',', '.') }}</td>
                    <td class="text-end tabular-nums text-secondary">Rp {{ number_format($row['total_ops'], 2, ',', '.') }}</td>
                    <td class="text-end tabular-nums fw-semibold {{ $row['laba_bersih'] >= 0 ? 'text-success' : 'text-danger' }}">
                        Rp {{ number_format($row['laba_bersih'], 2, ',', '.') }}
                    </td>
                    <td class="text-center">
                        @if($row['laba_bersih'] >= 0)
                            <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1">Surplus</span>
                        @else
                            <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-1">Defisit</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center py-3 text-secondary">Tidak ada transaksi PL maupun Operasional pada rentang filter ini.</td>
                </tr>
                @endforelse
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="2" class="text-end fw-semibold">TOTAL KESELURUHAN:</td>
                    <td class="text-end tabular-nums fw-semibold">Rp {{ number_format($sumPL, 2, ',', '.') }}</td>
                    <td class="text-end tabular-nums fw-semibold text-secondary">Rp {{ number_format($sumOps, 2, ',', '.') }}</td>
                    <td class="text-end tabular-nums fw-bold {{ $sumLaba >= 0 ? 'text-success' : 'text-danger' }}">
                        Rp {{ number_format($sumLaba, 2, ',', '.') }}
                    </td>
                    <td class="text-center fw-semibold {{ $sumLaba >= 0 ? 'text-success' : 'text-danger' }}">
                        {{ $sumLaba >= 0 ? 'Surplus' : 'Defisit' }}
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>

<!-- =========================================================================
     INFORMASI RUMUS PERHITUNGAN
     ========================================================================= -->
<div class="card-app mb-4 no-print">
    <div class="card-header py-2 px-3">
        <span class="fw-semibold text-dark" style="font-size: 13px;">Catatan Rumus Finansial</span>
    </div>
    <div class="card-body p-3">
        <div class="text-secondary small mb-2">
            <strong>Formula:</strong> {{ $cfg['rumus_keterangan'] ?? 'Laba Bersih = Komisi PL (Disc Charge) - Biaya Operasional' }}
        </div>
        <ul class="mb-0 small text-secondary ps-3" style="font-size: 12px; line-height: 1.6;">
            <li><strong>{{ $cfg['pl_column_label'] ?? 'Pendapatan Komisi (Disc Charge)' }}:</strong> 
                @if(($cfg['basis'] ?? '') === 'grand_total')
                    Akumulasi nilai Grand Total PL dari transaksi harian.
                @else
                    Akumulasi komisi agen (Discount Charge pada dokumen Packing List) dari transaksi harian.
                @endif
            </li>
            <li><strong>Total Biaya Operasional:</strong> Akumulasi seluruh pengeluaran beban operasional cabang pada periode terkait.</li>
            @if(!empty($cfg['include_komisi']))
            <li><strong>Komisi Bulanan:</strong> Akumulasi penerimaan komisi bulanan dari kantor pusat ditambahkan ke Laba Bersih sesuai konfigurasi.</li>
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
