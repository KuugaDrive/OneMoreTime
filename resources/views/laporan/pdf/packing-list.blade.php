@extends('layouts.pdf')

@section('title', 'Laporan Packing List - ' . $startDate . ' s/d ' . $endDate)
@section('report-title', 'LAPORAN TRANSAKSI PACKING LIST (KOMISI HARIAN)')

@section('content')
@php
    $sumTotal = array_sum(array_column($details, 'total'));
    $sumDiscount = array_sum(array_column($details, 'discount_charge'));
    $sumEVoucher = array_sum(array_column($details, 'e_voucher'));
    $sumGrandTotal = array_sum(array_column($details, 'grand_total'));
    $sumPaket = array_sum(array_column($details, 'jumlah_paket'));
@endphp

<div class="section-header">1. Detail Transaksi Packing List</div>
<table class="data-table">
    <thead>
        <tr>
            <th style="width: 20px;" class="text-center">No</th>
            <th style="width: 75px;">Kode Trx PL</th>
            <th style="width: 80px;">No. PL JNE</th>
            <th style="width: 65px;">Cabang</th>
            <th style="width: 55px;">Tanggal</th>
            <th class="text-end" style="width: 70px;">Total Bruto</th>
            <th class="text-end" style="width: 65px;">Diskon Charge</th>
            <th class="text-end" style="width: 55px;">e-Voucher</th>
            <th class="text-end" style="width: 70px;">Grand Total</th>
            <th class="text-center" style="width: 30px;">Paket</th>
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
            <td class="text-end fw-bold text-primary">Rp {{ number_format($row['grand_total'], 2, ',', '.') }}</td>
            <td class="text-center">{{ $row['jumlah_paket'] }}</td>
            <td>{{ $row['ket_trx_pl'] ?? '-' }}</td>
        </tr>
        @empty
        <tr>
            <td colspan="11" class="text-center" style="color: #6b7280; padding: 12px;">Tidak ada transaksi packing list pada rentang filter ini.</td>
        </tr>
        @endforelse
    </tbody>
    <tfoot>
        <tr>
            <td colspan="5" class="text-end fw-bold">GRAND TOTAL DETAIL:</td>
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
            <th class="text-center" style="width: 60px;">Jumlah Trx</th>
            <th class="text-end" style="width: 80px;">Total Bruto</th>
            <th class="text-end" style="width: 80px;">Total Diskon Charge</th>
            <th class="text-end" style="width: 75px;">Total e-Voucher</th>
            <th class="text-end" style="width: 85px;">Total Grand Total</th>
            <th class="text-center" style="width: 55px;">Total Paket</th>
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
            <td class="text-end fw-bold text-primary">Rp {{ number_format($rk['total_grand_total'], 2, ',', '.') }}</td>
            <td class="text-center">{{ $rk['total_paket'] }} paket</td>
        </tr>
        @empty
        <tr>
            <td colspan="8" class="text-center" style="color: #6b7280; padding: 10px;">Tidak ada data rekapitulasi.</td>
        </tr>
        @endforelse
    </tbody>
    <tfoot>
        <tr>
            <td colspan="2" class="text-end fw-bold">GRAND TOTAL REKAPITULASI:</td>
            <td class="text-center fw-bold">{{ array_sum(array_column($rekaps, 'jumlah_trx')) }} trx</td>
            <td class="text-end fw-bold">Rp {{ number_format(array_sum(array_column($rekaps, 'total_bruto')), 2, ',', '.') }}</td>
            <td class="text-end fw-bold text-danger">- Rp {{ number_format(array_sum(array_column($rekaps, 'total_discount_charge')), 2, ',', '.') }}</td>
            <td class="text-end fw-bold">Rp {{ number_format(array_sum(array_column($rekaps, 'total_e_voucher')), 2, ',', '.') }}</td>
            <td class="text-end fw-bold text-primary">Rp {{ number_format(array_sum(array_column($rekaps, 'total_grand_total')), 2, ',', '.') }}</td>
            <td class="text-center fw-bold">{{ array_sum(array_column($rekaps, 'total_paket')) }} paket</td>
        </tr>
    </tfoot>
</table>
@endsection
