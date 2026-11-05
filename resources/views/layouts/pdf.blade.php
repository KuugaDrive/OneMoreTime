<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'Laporan Keuangan')</title>
    <style>
        @page {
            margin: 24px 28px 28px 28px;
            size: @yield('page-size', 'A4 portrait');
        }
        body {
            font-family: 'DejaVu Sans', 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 9.5px;
            color: #1F2933;
            line-height: 1.35;
            background-color: #ffffff;
            margin: 0;
            padding: 0;
        }

        /* Kop Surat Resmi */
        .kop-table {
            width: 100%;
            border-bottom: 1.5px solid #1F4E79;
            padding-bottom: 6px;
            margin-bottom: 10px;
        }
        .kop-table td {
            vertical-align: top;
        }
        .brand-title {
            font-size: 13.5px;
            font-weight: bold;
            color: #1F4E79;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }
        .report-title {
            font-size: 11.5px;
            font-weight: bold;
            color: #1F2933;
            margin-top: 2px;
            letter-spacing: 0.3px;
        }
        .kop-meta {
            font-size: 8.5px;
            color: #5B6672;
            line-height: 1.35;
        }
        .period-badge {
            background-color: #F4F5F7;
            border: 1px solid #D9DDE3;
            padding: 4px 8px;
            border-radius: 3px;
            display: inline-block;
            font-size: 9px;
            color: #1F2933;
            margin-bottom: 10px;
        }

        /* Section Title */
        .section-header {
            font-size: 10px;
            font-weight: bold;
            color: #1F4E79;
            border-bottom: 1px solid #D9DDE3;
            padding-bottom: 3px;
            margin-top: 12px;
            margin-bottom: 6px;
            text-transform: uppercase;
        }

        /* Tabel Data */
        table.data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 12px;
            font-size: 8.5px;
        }
        table.data-table th, table.data-table td {
            border: 1px solid #D9DDE3;
            padding: 4px 6px;
            vertical-align: middle;
        }
        table.data-table th {
            background-color: #F4F5F7;
            color: #1F2933;
            font-weight: bold;
            text-align: left;
        }
        table.data-table tfoot th, table.data-table tfoot td {
            background-color: #F8F9FA;
            font-weight: bold;
        }
        table.data-table tr:nth-child(even) td {
            background-color: #FAFBFC;
        }

        /* Utility */
        .text-center { text-align: center; }
        .text-end { text-align: right; }
        .text-success { color: #107C41; }
        .text-danger { color: #C5221F; }
        .text-primary { color: #1F4E79; }
        .fw-bold { font-weight: bold; }
        .badge-code {
            font-family: monospace;
            background-color: #F4F5F7;
            padding: 1px 3px;
            border: 1px solid #D9DDE3;
            border-radius: 2px;
        }

        /* Tanda Tangan */
        .signature-table {
            width: 100%;
            margin-top: 20px;
            page-break-inside: avoid;
        }
        .signature-table td {
            width: 50%;
            vertical-align: top;
            font-size: 9px;
        }
        .signature-space {
            height: 45px;
        }
        .signature-name {
            font-weight: bold;
            text-decoration: underline;
        }

        /* Footer Cetak */
        .print-footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            font-size: 7.5px;
            color: #5B6672;
            border-top: 1px solid #D9DDE3;
            padding-top: 3px;
        }
    </style>
    @yield('styles')
</head>
<body>
    <!-- KOP RESMI LAPORAN -->
    <table class="kop-table">
        <tr>
            <td style="width: 65%;">
                <div class="brand-title">{{ config('app.nama_usaha', 'AGEN RESMI JNE') }}</div>
                <div class="report-title">@yield('report-title', 'Laporan Keuangan')</div>
                <div class="kop-meta" style="margin-top: 3px;">
                    Cabang: <strong>{{ $namaCabangKop ?? 'Semua Cabang' }}</strong>
                </div>
            </td>
            <td style="width: 35%; text-align: right;">
                <div class="kop-meta">
                    Pencetak: <strong>{{ auth()->user()->username ?? 'Petugas' }}</strong> ({{ strtoupper(auth()->user()->role ?? 'ADMIN') }})<br>
                    Tanggal Cetak: <strong>{{ date('d-m-Y H:i') }}</strong>
                </div>
            </td>
        </tr>
    </table>

    <div class="period-badge">
        Periode Laporan: <strong>{{ $startDate }}</strong> s/d <strong>{{ $endDate }}</strong> &nbsp;|&nbsp;
        Cabang: <strong>{{ $namaCabangKop ?? 'Semua Cabang' }}</strong>
    </div>

    <!-- KONTEN LAPORAN -->
    @yield('content')

    <!-- TANDA TANGAN / PENGESAHAN -->
    <table class="signature-table">
        <tr>
            <td style="text-align: center;">
                Dibuat Oleh,<br>
                <strong>Petugas Administrasi</strong>
                <div class="signature-space"></div>
                <div class="signature-name">( {{ auth()->user()->username ?? 'Petugas' }} )</div>
                <div class="kop-meta">{{ config('app.nama_usaha', 'Agen Resmi JNE') }}</div>
            </td>
            <td style="text-align: center;">
                Mengetahui / Menyetujui,<br>
                <strong>Pimpinan / Pengelola</strong>
                <div class="signature-space"></div>
                <div class="signature-name">( Pimpinan Usaha )</div>
                <div class="kop-meta">{{ config('app.nama_usaha', 'Agen Resmi JNE') }}</div>
            </td>
        </tr>
    </table>

    <div class="print-footer">
        <div>{{ config('app.name', 'Sistem Administrasi Keuangan') }} &bull; Laporan dicetak otomatis pada {{ date('d-m-Y H:i:s') }}</div>
    </div>
</body>
</html>
