<?php

namespace App\Http\Controllers;

use App\Models\PackingList;
use App\Models\Operasional;
use App\Models\Komisi;
use App\Models\BebanOps;
use App\Models\Cabang;
use App\Http\Requests\StorePackingListRequest;
use App\Http\Requests\StoreOperasionalRequest;
use App\Http\Requests\StoreKomisiRequest;
use App\Support\CodeGenerator;
use App\Support\NumberParser;
use Carbon\Carbon;
use Illuminate\Http\Request;

class TransaksiController extends Controller
{
    /**
     * Helper konversi format tanggal dd-mm-yyyy ke Y-m-d
     */
    protected function parseDate($val): string
    {
        try {
            return Carbon::createFromFormat('d-m-Y', trim($val))->format('Y-m-d');
        } catch (\Exception $e) {
            return Carbon::parse($val)->format('Y-m-d');
        }
    }

    // ==========================================
    // 1. TRANSAKSI PACKING LIST
    // ==========================================
    public function packingList()
    {
        $user = auth()->user();
        if ($user && $user->isAdmin()) {
            if (empty($user->id_cab)) {
                abort(403, 'Akun Anda belum terikat ke cabang. Hubungi Owner.');
            }
        }

        $query = PackingList::with('cabang');
        if ($user && $user->isAdmin()) {
            $query->where('id_cab', $user->id_cab);
        }

        $packingLists = $query
            ->orderBy('tanggal_pl', 'desc')
            ->orderBy('kode_trx_pl', 'desc')
            ->get();

        $cabangs = Cabang::orderBy('id_cab')->get();
        $nextCode = CodeGenerator::nextPackingListCode();

        return view('transaksi.packing-list', compact('packingLists', 'nextCode', 'cabangs'));
    }

