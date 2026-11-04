@extends('layouts.pdf')

@section('title', 'Laporan Laba Bersih - ' . $startDate . ' s/d ' . $endDate)
@section('report-title', 'LAPORAN REKAPITULASI LABA BERSIH AGEN JNE')

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
            <th class="text-end" style="width: 130px;">Laba Bersih Agen</th>
            <th class="text-center" style="width: 90px;">Status Finansial</th>
        </tr>
    </thead>
    <tbody>
        @forelse($rekaps as $idx => $row)
        <tr>
            <td class="text-center">{{ $idx + 1 }}</td>
            <td class="fw-bold">{{ $row['bulan_tahun'] }}</td>
            <td class="text-end text-primary fw-bold">Rp {{ number_format($row['total_pl'], 2, ',', '.') }}</td>
            <td class="text-end text-danger fw-bold">Rp {{ number_format($row['total_ops'], 2, ',', '.') }}</td>
            <td class="text-end fw-bold {{ $row['laba_bersih'] >= 0 ? 'text-success' : 'text-danger' }}">
                Rp {{ number_format($row['laba_bersih'], 2, ',', '.') }}
            </td>
            <td class="text-center">
                @if($row['status'] === 'Surplus')
                    <span style="color: #16a34a; font-weight: bold; background-color: #dcfce7; padding: 2px 6px; border-radius: 3px; border: 1px solid #bbf7d0;">Surplus</span>
                @else
                    <span style="color: #dc2626; font-weight: bold; background-color: #fee2e2; padding: 2px 6px; border-radius: 3px; border: 1px solid #fecaca;">Defisit</span>
                @endif
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="6" class="text-center" style="color: #6b7280; padding: 12px;">Tidak ada transaksi PL maupun Operasional pada rentang filter ini.</td>
        </tr>
        @endforelse
    </tbody>
    <tfoot>
        <tr>
            <td colspan="2" class="text-end fw-bold">GRAND TOTAL AKUMULASI:</td>
            <td class="text-end fw-bold text-primary">Rp {{ number_format($sumPl, 2, ',', '.') }}</td>
            <td class="text-end fw-bold text-danger">Rp {{ number_format($sumOps, 2, ',', '.') }}</td>
            <td class="text-end fw-bold {{ $sumLaba >= 0 ? 'text-success' : 'text-danger' }}">
                Rp {{ number_format($sumLaba, 2, ',', '.') }}
            </td>
            <td class="text-center fw-bold {{ $sumLaba >= 0 ? 'text-success' : 'text-danger' }}">
                {{ $sumLaba >= 0 ? 'SURPLUS' : 'DEFISIT' }}
            </td>
        </tr>
    </tfoot>
</table>

<div style="background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 4px; padding: 8px 12px; margin-top: 10px; font-size: 9px; color: #475569;">
    <strong>Catatan Rumus Finansial:</strong><br>
    &bull; <strong>Rumus:</strong> {{ $cfg['rumus_keterangan'] ?? 'Laba Bersih = Komisi PL (Disc Charge) - Biaya Operasional' }}<br>
    &bull; <strong>{{ $cfg['pl_column_label'] ?? 'Pendapatan Komisi (Disc Charge)' }}:</strong> {{ ($cfg['basis'] ?? '') === 'grand_total' ? 'Akumulasi nilai Grand Total PL.' : 'Akumulasi komisi agen (Discount Charge) dokumen PL.' }}<br>
    &bull; <strong>Total Biaya Ops:</strong> Akumulasi pengeluaran biaya operasional agen.
</div>
@endsection
