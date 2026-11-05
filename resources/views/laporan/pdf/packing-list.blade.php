@extends('layouts.pdf')

@section('title', 'Laporan Packing List - ' . $startDate . ' s/d ' . $endDate)
@section('report-title', 'LAPORAN TRANSAKSI PACKING LIST (KOMISI HARIAN)')
@section('page-size', 'A4 landscape')

@section('content')
@php
    $sumTotal = array_sum(array_column($details, 'total'));
    $sumDiscount = array_sum(array_column($details, 'discount_charge'));
    $sumEVoucher = array_sum(array_column($details, 'e_voucher'));
    $sumGrandTotal = array_sum(array_column($details, 'grand_total'));
    $sumPaket = array_sum(array_column($details, 'jumlah_paket'));
@endphp

<div class="section-header">1. Rincian Transaksi Packing List</div>
<table class="data-table">
    <thead>
        <tr>
            <th style="width: 25px;" class="text-center">No</th>
            <th style="width: 90px;">Kode Trx</th>
            <th style="width: 100px;">No. PL JNE</th>
            <th style="width: 80px;">Cabang</th>
            <th style="width: 70px;">Tanggal</th>
            <th class="text-end" style="width: 85px;">Total Bruto</th>
            <th class="text-end" style="width: 85px;">Disc Charge</th>
            <th class="text-end" style="width: 75px;">e-Voucher</th>
            <th class="text-end" style="width: 90px;">Grand Total</th>
            <th class="text-center" style="width: 40px;">Paket</th>
            <th>Keterangan</th>
        </tr>
    </thead>
    <tbody>
        @forelse($details as $idx => $row)
        <tr>
            <td class="text-center">{{ $idx + 1 }}</td>
            <td><span class="badge-code">{{ $row['kode_trx_pl'] }}</span></td>
            <td>{{ $row['no_pl_jne'] ?? '-' }}</td>
            <td>{{ $row['nama_cabang'] }}</td>
            <td>{{ $row['tanggal_pl'] }}</td>
            <td class="text-end">Rp {{ number_format($row['total'], 2, ',', '.') }}</td>
            <td class="text-end text-danger">- Rp {{ number_format($row['discount_charge'], 2, ',', '.') }}</td>
            <td class="text-end">Rp {{ number_format($row['e_voucher'] ?? 0, 2, ',', '.') }}</td>
            <td class="text-end fw-bold">Rp {{ number_format($row['grand_total'], 2, ',', '.') }}</td>
            <td class="text-center">{{ $row['jumlah_paket'] }}</td>
            <td>{{ $row['ket_trx_pl'] ?? '-' }}</td>
        </tr>
        @empty
        <tr>
            <td colspan="11" class="text-center" style="color: #5B6672; padding: 12px;">Tidak ada transaksi packing list pada rentang tanggal ini.</td>
        </tr>
        @endforelse
    </tbody>
    <tfoot>
        <tr>
            <td colspan="5" class="text-end fw-bold">TOTAL DETAIL:</td>
            <td class="text-end fw-bold">Rp {{ number_format($sumTotal, 2, ',', '.') }}</td>
            <td class="text-end fw-bold text-danger">- Rp {{ number_format($sumDiscount, 2, ',', '.') }}</td>
            <td class="text-end fw-bold">Rp {{ number_format($sumEVoucher, 2, ',', '.') }}</td>
            <td class="text-end fw-bold text-primary">Rp {{ number_format($sumGrandTotal, 2, ',', '.') }}</td>
            <td class="text-center fw-bold">{{ $sumPaket }}</td>
            <td></td>
        </tr>
    </tfoot>
</table>

<div class="section-header">2. Rekapitulasi Per Bulan-Tahun</div>
<table class="data-table">
    <thead>
        <tr>
            <th style="width: 25px;" class="text-center">No</th>
            <th>Bulan - Tahun</th>
            <th class="text-center" style="width: 80px;">Jumlah Trx</th>
            <th class="text-end" style="width: 100px;">Total Bruto</th>
            <th class="text-end" style="width: 100px;">Disc Charge</th>
            <th class="text-end" style="width: 90px;">e-Voucher</th>
            <th class="text-end" style="width: 100px;">Grand Total</th>
            <th class="text-center" style="width: 70px;">Total Paket</th>
        </tr>
    </thead>
    <tbody>
        @forelse($rekaps as $idx => $rk)
        <tr>
            <td class="text-center">{{ $idx + 1 }}</td>
            <td class="fw-bold">{{ $rk['bulan_tahun'] }}</td>
            <td class="text-center">{{ $rk['jumlah_trx'] }} trx</td>
            <td class="text-end">Rp {{ number_format($rk['total_bruto'] ?? 0, 2, ',', '.') }}</td>
            <td class="text-end text-danger">- Rp {{ number_format($rk['total_discount_charge'], 2, ',', '.') }}</td>
            <td class="text-end">Rp {{ number_format($rk['total_e_voucher'] ?? 0, 2, ',', '.') }}</td>
            <td class="text-end fw-bold">Rp {{ number_format($rk['total_grand_total'], 2, ',', '.') }}</td>
            <td class="text-center">{{ $rk['total_paket'] }}</td>
        </tr>
        @empty
        <tr>
            <td colspan="8" class="text-center" style="color: #5B6672; padding: 10px;">Tidak ada data rekapitulasi.</td>
        </tr>
        @endforelse
    </tbody>
    <tfoot>
        <tr>
            <td colspan="2" class="text-end fw-bold">TOTAL REKAPITULASI:</td>
            <td class="text-center fw-bold">{{ array_sum(array_column($rekaps, 'jumlah_trx')) }} trx</td>
            <td class="text-end fw-bold">Rp {{ number_format(array_sum(array_column($rekaps, 'total_bruto')), 2, ',', '.') }}</td>
            <td class="text-end fw-bold text-danger">- Rp {{ number_format(array_sum(array_column($rekaps, 'total_discount_charge')), 2, ',', '.') }}</td>
            <td class="text-end fw-bold">Rp {{ number_format(array_sum(array_column($rekaps, 'total_e_voucher')), 2, ',', '.') }}</td>
            <td class="text-end fw-bold text-primary">Rp {{ number_format(array_sum(array_column($rekaps, 'total_grand_total')), 2, ',', '.') }}</td>
            <td class="text-center fw-bold">{{ array_sum(array_column($rekaps, 'total_paket')) }}</td>
        </tr>
    </tfoot>
</table>
@endsection