    public function storePackingList(StorePackingListRequest $request)
    {
        $user = auth()->user();
        if ($user && $user->isAdmin()) {
            if (empty($user->id_cab)) {
                abort(403, 'Akun Anda belum terikat ke cabang. Hubungi Owner.');
            }
            $idCab = $user->id_cab;
        } else {
            $idCab = $request->id_cab;
        }

        $tanggalPl = $this->parseDate($request->tanggal_pl);
        $total = NumberParser::parse($request->total);
        $discountCharge = NumberParser::parse($request->discount_charge);
        $eVoucher = $request->filled('e_voucher') ? NumberParser::parse($request->e_voucher) : 0.0;

        // BAGIAN B.2: grand_total = total - discount_charge - e_voucher dihitung ulang di backend
        $grandTotal = $total - $discountCharge - $eVoucher;

        $kodeTrx = $request->kode_trx_pl ?: CodeGenerator::nextPackingListCode($tanggalPl);
        $createdBy = auth()->id() ?? 'USR-001';

        $noPlJne = $request->no_pl_jne ? strtoupper(trim($request->no_pl_jne)) : null;

        $pl = PackingList::create([
            'kode_trx_pl' => $kodeTrx,
            'no_pl_jne' => $noPlJne,
            'id_cab' => $idCab,
            'tanggal_pl' => $tanggalPl,
            'total' => $total,
            'discount_charge' => $discountCharge,
            'e_voucher' => $eVoucher,
            'grand_total' => $grandTotal,
            'jumlah_paket' => (int) $request->jumlah_paket,
            'ket_trx_pl' => $request->ket_trx_pl,
            'created_by' => $createdBy,
        ]);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Transaksi Packing List berhasil disimpan.',
                'data' => $pl,
            ]);
        }

        return redirect()->route('transaksi.packing-list')->with('success', 'Transaksi Packing List berhasil disimpan.');
    }

    public function updatePackingList(StorePackingListRequest $request, string $kode)
    {
        $pl = PackingList::findOrFail($kode);
        $this->authorize('update', $pl);

        $user = auth()->user();
        if ($user && $user->isAdmin()) {
            if (empty($user->id_cab)) {
                abort(403, 'Akun Anda belum terikat ke cabang. Hubungi Owner.');
            }
            $idCab = $user->id_cab;
        } else {
            $idCab = $request->id_cab;
        }

        $tanggalPl = $this->parseDate($request->tanggal_pl);
        $total = NumberParser::parse($request->total);
        $discountCharge = NumberParser::parse($request->discount_charge);
        $eVoucher = $request->filled('e_voucher') ? NumberParser::parse($request->e_voucher) : 0.0;
        $grandTotal = $total - $discountCharge - $eVoucher;

        $noPlJne = $request->filled('no_pl_jne') ? strtoupper(trim($request->no_pl_jne)) : $pl->no_pl_jne;

        $pl->update([
            'no_pl_jne' => $noPlJne,
            'id_cab' => $idCab,
            'tanggal_pl' => $tanggalPl,
            'total' => $total,
            'discount_charge' => $discountCharge,
            'e_voucher' => $eVoucher,
            'grand_total' => $grandTotal,
            'jumlah_paket' => (int) $request->jumlah_paket,
            'ket_trx_pl' => $request->ket_trx_pl,
        ]);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Transaksi Packing List berhasil diperbarui.',
                'data' => $pl,
            ]);
        }

        return redirect()->route('transaksi.packing-list')->with('success', 'Transaksi Packing List berhasil diperbarui.');
    }

    public function destroyPackingList(Request $request, string $kode)
    {
        $pl = PackingList::findOrFail($kode);
        $this->authorize('delete', $pl);
        $pl->delete();

        $msg = "Transaksi PL {$kode} berhasil dihapus.";
        return $request->wantsJson() ? response()->json(['success' => true, 'message' => $msg]) : back()->with('success', $msg);
    }

    // ==========================================
    // 2. TRANSAKSI OPERASIONAL
    // ==========================================
    public function operasional()
    {
        $operasionals = Operasional::with(['bebanOps', 'cabang'])
            ->orderBy('tanggal_trx_ops', 'desc')
            ->orderBy('kode_trx_ops', 'desc')
            ->get();

        $opsList = BebanOps::orderBy('id_ops')->get()->toArray();
        $cabangs = Cabang::orderBy('id_cab')->get();
        $nextCode = CodeGenerator::nextOperasionalCode();

        return view('transaksi.operasional', compact('operasionals', 'opsList', 'nextCode', 'cabangs'));
    }

    public function storeOperasional(StoreOperasionalRequest $request)
    {
        $tanggalOps = $this->parseDate($request->tanggal_trx_ops);
        $nominal = NumberParser::parse($request->nominal);

        // BAGIAN F.2: BebanOps sengaja enabled; menyimpang dari diagram yang menulis 'disable', demi fleksibilitas input
        $bebanInput = $request->beban_ops ?? $request->beban_ops_id;
        $bebanObj = BebanOps::where('beban_ops', $bebanInput)
            ->orWhere('id_ops', $bebanInput)
            ->first();

        $bebanOpsId = $bebanObj ? $bebanObj->id_ops : (BebanOps::first()->id_ops ?? 'OP-001');
        $kodeTrx = $request->kode_trx_ops ?: CodeGenerator::nextOperasionalCode($tanggalOps);
        $createdBy = auth()->id() ?? 'USR-001';

        $ops = Operasional::create([
            'kode_trx_ops' => $kodeTrx,
            'id_cab' => $request->id_cab,
            'tanggal_trx_ops' => $tanggalOps,
            'beban_ops_id' => $bebanOpsId,
            'nominal' => $nominal,
            'ket_trx_ops' => $request->ket_trx_ops,
            'created_by' => $createdBy,
        ]);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Transaksi Biaya Operasional berhasil disimpan.',
                'data' => $ops,
            ]);
        }

        return redirect()->route('transaksi.operasional')->with('success', 'Transaksi Biaya Operasional berhasil disimpan.');
    }

    public function updateOperasional(StoreOperasionalRequest $request, string $kode)
    {
        $ops = Operasional::findOrFail($kode);
        $this->authorize('update', $ops);

        $tanggalOps = $this->parseDate($request->tanggal_trx_ops);
        $nominal = NumberParser::parse($request->nominal);

        $bebanInput = $request->beban_ops ?? $request->beban_ops_id;
        $bebanObj = BebanOps::where('beban_ops', $bebanInput)
            ->orWhere('id_ops', $bebanInput)
            ->first();

        $bebanOpsId = $bebanObj ? $bebanObj->id_ops : $ops->beban_ops_id;

        $ops->update([
            'id_cab' => $request->id_cab,
            'tanggal_trx_ops' => $tanggalOps,
            'beban_ops_id' => $bebanOpsId,
            'nominal' => $nominal,
            'ket_trx_ops' => $request->ket_trx_ops,
        ]);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Transaksi Biaya Operasional berhasil diperbarui.',
                'data' => $ops,
            ]);
        }

        return redirect()->route('transaksi.operasional')->with('success', 'Transaksi Biaya Operasional berhasil diperbarui.');
    }

    public function destroyOperasional(Request $request, string $kode)
    {
        $ops = Operasional::findOrFail($kode);
        $this->authorize('delete', $ops);
        $ops->delete();

        $msg = "Transaksi operasional {$kode} berhasil dihapus.";
        return $request->wantsJson() ? response()->json(['success' => true, 'message' => $msg]) : back()->with('success', $msg);
    }

    // ==========================================
    // 3. TRANSAKSI KOMISI BULANAN
    // ==========================================
    public function komisi()
    {
        $komisis = Komisi::with('cabang')
            ->orderBy('tanggal_tf', 'desc')
            ->orderBy('kode_trx_cl', 'desc')
            ->get();

        $cabangs = Cabang::orderBy('id_cab')->get();
        $nextCode = CodeGenerator::nextKomisiCode();

        return view('transaksi.komisi', compact('komisis', 'nextCode', 'cabangs'));
    }

    public function storeKomisi(StoreKomisiRequest $request)
    {
        $tanggalTf = $this->parseDate($request->tanggal_tf);
        $nominal = NumberParser::parse($request->nominal);
        $kodeTrx = $request->kode_trx_cl ?: CodeGenerator::nextKomisiCode($tanggalTf);
        $createdBy = auth()->id() ?? 'USR-001';

        $komisi = Komisi::create([
            'kode_trx_cl' => $kodeTrx,
            'id_cab' => $request->id_cab,
            'tanggal_tf' => $tanggalTf,
            'nominal' => $nominal,
            'ket_trx_cl' => $request->ket_trx_cl,
            'created_by' => $createdBy,
        ]);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Transaksi Komisi Bulanan berhasil disimpan.',
                'data' => $komisi,
            ]);
        }

        return redirect()->route('transaksi.komisi')->with('success', 'Transaksi Komisi Bulanan berhasil disimpan.');
    }

    public function updateKomisi(StoreKomisiRequest $request, string $kode)
    {
        $komisi = Komisi::findOrFail($kode);
        $this->authorize('update', $komisi);

        $tanggalTf = $this->parseDate($request->tanggal_tf);
        $nominal = NumberParser::parse($request->nominal);

        $komisi->update([
            'id_cab' => $request->id_cab,
            'tanggal_tf' => $tanggalTf,
            'nominal' => $nominal,
            'ket_trx_cl' => $request->ket_trx_cl,
        ]);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Transaksi Komisi Bulanan berhasil diperbarui.',
                'data' => $komisi,
            ]);
        }

        return redirect()->route('transaksi.komisi')->with('success', 'Transaksi Komisi Bulanan berhasil diperbarui.');
    }

    public function destroyKomisi(Request $request, string $kode)
    {
        $komisi = Komisi::findOrFail($kode);
        $this->authorize('delete', $komisi);
        $komisi->delete();

        $msg = "Transaksi komisi {$kode} berhasil dihapus.";
        return $request->wantsJson() ? response()->json(['success' => true, 'message' => $msg]) : back()->with('success', $msg);
    }
}
