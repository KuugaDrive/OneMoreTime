<?php

namespace App\Http\Controllers;

use App\Models\PackingList;
use App\Models\Operasional;
use App\Models\Komisi;
use App\Models\Cabang;
use App\Services\LabaBersihService;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class LaporanController extends Controller
{
    protected function getIndonesianMonth(int $month): string
    {
        $months = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];
        return $months[$month] ?? '';
    }

    protected function parseFilterDates(Request $request): array
    {
        $rawStart = $request->input('tanggal_awal') ?? $request->input('filterTanggalAwal');
        $rawEnd = $request->input('tanggal_akhir') ?? $request->input('filterTanggalAkhir');

        try {
            $start = $rawStart ? Carbon::createFromFormat('d-m-Y', trim($rawStart))->format('Y-m-d') : '2025-01-01';
        } catch (\Exception $e) {
            $start = $rawStart ? Carbon::parse($rawStart)->format('Y-m-d') : '2025-01-01';
        }

        try {
            $end = $rawEnd ? Carbon::createFromFormat('d-m-Y', trim($rawEnd))->format('Y-m-d') : Carbon::now()->format('Y-m-d');
        } catch (\Exception $e) {
            $end = $rawEnd ? Carbon::parse($rawEnd)->format('Y-m-d') : Carbon::now()->format('Y-m-d');
        }

        // BAGIAN G: Validasi filter tanggal_akhir >= tanggal_awal
        if ($end < $start) {
            throw ValidationException::withMessages([
                'tanggal_akhir' => ['Tanggal akhir harus lebih besar atau sama dengan tanggal awal.'],
            ]);
        }

        return [$start, $end];
    }

    protected function getCabangContext(?string $idCab): array
    {
        $cabangs = Cabang::orderBy('id_cab')->get();
        $selectedCabang = $idCab ?: 'all';
        $namaCabangKop = 'Semua Cabang';

        if ($selectedCabang !== 'all' && !empty($selectedCabang)) {
            $c = $cabangs->firstWhere('id_cab', $selectedCabang);
            if ($c) {
                $namaCabangKop = $c->nama_cabang . " ({$c->id_cab})";
            }
        }

        return [$cabangs, $selectedCabang, $namaCabangKop];
    }

    // ==========================================
    // 1. LAPORAN PACKING LIST
    // ==========================================
    protected function getPackingListData(string $start, string $end, ?string $idCab = null): array
    {
        $query = PackingList::with('cabang')->whereBetween('tanggal_pl', [$start, $end]);
        if (!empty($idCab) && $idCab !== 'all') {
            $query->where('id_cab', $idCab);
        }

        $details = $query
            ->orderBy('tanggal_pl', 'desc')
            ->orderBy('kode_trx_pl', 'desc')
            ->get()
            ->map(function ($item) {
                return [
                    'kode_trx_pl' => $item->kode_trx_pl,
                    'no_pl_jne' => $item->no_pl_jne,
                    'id_cab' => $item->id_cab,
                    'nama_cabang' => $item->cabang ? $item->cabang->nama_cabang : '-',
                    'tanggal_pl' => Carbon::parse($item->tanggal_pl)->format('d-m-Y'),
                    'total' => (float) $item->total,
                    'discount_charge' => (float) $item->discount_charge,
                    'e_voucher' => (float) $item->e_voucher,
                    'grand_total' => (float) $item->grand_total,
                    'jumlah_paket' => $item->jumlah_paket,
                    'ket_trx_pl' => $item->ket_trx_pl,
                ];
            })
            ->toArray();

        $rekapQuery = PackingList::select(
                DB::raw('YEAR(tanggal_pl) as year'),
                DB::raw('MONTH(tanggal_pl) as month'),
                DB::raw('SUM(total) as total_bruto'),
                DB::raw('SUM(discount_charge) as total_discount_charge'),
                DB::raw('SUM(e_voucher) as total_e_voucher'),
                DB::raw('SUM(grand_total) as total_grand_total'),
                DB::raw('SUM(jumlah_paket) as total_paket'),
                DB::raw('COUNT(*) as jumlah_trx')
            )
            ->whereBetween('tanggal_pl', [$start, $end]);

        if (!empty($idCab) && $idCab !== 'all') {
            $rekapQuery->where('id_cab', $idCab);
        }

        $rekapsRaw = $rekapQuery
            ->groupBy(DB::raw('YEAR(tanggal_pl)'), DB::raw('MONTH(tanggal_pl)'))
            ->orderBy(DB::raw('YEAR(tanggal_pl)'), 'asc')
            ->orderBy(DB::raw('MONTH(tanggal_pl)'), 'asc')
            ->get();

        $rekaps = $rekapsRaw->map(function ($r) {
            return [
                'bulan_tahun' => $this->getIndonesianMonth((int)$r->month) . ' ' . $r->year,
                'total_bruto' => (float) $r->total_bruto,
                'total_discount_charge' => (float) $r->total_discount_charge,
                'total_e_voucher' => (float) $r->total_e_voucher,
                'total_grand_total' => (float) $r->total_grand_total,
                'total_paket' => (int) $r->total_paket,
                'jumlah_trx' => (int) $r->jumlah_trx,
            ];
        })->toArray();

        return [$details, $rekaps];
    }

    public function packingList(Request $request)
    {
        [$start, $end] = $this->parseFilterDates($request);
        [$cabangs, $selectedCabang, $namaCabangKop] = $this->getCabangContext($request->id_cab);
        [$details, $rekaps] = $this->getPackingListData($start, $end, $selectedCabang);

        $startDate = Carbon::parse($start)->format('d-m-Y');
        $endDate = Carbon::parse($end)->format('d-m-Y');

        return view('laporan.packing-list', compact(
            'details', 'rekaps', 'start', 'end', 'startDate', 'endDate',
            'cabangs', 'selectedCabang', 'namaCabangKop'
        ));
    }

    public function packingListPdf(Request $request)
    {
        [$start, $end] = $this->parseFilterDates($request);
        [$cabangs, $selectedCabang, $namaCabangKop] = $this->getCabangContext($request->id_cab);
        [$details, $rekaps] = $this->getPackingListData($start, $end, $selectedCabang);

        $startDate = Carbon::parse($start)->format('d-m-Y');
        $endDate = Carbon::parse($end)->format('d-m-Y');

        $pdf = Pdf::loadView('laporan.pdf.packing-list', compact(
            'details', 'rekaps', 'startDate', 'endDate', 'namaCabangKop'
        ))->setPaper('a4', 'portrait');

        $filename = "Laporan_Packing_List_{$startDate}_sd_{$endDate}.pdf";
        return $pdf->download($filename);
    }

    // ==========================================
    // 2. LAPORAN OPERASIONAL
    // ==========================================
    protected function getOperasionalData(string $start, string $end, ?string $idCab = null): array
    {
        $query = Operasional::with(['bebanOps', 'cabang'])->whereBetween('tanggal_trx_ops', [$start, $end]);
        if (!empty($idCab) && $idCab !== 'all') {
            $query->where('id_cab', $idCab);
        }

        $details = $query
            ->orderBy('tanggal_trx_ops', 'desc')
            ->orderBy('kode_trx_ops', 'desc')
            ->get()
            ->map(function ($item) {
                return [
                    'kode_trx_ops' => $item->kode_trx_ops,
                    'id_cab' => $item->id_cab,
                    'nama_cabang' => $item->cabang ? $item->cabang->nama_cabang : '-',
                    'tanggal_trx_ops' => Carbon::parse($item->tanggal_trx_ops)->format('d-m-Y'),
                    'beban_ops' => $item->bebanOps ? $item->bebanOps->beban_ops : '-',
                    'nominal' => (float) $item->nominal,
                    'ket_trx_ops' => $item->ket_trx_ops,
                ];
            })
            ->toArray();

        $rekapQuery = Operasional::select(
                DB::raw('YEAR(tanggal_trx_ops) as year'),
                DB::raw('MONTH(tanggal_trx_ops) as month'),
                DB::raw('SUM(nominal) as total_nominal'),
                DB::raw('COUNT(*) as jumlah_trx')
            )
            ->whereBetween('tanggal_trx_ops', [$start, $end]);

        if (!empty($idCab) && $idCab !== 'all') {
            $rekapQuery->where('id_cab', $idCab);
        }

        $rekapsRaw = $rekapQuery
            ->groupBy(DB::raw('YEAR(tanggal_trx_ops)'), DB::raw('MONTH(tanggal_trx_ops)'))
            ->orderBy(DB::raw('YEAR(tanggal_trx_ops)'), 'asc')
            ->orderBy(DB::raw('MONTH(tanggal_trx_ops)'), 'asc')
            ->get();

        $rekaps = $rekapsRaw->map(function ($r) {
            return [
                'bulan_tahun' => $this->getIndonesianMonth((int)$r->month) . ' ' . $r->year,
                'total_nominal' => (float) $r->total_nominal,
                'jumlah_trx' => (int) $r->jumlah_trx,
            ];
        })->toArray();

        return [$details, $rekaps];
    }

    public function operasional(Request $request)
    {
        [$start, $end] = $this->parseFilterDates($request);
        [$cabangs, $selectedCabang, $namaCabangKop] = $this->getCabangContext($request->id_cab);
        [$details, $rekaps] = $this->getOperasionalData($start, $end, $selectedCabang);

        $startDate = Carbon::parse($start)->format('d-m-Y');
        $endDate = Carbon::parse($end)->format('d-m-Y');

        return view('laporan.operasional', compact(
            'details', 'rekaps', 'start', 'end', 'startDate', 'endDate',
            'cabangs', 'selectedCabang', 'namaCabangKop'
        ));
    }

    public function operasionalPdf(Request $request)
    {
        [$start, $end] = $this->parseFilterDates($request);
        [$cabangs, $selectedCabang, $namaCabangKop] = $this->getCabangContext($request->id_cab);
        [$details, $rekaps] = $this->getOperasionalData($start, $end, $selectedCabang);

        $startDate = Carbon::parse($start)->format('d-m-Y');
        $endDate = Carbon::parse($end)->format('d-m-Y');

        $pdf = Pdf::loadView('laporan.pdf.operasional', compact(
            'details', 'rekaps', 'startDate', 'endDate', 'namaCabangKop'
        ))->setPaper('a4', 'portrait');

        $filename = "Laporan_Operasional_{$startDate}_sd_{$endDate}.pdf";
        return $pdf->download($filename);
    }

    // ==========================================
    // 3. LAPORAN LABA BERSIH (TERPUSAT VIA LabaBersihService)
    // ==========================================
    public function labaBersih(Request $request)
    {
        [$start, $end] = $this->parseFilterDates($request);
        [$cabangs, $selectedCabang, $namaCabangKop] = $this->getCabangContext($request->id_cab);

        $service = new LabaBersihService();
        $labaResult = $service->calculateMonthly($start, $end, $selectedCabang);
        $rekaps = $labaResult['rekaps'];
        $totals = $labaResult['totals'];
        $cfg = $labaResult['config'];

        $startDate = Carbon::parse($start)->format('d-m-Y');
        $endDate = Carbon::parse($end)->format('d-m-Y');

        return view('laporan.laba-bersih', compact(
            'rekaps', 'totals', 'cfg', 'start', 'end', 'startDate', 'endDate',
            'cabangs', 'selectedCabang', 'namaCabangKop'
        ));
    }

    public function labaBersihPdf(Request $request)
    {
        [$start, $end] = $this->parseFilterDates($request);
        [$cabangs, $selectedCabang, $namaCabangKop] = $this->getCabangContext($request->id_cab);

        $service = new LabaBersihService();
        $labaResult = $service->calculateMonthly($start, $end, $selectedCabang);
        $rekaps = $labaResult['rekaps'];
        $totals = $labaResult['totals'];
        $cfg = $labaResult['config'];

        $startDate = Carbon::parse($start)->format('d-m-Y');
        $endDate = Carbon::parse($end)->format('d-m-Y');

        $pdf = Pdf::loadView('laporan.pdf.laba-bersih', compact(
            'rekaps', 'totals', 'cfg', 'startDate', 'endDate', 'namaCabangKop'
        ))->setPaper('a4', 'portrait');

        $filename = "Laporan_Laba_Bersih_{$startDate}_sd_{$endDate}.pdf";
        return $pdf->download($filename);
    }

    // ==========================================
    // 4. LAPORAN KOMISI BULANAN
    // ==========================================
    protected function getKomisiData(string $start, string $end, ?string $idCab = null): array
    {
        $query = Komisi::with('cabang')->whereBetween('tanggal_tf', [$start, $end]);
        if (!empty($idCab) && $idCab !== 'all') {
            $query->where('id_cab', $idCab);
        }

        $details = $query
            ->orderBy('tanggal_tf', 'desc')
            ->orderBy('kode_trx_cl', 'desc')
            ->get()
            ->map(function ($item) {
                return [
                    'kode_trx_cl' => $item->kode_trx_cl,
                    'id_cab' => $item->id_cab,
                    'nama_cabang' => $item->cabang ? $item->cabang->nama_cabang : '-',
                    'tanggal_tf' => Carbon::parse($item->tanggal_tf)->format('d-m-Y'),
                    'nominal' => (float) $item->nominal,
                    'ket_trx_cl' => $item->ket_trx_cl,
                ];
            })
            ->toArray();

        $lag = (int) config('laporan.komisi_transfer_lag_bulan', 0);

        // 1. Ambil transfer masuk (Komisi) per bulan pada rentang tanggal_tf
        $komisiQuery = Komisi::select(
                DB::raw('YEAR(tanggal_tf) as year'),
                DB::raw('MONTH(tanggal_tf) as month'),
                DB::raw('SUM(nominal) as total_nominal'),
                DB::raw('COUNT(*) as jumlah_tf')
            )
            ->whereBetween('tanggal_tf', [$start, $end]);

        if (!empty($idCab) && $idCab !== 'all') {
            $komisiQuery->where('id_cab', $idCab);
        }

        $komisiMonthly = $komisiQuery
            ->groupBy(DB::raw('YEAR(tanggal_tf)'), DB::raw('MONTH(tanggal_tf)'))
            ->get()
            ->keyBy(function ($item) {
                return sprintf('%04d-%02d', $item->year, $item->month);
            });

        // 2. Ambil Packing List pasangannya pada rentang yang digeser sesuai lag
        // Contoh: filter transfer Jan-Mar 2026 dan lag=1, maka PL ditarik Des 2025-Feb 2026
        $startCarbon = Carbon::parse($start);
        $endCarbon = Carbon::parse($end);

        $plStartDate = $startCarbon->copy()->subMonthsNoOverflow($lag)->format('Y-m-d');
        $plEndDate = $endCarbon->copy()->subMonthsNoOverflow($lag)->format('Y-m-d');

        $plQuery = PackingList::select(
                DB::raw('YEAR(tanggal_pl) as year'),
                DB::raw('MONTH(tanggal_pl) as month'),
                DB::raw('SUM(discount_charge) as total_disc_charge')
            )
            ->whereBetween('tanggal_pl', [$plStartDate, $plEndDate]);

        if (!empty($idCab) && $idCab !== 'all') {
            $plQuery->where('id_cab', $idCab);
        }

        $plMonthlyRaw = $plQuery
            ->groupBy(DB::raw('YEAR(tanggal_pl)'), DB::raw('MONTH(tanggal_pl)'))
            ->get();

        // Petakan ke bulan transfer (digeser +lag bulan)
        $plShifted = [];
        foreach ($plMonthlyRaw as $plItem) {
            $transferYm = Carbon::createFromDate($plItem->year, $plItem->month, 1)
                ->addMonthsNoOverflow($lag)
                ->format('Y-m');
            $plShifted[$transferYm] = (float) $plItem->total_disc_charge;
        }

        // Gabungkan seluruh bulan transfer unik
        $allMonths = $komisiMonthly->keys()
            ->merge(array_keys($plShifted))
            ->unique()
            ->sort()
            ->values();

        $rekaps = [];
        foreach ($allMonths as $ym) {
            [$year, $month] = explode('-', $ym);
            $komisiSistem = (float) ($plShifted[$ym] ?? 0);
            $transferMasuk = (float) ($komisiMonthly->get($ym)->total_nominal ?? 0);
            $selisih = $transferMasuk - $komisiSistem;

            $rekaps[] = [
                'year' => (int) $year,
                'month' => (int) $month,
                'bulan_tahun' => $this->getIndonesianMonth((int) $month) . ' ' . $year,
                'komisi_sistem' => $komisiSistem,
                'transfer_masuk' => $transferMasuk,
                'selisih' => $selisih,
            ];
        }

        return [$details, $rekaps, $lag];
    }

    public function komisi(Request $request)
    {
        [$start, $end] = $this->parseFilterDates($request);
        [$cabangs, $selectedCabang, $namaCabangKop] = $this->getCabangContext($request->id_cab);
        [$details, $rekaps, $lag] = $this->getKomisiData($start, $end, $selectedCabang);

        $startDate = Carbon::parse($start)->format('d-m-Y');
        $endDate = Carbon::parse($end)->format('d-m-Y');

        return view('laporan.komisi', compact(
            'details', 'rekaps', 'lag', 'start', 'end', 'startDate', 'endDate',
            'cabangs', 'selectedCabang', 'namaCabangKop'
        ));
    }

    public function komisiPdf(Request $request)
    {
        [$start, $end] = $this->parseFilterDates($request);
        [$cabangs, $selectedCabang, $namaCabangKop] = $this->getCabangContext($request->id_cab);
        [$details, $rekaps, $lag] = $this->getKomisiData($start, $end, $selectedCabang);

        $startDate = Carbon::parse($start)->format('d-m-Y');
        $endDate = Carbon::parse($end)->format('d-m-Y');

        $pdf = Pdf::loadView('laporan.pdf.komisi', compact(
            'details', 'rekaps', 'lag', 'startDate', 'endDate', 'namaCabangKop'
        ))->setPaper('a4', 'portrait');

        $filename = "Laporan_Komisi_{$startDate}_sd_{$endDate}.pdf";
        return $pdf->download($filename);
    }
}
