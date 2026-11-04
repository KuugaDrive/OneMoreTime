@extends('layouts.app')

@section('title', 'Laporan Komisi Bulanan')

@section('content')
<!-- KOP LAPORAN UNTUK CETAK PDF (@media print) -->
<div class="print-only-header">
    <div class="kop-title">AGEN RESMI JNE EXPRESS</div>
    <div class="kop-subtitle">LAPORAN PENCAIRAN TRANSFER KOMISI BULANAN</div>
    <div class="kop-address">Jl. Tomang Raya No. 11, Jakarta Barat | Telp: (021) 566-7890 | Website: jne.co.id</div>
    <div class="print-period-box">
        Periode: <strong id="printPeriodText">{{ $startDate }} s/d {{ $endDate }}</strong> | Cabang: <strong>{{ $namaCabangKop ?? 'Semua Cabang' }}</strong> | Tanggal Cetak: <strong>{{ date('d-m-Y H:i') }}</strong>
    </div>
</div>

<!-- Header Halaman (Layar Normal) -->
<div class="page-header no-print">
    <div>
        <h1 class="page-title">
            <i class="bi bi-wallet-fill text-warning-emphasis"></i> Laporan Komisi Bulanan
        </h1>
        <p class="page-subtitle">Detail histori transfer pembayaran komisi bulanan dari JNE Pusat dan rekapitulasi per periode.</p>
    </div>
    <div class="d-flex align-items-center gap-2">
        <button type="button" class="btn btn-outline-secondary btn-sm px-3 shadow-sm" onclick="window.print()">
            <i class="bi bi-printer me-1"></i> Cetak Cepat
        </button>
        <a href="{{ route('laporan.komisi.pdf', ['tanggal_awal' => $startDate, 'tanggal_akhir' => $endDate, 'id_cab' => $selectedCabang]) }}" id="btnDownloadPdf" class="btn btn-danger btn-sm px-3 shadow-sm">
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
        <form method="GET" action="{{ route('laporan.komisi') }}" id="filterKomisiForm" class="row g-3 align-items-end">
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
     TABEL DETAIL HISTORI KOMISI BULANAN
     ========================================================================= -->
@php
    $sumNominalKomisi = array_sum(array_column($details, 'nominal'));
@endphp

<div class="card-app mb-4">
    <div class="card-header">
        <span class="d-flex align-items-center gap-2 fw-bold text-dark">
            <i class="bi bi-list-check text-primary"></i> Detail Transfer Komisi Bulanan
        </span>
        <span class="badge bg-light text-dark border no-print">{{ count($details) }} transaksi</span>
    </div>
    <div class="table-responsive">
        <table class="table table-app align-middle mb-0" id="reportKomisiDetailTable">
            <thead>
                <tr>
                    <th style="width: 50px;">No</th>
                    <th>Tanggal Transfer</th>
                    <th>Kode Trx CL</th>
                    <th>Cabang</th>
                    <th class="text-end">Nominal Komisi</th>
                    <th>Keterangan Transfer</th>
                </tr>
            </thead>
            <tbody>
                @forelse($details as $index => $row)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $row['tanggal_tf'] }}</td>
                    <td><span class="badge bg-light text-dark border">{{ $row['kode_trx_cl'] }}</span></td>
                    <td><span class="badge bg-light text-primary border">{{ $row['nama_cabang'] }}</span></td>
                    <td class="text-end fw-bold text-success fs-6">Rp {{ number_format($row['nominal'], 2, ',', '.') }}</td>
                    <td><span class="text-truncate d-inline-block" style="max-width: 350px;">{{ $row['ket_trx_cl'] }}</span></td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center py-3 text-muted">Tidak ada transaksi transfer komisi pada rentang filter ini.</td>
                </tr>
                @endforelse
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="4" class="text-end fw-bold text-uppercase">Grand Total Penerimaan Komisi:</td>
                    <td class="text-end text-success fw-bold fs-6">Rp {{ number_format($sumNominalKomisi, 2, ',', '.') }}</td>
                    <td></td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>

<!-- =========================================================================
     TABEL REKAP PER BULAN - TAHUN (REKONSILIASI SISTEM VS TRANSFER)
     ========================================================================= -->
<div class="card-app">
    <div class="card-header">
        <span class="d-flex align-items-center gap-2 fw-bold text-dark">
            <i class="bi bi-calendar2-range text-primary"></i> Rekapitulasi Komisi per Bulan-Tahun
        </span>
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
                    <td class="fw-bold">{{ $rk['bulan_tahun'] }}</td>
                    <td class="text-end fw-semibold">Rp {{ number_format($rk['komisi_sistem'], 2, ',', '.') }}</td>
                    <td class="text-end fw-bold text-dark">Rp {{ number_format($rk['transfer_masuk'], 2, ',', '.') }}</td>
                    <td class="text-end fw-bold {{ $sel > 0.001 ? 'text-success' : ($sel < -0.001 ? 'text-danger' : 'text-muted') }}">
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
                    <td colspan="4" class="text-center py-3 text-muted">Tidak ada data rekapitulasi pada filter ini.</td>
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
                    <td class="text-end fw-bold text-uppercase">Total Rekonsiliasi:</td>
                    <td class="text-end fw-bold">Rp {{ number_format($totKomisi, 2, ',', '.') }}</td>
                    <td class="text-end fw-bold">Rp {{ number_format($totTransfer, 2, ',', '.') }}</td>
                    <td class="text-end fw-bold {{ $totSelisih > 0.001 ? 'text-success' : ($totSelisih < -0.001 ? 'text-danger' : 'text-muted') }}">
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
    <div class="card-footer bg-light py-2 text-muted small border-top">
        <div><strong>Catatan:</strong> Selisih = Transfer masuk - Komisi menurut sistem (Disc Charge Packing List). Selisih tidak ikut dihitung dalam Laba Bersih. Mohon cek ke JNE bila selisih tidak wajar.</div>
        @if(($lag ?? 0) > 0)
        <div class="mt-1 text-primary"><i class="bi bi-info-circle me-1"></i>Komisi menurut sistem dihitung dari Packing List {{ $lag }} bulan sebelumnya.</div>
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
