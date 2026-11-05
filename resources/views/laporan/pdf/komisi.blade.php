@extends('layouts.pdf')

@section('title', 'Laporan Komisi Bulanan - ' . $startDate . ' s/d ' . $endDate)
@section('report-title', 'LAPORAN KOMISI BULANAN')

@section('content')
@php
    $sumNominal = array_sum(array_column($details, 'nominal'));
@endphp

<div class="section-header">1. Rincian Riwayat Transfer Komisi</div>
<table class="data-table">
    <thead>
        <tr>
            <th style="width: 25px;" class="text-center">No</th>
            <th style="width: 100px;">Kode Trx CL</th>
            <th style="width: 90px;">Cabang</th>
            <th style="width: 75px;">Tanggal Transfer</th>
            <th class="text-end" style="width: 110px;">Nominal (Rp)</th>
            <th>Keterangan Transfer</th>
        </tr>
    </thead>
    <tbody>
        @forelse($details as $idx => $row)
        <tr>
            <td class="text-center">{{ $idx + 1 }}</td>
            <td><span class="badge-code">{{ $row['kode_trx_cl'] }}</span></td>
            <td>{{ $row['nama_cabang'] }}</td>
            <td>{{ $row['tanggal_tf'] }}</td>
            <td class="text-end fw-medium">Rp {{ number_format($row['nominal'], 2, ',', '.') }}</td>
            <td>{{ $row['ket_trx_cl'] ?? '-' }}</td>
        </tr>
        @empty
        <tr>
            <td colspan="6" class="text-center" style="color: #5B6672; padding: 12px;">Tidak ada riwayat transfer komisi pada rentang tanggal ini.</td>
        </tr>
        @endforelse
    </tbody>
    <tfoot>
        <tr>
            <td colspan="4" class="text-end fw-bold">TOTAL DETAIL:</td>
            <td class="text-end fw-bold">Rp {{ number_format($sumNominal, 2, ',', '.') }}</td>
            <td></td>
        </tr>
    </tfoot>
</table>

<div class="section-header">2. Rekapitulasi Komisi Bulanan Per Tahun</div>
<table class="data-table">
    <thead>
        <tr>
            <th style="width: 140px;">Bulan-Tahun</th>
            <th class="text-end" style="width: 130px;">Komisi menurut sistem (Rp)</th>
            <th class="text-end" style="width: 130px;">Transfer masuk (Rp)</th>
            <th class="text-end" style="width: 130px;">Selisih (Rp)</th>
        </tr>
    </thead>
    <tbody>
        @forelse($rekaps as $rk)
        @php
            $sel = (float) $rk['selisih'];
        @endphp
        <tr>
            <td class="fw-bold">{{ $rk['bulan_tahun'] }}</td>
            <td class="text-end">Rp {{ number_format($rk['komisi_sistem'], 2, ',', '.') }}</td>
            <td class="text-end">Rp {{ number_format($rk['transfer_masuk'], 2, ',', '.') }}</td>
            <td class="text-end fw-bold" style="{{ $sel > 0.001 ? 'color: #107C41;' : ($sel < -0.001 ? 'color: #C5221F;' : 'color: #1F2933;') }}">
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
            <td colspan="4" class="text-center" style="color: #5B6672; padding: 10px;">Tidak ada data rekapitulasi.</td>
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
            <td class="text-end fw-bold">TOTAL REKONSILIASI:</td>
            <td class="text-end fw-bold">Rp {{ number_format($totKomisi, 2, ',', '.') }}</td>
            <td class="text-end fw-bold">Rp {{ number_format($totTransfer, 2, ',', '.') }}</td>
            <td class="text-end fw-bold" style="{{ $totSelisih > 0.001 ? 'color: #107C41;' : ($totSelisih < -0.001 ? 'color: #C5221F;' : 'color: #1F2933;') }}">
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

<div style="margin-top: 10px; font-size: 8pt; color: #5B6672; line-height: 1.4;">
    <strong>Catatan:</strong> Selisih = Transfer masuk - Komisi menurut sistem (Disc Charge Packing List). Selisih tidak ikut dihitung dalam Laba Bersih.
    @if(($lag ?? 0) > 0)
    <br><span style="color: #1F4E79;">* Komisi menurut sistem dihitung dari Packing List {{ $lag }} bulan sebelumnya.</span>
    @endif
</div>
@endsection
