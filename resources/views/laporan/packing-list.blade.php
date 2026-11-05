@extends('layouts.app')

@section('title', 'Laporan Packing List')

@section('content')
<div class="page-header d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4 no-print">
    <div>
        <h1 class="page-title">Laporan Packing List</h1>
        <p class="page-subtitle">Laporan detail kiriman harian, omset bruto, discount charge, dan setoran per periode.</p>
    </div>
    <div class="d-flex align-items-center gap-2">
        <button type="button" class="btn btn-outline-secondary btn-sm px-3" onclick="window.print()">
            <i class="bi bi-printer me-1"></i> Cetak
        </button>
        <a href="{{ route('laporan.packing-list.pdf', ['tanggal_awal' => $startDate, 'tanggal_akhir' => $endDate, 'id_cab' => $selectedCabang]) }}" id="btnDownloadPdf" class="btn btn-primary btn-sm px-3">
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
        <form method="GET" action="{{ route('laporan.packing-list') }}" id="filterPLForm" class="row g-2 align-items-end">
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
     TABEL DETAIL TRANSAKSI PACKING LIST
     ========================================================================= -->
@php
    $sumTotal = array_sum(array_column($details, 'total'));
    $sumDiscount = array_sum(array_column($details, 'discount_charge'));
    $sumEVoucher = array_sum(array_column($details, 'e_voucher'));
    $sumGrandTotal = array_sum(array_column($details, 'grand_total'));
    $sumPaket = array_sum(array_column($details, 'jumlah_paket'));
@endphp

<div class="card-app mb-4">
    <div class="card-header d-flex justify-content-between align-items-center py-2 px-3">
        <span class="fw-semibold text-dark" style="font-size: 13px;">1. Rincian Transaksi Packing List</span>
        <span class="text-secondary small">{{ count($details) }} baris data</span>
    </div>
    <div class="table-responsive">
        <table class="table table-app align-middle mb-0" id="reportPLDetailTable">
            <thead>
                <tr>
                    <th style="width: 45px;">No</th>
                    <th>Tanggal</th>
                    <th>Kode Trx</th>
                    <th>No. PL JNE</th>
                    <th>Cabang</th>
                    <th class="text-end">Total Bruto</th>
                    <th class="text-end">Disc Charge</th>
                    <th class="text-end">e-Voucher</th>
                    <th class="text-end">Grand Total</th>
                    <th class="text-center">Paket</th>
                    <th>Keterangan</th>
                </tr>
            </thead>
            <tbody>
                @forelse($details as $index => $row)
                <tr>
                    <td class="row-number text-secondary">{{ $index + 1 }}</td>
                    <td class="tabular-nums">{{ $row['tanggal_pl'] }}</td>
                    <td><span class="font-monospace text-dark fw-medium">{{ $row['kode_trx_pl'] }}</span></td>
                    <td>
                        @if(!empty($row['no_pl_jne']))
                            <span class="font-monospace text-secondary small">{{ $row['no_pl_jne'] }}</span>
                        @else
                            <span class="text-secondary">-</span>
                        @endif
                    </td>
                    <td><span class="text-dark">{{ $row['nama_cabang'] }}</span></td>
                    <td class="text-end tabular-nums">Rp {{ number_format($row['total'], 2, ',', '.') }}</td>
                    <td class="text-end tabular-nums text-secondary">Rp {{ number_format($row['discount_charge'], 2, ',', '.') }}</td>
                    <td class="text-end tabular-nums text-secondary">Rp {{ number_format($row['e_voucher'] ?? 0, 2, ',', '.') }}</td>
                    <td class="text-end tabular-nums fw-medium text-dark">Rp {{ number_format($row['grand_total'], 2, ',', '.') }}</td>
                    <td class="text-center tabular-nums">{{ $row['jumlah_paket'] }}</td>
                    <td><span class="text-truncate d-inline-block text-secondary" style="max-width: 200px;">{{ $row['ket_trx_pl'] }}</span></td>
                </tr>
                @empty
                <tr>
                    <td colspan="11" class="text-center py-3 text-secondary">Tidak ada transaksi packing list pada rentang filter ini.</td>
                </tr>
                @endforelse
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="5" class="text-end fw-semibold">TOTAL RINCIAN:</td>
                    <td class="text-end tabular-nums fw-semibold">Rp {{ number_format($sumTotal, 2, ',', '.') }}</td>
                    <td class="text-end tabular-nums fw-semibold text-secondary">Rp {{ number_format($sumDiscount, 2, ',', '.') }}</td>
                    <td class="text-end tabular-nums fw-semibold text-secondary">Rp {{ number_format($sumEVoucher, 2, ',', '.') }}</td>
                    <td class="text-end tabular-nums fw-bold text-dark">Rp {{ number_format($sumGrandTotal, 2, ',', '.') }}</td>
                    <td class="text-center tabular-nums fw-semibold">{{ $sumPaket }}</td>
                    <td></td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>

