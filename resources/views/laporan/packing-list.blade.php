@extends('layouts.app')

@section('title', 'Laporan Packing List')

@section('content')
<!-- KOP LAPORAN UNTUK CETAK PDF (@media print) -->
<div class="print-only-header">
    <div class="kop-title">AGEN RESMI JNE EXPRESS</div>
    <div class="kop-subtitle">LAPORAN TRANSAKSI PACKING LIST (KOMISI HARIAN)</div>
    <div class="kop-address">Jl. Tomang Raya No. 11, Jakarta Barat | Telp: (021) 566-7890 | Website: jne.co.id</div>
    <div class="print-period-box">
        Periode: <strong id="printPeriodText">{{ $startDate }} s/d {{ $endDate }}</strong> | Cabang: <strong>{{ $namaCabangKop ?? 'Semua Cabang' }}</strong> | Tanggal Cetak: <strong>{{ date('d-m-Y H:i') }}</strong>
    </div>
</div>

<!-- Header Halaman (Layar Normal) -->
<div class="page-header no-print">
    <div>
        <h1 class="page-title">
            <i class="bi bi-file-earmark-bar-graph-fill text-primary"></i> Laporan Packing List
        </h1>
        <p class="page-subtitle">Laporan detail transaksi packing list harian dan rekap omset, diskon, grand total per bulan-tahun.</p>
    </div>
    <div class="d-flex align-items-center gap-2">
        <button type="button" class="btn btn-outline-secondary btn-sm px-3 shadow-sm" onclick="window.print()">
            <i class="bi bi-printer me-1"></i> Cetak Cepat
        </button>
        <a href="{{ route('laporan.packing-list.pdf', ['tanggal_awal' => $startDate, 'tanggal_akhir' => $endDate, 'id_cab' => $selectedCabang]) }}" id="btnDownloadPdf" class="btn btn-danger btn-sm px-3 shadow-sm">
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
        <form method="GET" action="{{ route('laporan.packing-list') }}" id="filterPLForm" class="row g-3 align-items-end">
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
                <button type="submit" class="btn btn-primary btn-sm px-4 w-100" style="padding-top: 0.6rem; padding-bottom: 0.6rem;">
                    <i class="bi bi-filter me-1"></i> Tampilkan Laporan
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
    <div class="card-header">
        <span class="d-flex align-items-center gap-2 fw-bold text-dark">
            <i class="bi bi-list-ul text-primary"></i> Detail Transaksi Packing List
        </span>
        <span class="badge bg-light text-dark border no-print">{{ count($details) }} baris data</span>
    </div>
    <div class="table-responsive">
        <table class="table table-app align-middle mb-0" id="reportPLDetailTable">
            <thead>
                <tr>
                    <th style="width: 50px;">No</th>
                    <th>Tanggal PL</th>
                    <th>Kode Trx PL</th>
                    <th>No. PL JNE</th>
                    <th>Cabang</th>
                    <th class="text-end">Total Bruto</th>
                    <th class="text-end">Discount Charge</th>
                    <th class="text-end">e-Voucher</th>
                    <th class="text-end">Grand Total</th>
                    <th class="text-center">Jml Paket</th>
                    <th>Keterangan</th>
                </tr>
            </thead>
            <tbody>
                @forelse($details as $index => $row)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $row['tanggal_pl'] }}</td>
                    <td><span class="badge bg-light text-dark border">{{ $row['kode_trx_pl'] }}</span></td>
                    <td>
                        @if(!empty($row['no_pl_jne']))
                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle font-monospace">{{ $row['no_pl_jne'] }}</span>
                        @else
                            <span class="text-muted">-</span>
                        @endif
                    </td>
                    <td><span class="badge bg-light text-primary border">{{ $row['nama_cabang'] }}</span></td>
                    <td class="text-end">Rp {{ number_format($row['total'], 2, ',', '.') }}</td>
                    <td class="text-end text-danger">- Rp {{ number_format($row['discount_charge'], 2, ',', '.') }}</td>
                    <td class="text-end text-secondary">Rp {{ number_format($row['e_voucher'] ?? 0, 2, ',', '.') }}</td>
                    <td class="text-end fw-bold text-primary">Rp {{ number_format($row['grand_total'], 2, ',', '.') }}</td>
                    <td class="text-center">{{ $row['jumlah_paket'] }}</td>
                    <td><span class="text-truncate d-inline-block" style="max-width: 200px;">{{ $row['ket_trx_pl'] }}</span></td>
                </tr>
                @empty
                <tr>
                    <td colspan="11" class="text-center py-3 text-muted">Tidak ada transaksi packing list pada rentang filter ini.</td>
                </tr>
                @endforelse
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="5" class="text-end fw-bold text-uppercase">Grand Total Detail:</td>
                    <td class="text-end fw-bold">Rp {{ number_format($sumTotal, 2, ',', '.') }}</td>
                    <td class="text-end text-danger fw-bold">- Rp {{ number_format($sumDiscount, 2, ',', '.') }}</td>
                    <td class="text-end text-secondary fw-bold">Rp {{ number_format($sumEVoucher, 2, ',', '.') }}</td>
                    <td class="text-end text-primary fw-bold">Rp {{ number_format($sumGrandTotal, 2, ',', '.') }}</td>
                    <td class="text-center fw-bold">{{ $sumPaket }} paket</td>
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
            <i class="bi bi-calendar2-range text-primary"></i> Rekap per Bulan-Tahun
        </span>
    </div>
    <div class="table-responsive">
        <table class="table table-app align-middle mb-0">
            <thead>
                <tr>
                    <th style="width: 50px;">No</th>
                    <th>Bulan - Tahun</th>
                    <th class="text-center">Jumlah Transaksi</th>
                    <th class="text-end">Total Bruto</th>
                    <th class="text-end">Total Discount Charge</th>
                    <th class="text-end">Total e-Voucher</th>
                    <th class="text-end">Total Grand Total</th>
                    <th class="text-center">Total Paket</th>
                </tr>
            </thead>
            <tbody>
                @forelse($rekaps as $idx => $rk)
                <tr>
                    <td>{{ $idx + 1 }}</td>
                    <td class="fw-bold">{{ $rk['bulan_tahun'] }}</td>
                    <td class="text-center"><span class="badge bg-secondary">{{ $rk['jumlah_trx'] }} trx</span></td>
                    <td class="text-end">Rp {{ number_format($rk['total_bruto'] ?? 0, 2, ',', '.') }}</td>
                    <td class="text-end text-danger">- Rp {{ number_format($rk['total_discount_charge'], 2, ',', '.') }}</td>
                    <td class="text-end text-secondary">Rp {{ number_format($rk['total_e_voucher'] ?? 0, 2, ',', '.') }}</td>
                    <td class="text-end fw-bold text-primary">Rp {{ number_format($rk['total_grand_total'], 2, ',', '.') }}</td>
                    <td class="text-center fw-semibold">{{ $rk['total_paket'] }} paket</td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="text-center py-3 text-muted">Tidak ada data rekapitulasi pada filter ini.</td>
                </tr>
                @endforelse
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="2" class="text-end fw-bold text-uppercase">Grand Total Rekapitulasi:</td>
                    <td class="text-center fw-bold">{{ array_sum(array_column($rekaps, 'jumlah_trx')) }} trx</td>
                    <td class="text-end fw-bold">Rp {{ number_format(array_sum(array_column($rekaps, 'total_bruto')), 2, ',', '.') }}</td>
                    <td class="text-end text-danger fw-bold">- Rp {{ number_format(array_sum(array_column($rekaps, 'total_discount_charge')), 2, ',', '.') }}</td>
                    <td class="text-end text-secondary fw-bold">Rp {{ number_format(array_sum(array_column($rekaps, 'total_e_voucher')), 2, ',', '.') }}</td>
                    <td class="text-end text-primary fw-bold">Rp {{ number_format(array_sum(array_column($rekaps, 'total_grand_total')), 2, ',', '.') }}</td>
                    <td class="text-center fw-bold">{{ array_sum(array_column($rekaps, 'total_paket')) }} paket</td>
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
