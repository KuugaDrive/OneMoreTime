<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'Laporan JNE')</title>
    <style>
        @page {
            margin: 28px 32px 35px 32px;
            size: A4 portrait;
        }
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 11px;
            color: #1f2937;
            line-height: 1.35;
            background-color: #ffffff;
            margin: 0;
            padding: 0;
        }

        /* Kop Surat Resmi */
        .kop-table {
            width: 100%;
            border-bottom: 2px solid #dc2626;
            padding-bottom: 8px;
            margin-bottom: 12px;
        }
        .kop-table td {
            vertical-align: middle;
        }
        .brand-title {
            font-size: 16px;
            font-weight: bold;
            color: #1e3a8a;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }
        .report-title {
            font-size: 13px;
            font-weight: bold;
            color: #dc2626;
            margin-top: 2px;
            letter-spacing: 0.3px;
        }
        .kop-meta {
            font-size: 9px;
            color: #6b7280;
            line-height: 1.3;
        }
        .period-badge {
            background-color: #f3f4f6;
            border: 1px solid #e5e7eb;
            padding: 5px 10px;
            border-radius: 4px;
            display: inline-block;
            font-size: 10px;
            font-weight: normal;
            color: #111827;
            margin-bottom: 12px;
        }

        /* Section Title */
        .section-header {
            font-size: 11px;
            font-weight: bold;
            color: #1e3a8a;
            border-bottom: 1px solid #cbd5e1;
            padding-bottom: 4px;
            margin-top: 14px;
            margin-bottom: 8px;
            text-transform: uppercase;
        }

        /* Tabel Data */
        table.data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 14px;
            font-size: 10px;
        }
        table.data-table th, table.data-table td {
            border: 1px solid #d1d5db;
            padding: 5px 7px;
            vertical-align: middle;
        }
        table.data-table th {
            background-color: #f8fafc;
            color: #334155;
            font-weight: bold;
            text-align: left;
        }
        table.data-table tfoot th, table.data-table tfoot td {
            background-color: #f1f5f9;
            font-weight: bold;
        }
        table.data-table tr:nth-child(even) td {
            background-color: #fafafa;
        }

        /* Utility */
        .text-center { text-align: center; }
        .text-end { text-align: right; }
        .text-success { color: #16a34a; }
        .text-danger { color: #dc2626; }
        .text-primary { color: #1e3a8a; }
        .fw-bold { font-weight: bold; }
        .badge-code {
            font-family: monospace;
            background-color: #f3f4f6;
            padding: 2px 4px;
            border: 1px solid #e5e7eb;
            border-radius: 3px;
        }

        /* Tanda Tangan / Signature Footer */
        .signature-table {
            width: 100%;
            margin-top: 25px;
            page-break-inside: avoid;
        }
        .signature-table td {
            width: 50%;
            vertical-align: top;
            font-size: 10px;
        }
        .signature-space {
            height: 50px;
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
            font-size: 8px;
            color: #9ca3af;
            border-top: 1px solid #e5e7eb;
            padding-top: 4px;
            display: flex;
            justify-content: space-between;
        }
    </style>
    @yield('styles')
</head>
<body>
    <!-- KOP RESMI AGEN JNE -->
    <table class="kop-table">
        <tr>
            <td style="width: 65%;">
                <div class="brand-title">AGEN RESMI JNE EXPRESS</div>
                <div class="report-title">@yield('report-title', 'Laporan Resmi')</div>
                <div class="kop-meta" style="margin-top: 3px;">
                    <strong>Cabang:</strong> {{ $namaCabangKop ?? 'Semua Cabang' }} |
                    Jl. Tomang Raya No. 11, Jakarta Barat | Telp: (021) 566-7890
                </div>
            </td>
            <td style="width: 35%; text-align: right;">
                <div class="kop-meta">
                    <strong>Pencetak:</strong> {{ auth()->user()->username ?? 'Owner' }} ({{ strtoupper(auth()->user()->role ?? 'OWNER') }})<br>
                    <strong>Tanggal Cetak:</strong> {{ date('d-m-Y H:i') }} WIB<br>
                    <strong>Status Dokumen:</strong> RESMI
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
                Mengetahui / Penanggung Jawab,<br>
                <strong>Admin Operasional</strong>
                <div class="signature-space"></div>
                <div class="signature-name">( Admin Operasional )</div>
                <div class="kop-meta">Petugas Kasir & Operasional</div>
            </td>
            <td style="text-align: center;">
                Jakarta, {{ date('d-m-Y') }}<br>
                Disetujui Oleh,<br>
                <strong>Owner / Pimpinan Agen</strong>
                <div class="signature-space"></div>
                <div class="signature-name">( {{ auth()->user()->username ?? 'Owner Utama' }} )</div>
                <div class="kop-meta">Owner Agen Resmi JNE</div>
            </td>
        </tr>
    </table>

    <div class="print-footer">
        <div>Dokumen ini digenerate secara otomatis oleh Sistem Pencatatan JNE Agent.</div>
    </div>
</body>
</html>