<!-- =========================================================================
     TABEL REKAP PER BULAN - TAHUN
     ========================================================================= -->
<div class="card-app mb-4">
    <div class="card-header py-2 px-3">
        <span class="fw-semibold text-dark" style="font-size: 13px;">2. Rekapitulasi per Bulan-Tahun</span>
    </div>
    <div class="table-responsive">
        <table class="table table-app align-middle mb-0">
            <thead>
                <tr>
                    <th style="width: 45px;">No</th>
                    <th>Bulan - Tahun</th>
                    <th class="text-center">Jumlah Trx</th>
                    <th class="text-end">Total Bruto</th>
                    <th class="text-end">Disc Charge</th>
                    <th class="text-end">e-Voucher</th>
                    <th class="text-end">Grand Total</th>
                    <th class="text-center">Total Paket</th>
                </tr>
            </thead>
            <tbody>
                @forelse($rekaps as $idx => $rk)
                <tr>
                    <td class="row-number text-secondary">{{ $idx + 1 }}</td>
                    <td class="fw-semibold text-dark">{{ $rk['bulan_tahun'] }}</td>
                    <td class="text-center tabular-nums">{{ $rk['jumlah_trx'] }} trx</td>
                    <td class="text-end tabular-nums">Rp {{ number_format($rk['total_bruto'] ?? 0, 2, ',', '.') }}</td>
                    <td class="text-end tabular-nums text-secondary">Rp {{ number_format($rk['total_discount_charge'], 2, ',', '.') }}</td>
                    <td class="text-end tabular-nums text-secondary">Rp {{ number_format($rk['total_e_voucher'] ?? 0, 2, ',', '.') }}</td>
                    <td class="text-end tabular-nums fw-medium text-dark">Rp {{ number_format($rk['total_grand_total'], 2, ',', '.') }}</td>
                    <td class="text-center tabular-nums">{{ $rk['total_paket'] }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="text-center py-3 text-secondary">Tidak ada data rekapitulasi pada filter ini.</td>
                </tr>
                @endforelse
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="2" class="text-end fw-semibold">TOTAL REKAPITULASI:</td>
                    <td class="text-center tabular-nums fw-semibold">{{ array_sum(array_column($rekaps, 'jumlah_trx')) }} trx</td>
                    <td class="text-end tabular-nums fw-semibold">Rp {{ number_format(array_sum(array_column($rekaps, 'total_bruto')), 2, ',', '.') }}</td>
                    <td class="text-end tabular-nums fw-semibold text-secondary">Rp {{ number_format(array_sum(array_column($rekaps, 'total_discount_charge')), 2, ',', '.') }}</td>
                    <td class="text-end tabular-nums fw-semibold text-secondary">Rp {{ number_format(array_sum(array_column($rekaps, 'total_e_voucher')), 2, ',', '.') }}</td>
                    <td class="text-end tabular-nums fw-bold text-dark">Rp {{ number_format(array_sum(array_column($rekaps, 'total_grand_total')), 2, ',', '.') }}</td>
                    <td class="text-center tabular-nums fw-semibold">{{ array_sum(array_column($rekaps, 'total_paket')) }}</td>
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
                btnPdf.href = `{{ route('laporan.packing-list.pdf') }}?tanggal_awal=${encodeURIComponent(inputAwal.value)}&tanggal_akhir=${encodeURIComponent(inputAkhir.value)}&id_cab=${encodeURIComponent(cabVal)}`;
            }
        };
        if (inputAwal) inputAwal.addEventListener('change', updateLinks);
        if (inputAkhir) inputAkhir.addEventListener('change', updateLinks);
        if (selectCab) selectCab.addEventListener('change', updateLinks);
    });
</script>
@endpush
