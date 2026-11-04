<?php

namespace App\Services;

use App\Models\PackingList;
use App\Models\Operasional;
use App\Models\Komisi;
use App\Models\Cabang;
use Illuminate\Support\Facades\DB;

class LabaBersihService
{
    /**
     * Nama bulan Indonesia
     */
    public static function getIndonesianMonth(int $month): string
    {
        $months = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];
        return $months[$month] ?? '';
    }

    /**
     * Ambil konfigurasi saat ini
     */
    public static function getConfig(?string $overrideBasis = null, ?bool $overrideIncludeKomisi = null): array
    {
        $basis = $overrideBasis ?? config('laporan.laba_basis', 'discount_charge');
        $includeKomisi = $overrideIncludeKomisi ?? config('laporan.laba_include_komisi_bulanan', false);

        $plColumnLabel = ($basis === 'grand_total') ? 'Grand Total PL' : 'Pendapatan Komisi (Disc Charge)';

        $rumusText = ($basis === 'grand_total')
            ? 'Laba Bersih = Grand Total PL - Biaya Operasional'
            : 'Laba Bersih = Komisi PL (Disc Charge) - Biaya Operasional';

        if ($includeKomisi) {
            $rumusText .= ' + Komisi Bulanan';
        }

        return [
            'basis' => $basis,
            'include_komisi' => (bool) $includeKomisi,
            'pl_column_label' => $plColumnLabel,
            'rumus_keterangan' => $rumusText,
        ];
    }

    /**
     * Hitung rekap bulanan laba bersih untuk laporan
     */
    public function calculateMonthly(
        string $startDate,
        string $endDate,
        ?string $idCab = null,
        ?string $overrideBasis = null,
        ?bool $overrideIncludeKomisi = null
    ): array {
        $cfg = self::getConfig($overrideBasis, $overrideIncludeKomisi);
        $basis = $cfg['basis'];
        $includeKomisi = $cfg['include_komisi'];

        // 1. Agregasi Packing List
        $plQuery = PackingList::select(
            DB::raw('YEAR(tanggal_pl) as year'),
            DB::raw('MONTH(tanggal_pl) as month'),
            DB::raw('SUM(discount_charge) as total_disc_charge'),
            DB::raw('SUM(grand_total) as total_grand_total')
        )->whereBetween('tanggal_pl', [$startDate, $endDate]);

        if (!empty($idCab) && $idCab !== 'all') {
            $plQuery->where('id_cab', $idCab);
        }

        $plMonthly = $plQuery
            ->groupBy(DB::raw('YEAR(tanggal_pl)'), DB::raw('MONTH(tanggal_pl)'))
            ->get()
            ->keyBy(function ($item) {
                return sprintf('%04d-%02d', $item->year, $item->month);
            });

        // 2. Agregasi Operasional
        $opsQuery = Operasional::select(
            DB::raw('YEAR(tanggal_trx_ops) as year'),
            DB::raw('MONTH(tanggal_trx_ops) as month'),
            DB::raw('SUM(nominal) as total_ops')
        )->whereBetween('tanggal_trx_ops', [$startDate, $endDate]);

        if (!empty($idCab) && $idCab !== 'all') {
            $opsQuery->where('id_cab', $idCab);
        }

        $opsMonthly = $opsQuery
            ->groupBy(DB::raw('YEAR(tanggal_trx_ops)'), DB::raw('MONTH(tanggal_trx_ops)'))
            ->get()
            ->keyBy(function ($item) {
                return sprintf('%04d-%02d', $item->year, $item->month);
            });

        // 3. Agregasi Komisi Bulanan (jika diikutsertakan)
        $komisiMonthly = collect();
        if ($includeKomisi) {
            $komisiQuery = Komisi::select(
                DB::raw('YEAR(tanggal_tf) as year'),
                DB::raw('MONTH(tanggal_tf) as month'),
                DB::raw('SUM(nominal) as total_komisi')
            )->whereBetween('tanggal_tf', [$startDate, $endDate]);

            if (!empty($idCab) && $idCab !== 'all') {
                $komisiQuery->where('id_cab', $idCab);
            }

            $komisiMonthly = $komisiQuery
                ->groupBy(DB::raw('YEAR(tanggal_tf)'), DB::raw('MONTH(tanggal_tf)'))
                ->get()
                ->keyBy(function ($item) {
                    return sprintf('%04d-%02d', $item->year, $item->month);
                });
        }

        // Gabungkan seluruh bulan unik yang ada
        $allMonths = $plMonthly->keys()
            ->merge($opsMonthly->keys())
            ->merge($komisiMonthly->keys())
            ->unique()
            ->sort()
            ->values();

        $rekaps = [];
        $sumPl = 0.0;
        $sumDisc = 0.0;
        $sumGt = 0.0;
        $sumOps = 0.0;
        $sumKomisi = 0.0;
        $sumLaba = 0.0;

        foreach ($allMonths as $ym) {
            [$year, $month] = explode('-', $ym);
            $discCharge = (float) ($plMonthly->get($ym)->total_disc_charge ?? 0);
            $grandTotal = (float) ($plMonthly->get($ym)->total_grand_total ?? 0);
            $opsVal = (float) ($opsMonthly->get($ym)->total_ops ?? 0);
            $komisiVal = (float) ($komisiMonthly->get($ym)->total_komisi ?? 0);

            // Tentukan pendapatan PL berdasarkan konfigurasi laba_basis
            $plRevenue = ($basis === 'grand_total') ? $grandTotal : $discCharge;

            // Hitung laba bersih: Pendapatan PL - Operasional (+ Komisi jika true)
            $labaBersih = $plRevenue - $opsVal;
            if ($includeKomisi) {
                $labaBersih += $komisiVal;
            }

            $rekaps[] = [
                'year' => (int) $year,
                'month' => (int) $month,
                'bulan_tahun' => self::getIndonesianMonth((int) $month) . ' ' . $year,
                'total_pl' => $plRevenue,
                'total_disc_charge' => $discCharge,
                'total_grand_total' => $grandTotal,
                'total_ops' => $opsVal,
                'total_komisi' => $komisiVal,
                'laba_bersih' => $labaBersih,
                'status' => $labaBersih >= 0 ? 'Surplus' : 'Defisit',
            ];

            $sumPl += $plRevenue;
            $sumDisc += $discCharge;
            $sumGt += $grandTotal;
            $sumOps += $opsVal;
            $sumKomisi += $komisiVal;
            $sumLaba += $labaBersih;
        }

        return [
            'rekaps' => $rekaps,
            'totals' => [
                'total_pl' => $sumPl,
                'total_disc_charge' => $sumDisc,
                'total_grand_total' => $sumGt,
                'total_ops' => $sumOps,
                'total_komisi' => $sumKomisi,
                'laba_bersih' => $sumLaba,
                'status' => $sumLaba >= 0 ? 'Surplus' : 'Defisit',
            ],
            'config' => $cfg,
        ];
    }

    /**
     * Hitung total ringkasan untuk Dashboard
     */
    public function calculateDashboardSummary(?string $idCab = null): array
    {
        $cfg = self::getConfig();
        $basis = $cfg['basis'];
        $includeKomisi = $cfg['include_komisi'];

        $plQuery = PackingList::query();
        $opsQuery = Operasional::query();
        $komisiQuery = Komisi::query();

        if (!empty($idCab) && $idCab !== 'all') {
            $plQuery->where('id_cab', $idCab);
            $opsQuery->where('id_cab', $idCab);
            $komisiQuery->where('id_cab', $idCab);
        }

        $totalDiscCharge = (float) (clone $plQuery)->sum('discount_charge');
        $totalGrandTotal = (float) (clone $plQuery)->sum('grand_total');
        $totalOps = (float) (clone $opsQuery)->sum('nominal');
        $totalKomisi = (float) (clone $komisiQuery)->sum('nominal');

        $plRevenue = ($basis === 'grand_total') ? $totalGrandTotal : $totalDiscCharge;

        $labaBersih = $plRevenue - $totalOps;
        if ($includeKomisi) {
            $labaBersih += $totalKomisi;
        }

        return [
            'total_komisi_pl' => $totalDiscCharge,
            'total_grand_total_pl' => $totalGrandTotal,
            'total_operasional' => $totalOps,
            'total_komisi' => $totalKomisi,
            'laba_bersih' => $labaBersih,
            'total_paket' => (int) (clone $plQuery)->sum('jumlah_paket'),
            'total_trx_pl' => (clone $plQuery)->count(),
            'total_trx_ops' => (clone $opsQuery)->count(),
            'config' => $cfg,
        ];
    }

    /**
     * Hitung ringkasan per cabang untuk Dashboard
     */
    public function calculateRingkasanCabang(): array
    {
        $cfg = self::getConfig();
        $basis = $cfg['basis'];
        $includeKomisi = $cfg['include_komisi'];

        $cabangs = Cabang::orderBy('id_cab')->get();

        return $cabangs->map(function ($cab) use ($basis, $includeKomisi) {
            $discCharge = (float) PackingList::where('id_cab', $cab->id_cab)->sum('discount_charge');
            $grandTotal = (float) PackingList::where('id_cab', $cab->id_cab)->sum('grand_total');
            $opsSum = (float) Operasional::where('id_cab', $cab->id_cab)->sum('nominal');
            $komisiSum = (float) Komisi::where('id_cab', $cab->id_cab)->sum('nominal');

            $plRevenue = ($basis === 'grand_total') ? $grandTotal : $discCharge;

            $labaBersih = $plRevenue - $opsSum;
            if ($includeKomisi) {
                $labaBersih += $komisiSum;
            }

            return [
                'id_cab' => $cab->id_cab,
                'nama_cabang' => $cab->nama_cabang,
                'total_komisi_pl' => $discCharge,
                'total_grand_total_pl' => $grandTotal,
                'total_pl' => $plRevenue,
                'total_ops' => $opsSum,
                'laba_bersih' => $labaBersih,
            ];
        })->toArray();
    }
}
