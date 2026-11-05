@extends('layouts.pdf')

@section('title', 'Laporan Laba Bersih - ' . $startDate . ' s/d ' . $endDate)
@section('report-title', 'LAPORAN LABA BERSIH')

@section('content')
@php
    $sumPl = array_sum(array_column($rekaps, 'total_pl'));
    $sumOps = array_sum(array_column($rekaps, 'total_ops'));
    $sumLaba = array_sum(array_column($rekaps, 'laba_bersih'));
@endphp

<div class="section-header">1. Rekapitulasi Laba Bersih Per Bulan-Tahun</div>
<table class="data-table">
    <thead>
        <tr>
            <th style="width: 30px;" class="text-center">No</th>
            <th>Bulan - Tahun</th>
            <th class="text-end" style="width: 140px;">{{ $cfg['pl_column_label'] ?? 'Pendapatan Komisi (Disc Charge)' }}</th>
            <th class="text-end" style="width: 120px;">Total Biaya Ops</th>
            <th class="text-end" style="width: 130px;">Laba Bersih</th>
            <th class="text-center" style="width: 80px;">Status</th>
        </tr>
    </thead>
    <tbody>
        @forelse($rekaps as $idx => $row)
        <tr>
            <td class="text-center">{{ $idx + 1 }}</td>
            <td class="fw-bold">{{ $row['bulan_tahun'] }}</td>
            <td class="text-end">Rp {{ number_format($row['total_pl'], 2, ',', '.') }}</td>
            <td class="text-end">Rp {{ number_format($row['total_ops'], 2, ',', '.') }}</td>
            <td class="text-end fw-semibold {{ $row['laba_bersih'] >= 0 ? 'text-success' : 'text-danger' }}">
                Rp {{ number_format($row['laba_bersih'], 2, ',', '.') }}
            </td>
            <td class="text-center">
                @if($row['status'] === 'Surplus')
                    <span class="text-success fw-bold">Surplus</span>
                @else
                    <span class="text-danger fw-bold">Defisit</span>
                @endif
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="6" class="text-center" style="color: #5B6672; padding: 12px;">Tidak ada transaksi PL maupun Operasional pada rentang tanggal ini.</td>
        </tr>
        @endforelse
    </tbody>
    <tfoot>
        <tr>
            <td colspan="2" class="text-end fw-bold">TOTAL AKUMULASI:</td>
            <td class="text-end fw-bold">Rp {{ number_format($sumPl, 2, ',', '.') }}</td>
            <td class="text-end fw-bold">Rp {{ number_format($sumOps, 2, ',', '.') }}</td>
            <td class="text-end fw-bold {{ $sumLaba >= 0 ? 'text-success' : 'text-danger' }}">
                Rp {{ number_format($sumLaba, 2, ',', '.') }}
            </td>
            <td class="text-center fw-bold {{ $sumLaba >= 0 ? 'text-success' : 'text-danger' }}">
                {{ $sumLaba >= 0 ? 'SURPLUS' : 'DEFISIT' }}
            </td>
        </tr>
    </tfoot>
</table>

<div style="background-color: #F8F9FA; border: 1px solid #D9DDE3; border-radius: 3px; padding: 6px 10px; margin-top: 10px; font-size: 8.5px; color: #5B6672;">
    <strong>Keterangan Rumus:</strong><br>
    &bull; <strong>Rumus:</strong> {{ $cfg['rumus_keterangan'] ?? 'Laba Bersih = Komisi PL (Disc Charge) - Biaya Operasional' }}<br>
    &bull; <strong>{{ $cfg['pl_column_label'] ?? 'Pendapatan Komisi (Disc Charge)' }}:</strong> {{ ($cfg['basis'] ?? '') === 'grand_total' ? 'Akumulasi nilai Grand Total PL.' : 'Akumulasi komisi agen (Discount Charge) dokumen PL.' }}<br>
    &bull; <strong>Total Biaya Ops:</strong> Akumulasi pengeluaran biaya operasional agen.
</div>
@endsection
