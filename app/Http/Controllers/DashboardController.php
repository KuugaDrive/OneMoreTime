<?php

namespace App\Http\Controllers;

use App\Models\PackingList;
use App\Models\Operasional;
use App\Models\Cabang;
use App\Services\LabaBersihService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Halaman Dashboard Ringkasan (Khusus Owner)
     */
    public function index(Request $request)
    {
        $idCab = $request->id_cab;
        $cabangs = Cabang::orderBy('id_cab')->get();

        $service = new LabaBersihService();
        $summary = $service->calculateDashboardSummary($idCab);
        $ringkasanCabang = $service->calculateRingkasanCabang();

        $plQuery = PackingList::query();
        $opsQuery = Operasional::query();

        if (!empty($idCab) && $idCab !== 'all') {
            $plQuery->where('id_cab', $idCab);
            $opsQuery->where('id_cab', $idCab);
        }

        $recentPL = (clone $plQuery)->with('cabang')
            ->orderBy('tanggal_pl', 'desc')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get()
            ->map(function ($item) {
                return [
                    'kode_trx_pl' => $item->kode_trx_pl,
                    'no_pl_jne' => $item->no_pl_jne,
                    'nama_cabang' => $item->cabang ? $item->cabang->nama_cabang : '-',
                    'tanggal_pl' => Carbon::parse($item->tanggal_pl)->format('d-m-Y'),
                    'jumlah_paket' => $item->jumlah_paket,
                    'discount_charge' => (float) $item->discount_charge,
                    'grand_total' => (float) $item->grand_total,
                ];
            })
            ->toArray();

        $recentOps = (clone $opsQuery)->with(['bebanOps', 'cabang'])
            ->orderBy('tanggal_trx_ops', 'desc')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get()
            ->map(function ($item) {
                return [
                    'kode_trx_ops' => $item->kode_trx_ops,
                    'nama_cabang' => $item->cabang ? $item->cabang->nama_cabang : '-',
                    'tanggal_trx_ops' => Carbon::parse($item->tanggal_trx_ops)->format('d-m-Y'),
                    'beban_ops' => $item->bebanOps ? $item->bebanOps->beban_ops : '-',
                    'nominal' => (float) $item->nominal,
                ];
            })
            ->toArray();

        return view('dashboard', compact('summary', 'recentPL', 'recentOps', 'cabangs', 'idCab', 'ringkasanCabang'));
    }
}
