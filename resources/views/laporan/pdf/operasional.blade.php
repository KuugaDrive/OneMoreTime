@extends('layouts.pdf')

@section('title', 'Laporan Biaya Operasional - ' . $startDate . ' s/d ' . $endDate)
@section('report-title', 'LAPORAN PENGELUARAN BIAYA OPERASIONAL CABANG')

@section('content')
@php
    $sumNominal = array_sum(array_column($details, 'nominal'));
@endphp

<div class="section-header">1. Rincian Transaksi Biaya Operasional</div>
<table class="data-table">
    <thead>
        <tr>
            <th style="width: 25px;" class="text-center">No</th>
            <th style="width: 95px;">Kode Trx Ops</th>
            <th style="width: 85px;">Cabang</th>
            <th style="width: 65px;">Tanggal</th>
            <th style="width: 130px;">Beban Operasional</th>
            <th class="text-end" style="width: 95px;">Nominal Biaya</th>
            <th>Keterangan Pengeluaran</th>
        </tr>
    </thead>
    <tbody>
        @forelse($details as $idx => $row)
        <tr>
            <td class="text-center">{{ $idx + 1 }}</td>
            <td><span class="badge-code">{{ $row['kode_trx_ops'] }}</span></td>
            <td>{{ $row['nama_cabang'] }}</td>
            <td>{{ $row['tanggal_trx_ops'] }}</td>
            <td><strong class="text-danger">{{ $row['beban_ops'] }}</strong></td>
            <td class="text-end fw-bold text-danger">Rp {{ number_format($row['nominal'], 2, ',', '.') }}</td>
            <td>{{ $row['ket_trx_ops'] ?? '-' }}</td>
        </tr>
        @empty
        <tr>
            <td colspan="7" class="text-center" style="color: #6b7280; padding: 12px;">Tidak ada pengeluaran operasional pada rentang filter ini.</td>
        </tr>
        @endforelse
    </tbody>
    <tfoot>
        <tr>
            <td colspan="5" class="text-end fw-bold">GRAND TOTAL PENGELUARAN:</td>
            <td class="text-end fw-bold text-danger">Rp {{ number_format($sumNominal, 2, ',', '.') }}</td>
            <td></td>
        </tr>
    </tfoot>
</table>

<div class="section-header">2. Rekapitulasi Beban Operasional Per Bulan-Tahun</div>
<table class="data-table">
    <thead>
        <tr>
            <th style="width: 30px;" class="text-center">No</th>
            <th>Bulan - Tahun</th>
            <th class="text-center" style="width: 100px;">Jumlah Transaksi</th>
            <th class="text-end" style="width: 150px;">Total Nominal Beban</th>
        </tr>
    </thead>
    <tbody>
        @forelse($rekaps as $idx => $rk)
        <tr>
            <td class="text-center">{{ $idx + 1 }}</td>
            <td class="fw-bold">{{ $rk['bulan_tahun'] }}</td>
            <td class="text-center">{{ $rk['jumlah_trx'] }} trx</td>
            <td class="text-end fw-bold text-danger">Rp {{ number_format($rk['total_nominal'], 2, ',', '.') }}</td>
        </tr>
        @empty
        <tr>
            <td colspan="4" class="text-center" style="color: #6b7280; padding: 10px;">Tidak ada data rekapitulasi.</td>
        </tr>
        @endforelse
    </tbody>
    <tfoot>
        <tr>
            <td colspan="2" class="text-end fw-bold">GRAND TOTAL OPERASIONAL:</td>
            <td class="text-center fw-bold">{{ array_sum(array_column($rekaps, 'jumlah_trx')) }} trx</td>
            <td class="text-end fw-bold text-danger">Rp {{ number_format(array_sum(array_column($rekaps, 'total_nominal')), 2, ',', '.') }}</td>
        </tr>
    </tfoot>
</table>
@endsection
