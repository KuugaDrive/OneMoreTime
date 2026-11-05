@extends('layouts.app')

@section('title', 'Laporan Komisi Bulanan')

@section('content')
<div class="page-header d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4 no-print">
    <div>
        <h1 class="page-title">Laporan Komisi Bulanan</h1>
        <p class="page-subtitle">Detail histori transfer pembayaran komisi bulanan dari kantor pusat dan rekonsiliasi sistem per periode.</p>
    </div>
    <div class="d-flex align-items-center gap-2">
        <button type="button" class="btn btn-outline-secondary btn-sm px-3" onclick="window.print()">
            <i class="bi bi-printer me-1"></i> Cetak
        </button>
        <a href="{{ route('laporan.komisi.pdf', ['tanggal_awal' => $startDate, 'tanggal_akhir' => $endDate, 'id_cab' => $selectedCabang]) }}" id="btnDownloadPdf" class="btn btn-primary btn-sm px-3">
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
        <form method="GET" action="{{ route('laporan.komisi') }}" id="filterKomisiForm" class="row g-2 align-items-end">
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
     TABEL DETAIL HISTORI KOMISI BULANAN
     ========================================================================= -->
@php
    $sumNominalKomisi = array_sum(array_column($details, 'nominal'));
@endphp

<div class="card-app mb-4">
    <div class="card-header d-flex justify-content-between align-items-center py-2 px-3">
        <span class="fw-semibold text-dark" style="font-size: 13px;">1. Rincian Transfer Komisi Bulanan</span>
        <span class="text-secondary small">{{ count($details) }} transaksi</span>
    </div>
    <div class="table-responsive">
        <table class="table table-app align-middle mb-0" id="reportKomisiDetailTable">
            <thead>
                <tr>
                    <th style="width: 45px;">No</th>
                    <th>Tanggal Transfer</th>
                    <th>Kode Trx CL</th>
                    <th>Cabang</th>
                    <th class="text-end">Nominal Komisi</th>
                    <th>Keterangan</th>
                </tr>
            </thead>
            <tbody>
                @forelse($details as $index => $row)
                <tr>
                    <td class="row-number text-secondary">{{ $index + 1 }}</td>
                    <td class="tabular-nums">{{ $row['tanggal_tf'] }}</td>
                    <td><span class="font-monospace text-dark fw-medium">{{ $row['kode_trx_cl'] }}</span></td>
                    <td><span class="text-dark">{{ $row['nama_cabang'] }}</span></td>
                    <td class="text-end tabular-nums fw-medium text-dark">Rp {{ number_format($row['nominal'], 2, ',', '.') }}</td>
                    <td><span class="text-truncate d-inline-block text-secondary" style="max-width: 320px;">{{ $row['ket_trx_cl'] }}</span></td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center py-3 text-secondary">Tidak ada transaksi transfer komisi pada rentang filter ini.</td>
                </tr>
                @endforelse
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="4" class="text-end fw-semibold">TOTAL RINCIAN:</td>
                    <td class="text-end tabular-nums fw-bold text-dark">Rp {{ number_format($sumNominalKomisi, 2, ',', '.') }}</td>
                    <td></td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>

<!-- =========================================================================
     TABEL REKAP PER BULAN - TAHUN (REKONSILIASI SISTEM VS TRANSFER)
     ========================================================================= -->
<div class="card-app mb-4">
    <div class="card-header py-2 px-3">
        <span class="fw-semibold text-dark" style="font-size: 13px;">2. Rekapitulasi &amp; Rekonsiliasi Komisi per Bulan-Tahun</span>
    </div>
    <div class="table-responsive">
        <table class="table table-app align-middle mb-0">
            <thead>
                <tr>
                    <th>Bulan-Tahun</th>
                    <th class="text-end">Komisi menurut sistem (Rp)</th>
                    <th class="text-end">Transfer masuk (Rp)</th>
                    <th class="text-end">Selisih (Rp)</th>
                </tr>
            </thead>
            <tbody>
                @forelse($rekaps as $rk)
                @php
                    $sel = (float) $rk['selisih'];
                @endphp
                <tr>
                    <td class="fw-semibold text-dark">{{ $rk['bulan_tahun'] }}</td>
                    <td class="text-end tabular-nums">Rp {{ number_format($rk['komisi_sistem'], 2, ',', '.') }}</td>
                    <td class="text-end tabular-nums fw-medium text-dark">Rp {{ number_format($rk['transfer_masuk'], 2, ',', '.') }}</td>
                    <td class="text-end tabular-nums fw-semibold {{ $sel > 0.001 ? 'text-success' : ($sel < -0.001 ? 'text-danger' : 'text-secondary') }}">
                        @if($sel > 0.001)
                            + Rp {{ number_format($sel, 2, ',', '.') }}
                        @elseif($sel < -0.001)
                            - Rp {{ number_format(abs($sel), 2, ',', '.') }}
                        @else
                            Rp 0,00
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="text-center py-3 text-secondary">Tidak ada data rekapitulasi pada filter ini.</td>
                </tr>
                @endforelse
            </tbody>
            <tfoot>
                @php
                    $totKomisi = array_sum(array_column($rekaps, 'komisi_sistem'));
                    $totTransfer = array_sum(array_column($rekaps, 'transfer_masuk'));
                    $totSelisih = $totTransfer - $totKomisi;
                @endphp
                <tr>
                    <td class="text-end fw-semibold">TOTAL REKONSILIASI:</td>
                    <td class="text-end tabular-nums fw-semibold">Rp {{ number_format($totKomisi, 2, ',', '.') }}</td>
                    <td class="text-end tabular-nums fw-semibold text-dark">Rp {{ number_format($totTransfer, 2, ',', '.') }}</td>
                    <td class="text-end tabular-nums fw-bold {{ $totSelisih > 0.001 ? 'text-success' : ($totSelisih < -0.001 ? 'text-danger' : 'text-secondary') }}">
                        @if($totSelisih > 0.001)
                            + Rp {{ number_format($totSelisih, 2, ',', '.') }}
                        @elseif($totSelisih < -0.001)
                            - Rp {{ number_format(abs($totSelisih), 2, ',', '.') }}
                        @else
                            Rp 0,00
                        @endif
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>
    <div class="card-footer bg-white border-top py-2 px-3 text-secondary small" style="font-size: 12px;">
        <div><strong>Catatan:</strong> Selisih = Transfer masuk - Komisi menurut sistem (Disc Charge Packing List). Selisih tidak ikut dihitung dalam Laba Bersih.</div>
        @if(($lag ?? 0) > 0)
        <div class="mt-1 text-primary">Komisi menurut sistem dihitung dari Packing List {{ $lag }} bulan sebelumnya.</div>
        @endif
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
                btnPdf.href = `{{ route('laporan.komisi.pdf') }}?tanggal_awal=${encodeURIComponent(inputAwal.value)}&tanggal_akhir=${encodeURIComponent(inputAkhir.value)}&id_cab=${encodeURIComponent(cabVal)}`;
            }
        };
        if (inputAwal) inputAwal.addEventListener('change', updateLinks);
        if (inputAkhir) inputAkhir.addEventListener('change', updateLinks);
        if (selectCab) selectCab.addEventListener('change', updateLinks);
    });
</script>
@endpush
