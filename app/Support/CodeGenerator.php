<?php

namespace App\Support;

use App\Models\Status;
use App\Models\User;
use App\Models\Cabang;
use App\Models\BebanOps;
use App\Models\PackingList;
use App\Models\Operasional;
use App\Models\Komisi;
use Carbon\Carbon;

class CodeGenerator
{
    public static function nextUserId(): string
    {
        $last = User::orderByRaw('CAST(SUBSTRING(id_user, 5) AS UNSIGNED) DESC')->first();
        $num = $last ? (int) substr($last->id_user, 4) + 1 : 1;
        return sprintf('USR-%03d', $num);
    }

    public static function nextCabangId(): string
    {
        $last = Cabang::orderByRaw('CAST(SUBSTRING(id_cab, 4) AS UNSIGNED) DESC')->first();
        $num = $last ? (int) substr($last->id_cab, 3) + 1 : 1;
        return sprintf('CB-%03d', $num);
    }

    public static function nextOpsId(): string
    {
        $last = BebanOps::orderByRaw('CAST(SUBSTRING(id_ops, 4) AS UNSIGNED) DESC')->first();
        $num = $last ? (int) substr($last->id_ops, 3) + 1 : 1;
        return sprintf('OP-%03d', $num);
    }

    public static function nextStatusId(): string
    {
        $last = Status::orderByRaw('CAST(SUBSTRING(id_status, 4) AS UNSIGNED) DESC')->first();
        $num = $last ? (int) substr($last->id_status, 3) + 1 : 1;
        return sprintf('ST-%03d', $num);
    }

    public static function nextPackingListCode(?string $date = null): string
    {
        $dateStr = $date ? Carbon::parse($date)->format('Ymd') : Carbon::now()->format('Ymd');
        $prefix = "PL-{$dateStr}-";
        $last = PackingList::where('kode_trx_pl', 'like', "{$prefix}%")
            ->orderByRaw('CAST(SUBSTRING(kode_trx_pl, 13) AS UNSIGNED) DESC')
            ->first();

        $num = $last ? (int) substr($last->kode_trx_pl, 12) + 1 : 1;
        return sprintf('%s%03d', $prefix, $num);
    }

    public static function nextOperasionalCode(?string $date = null): string
    {
        $dateStr = $date ? Carbon::parse($date)->format('Ymd') : Carbon::now()->format('Ymd');
        $prefix = "OPS-{$dateStr}-";
        $last = Operasional::where('kode_trx_ops', 'like', "{$prefix}%")
            ->orderByRaw('CAST(SUBSTRING(kode_trx_ops, 14) AS UNSIGNED) DESC')
            ->first();

        $num = $last ? (int) substr($last->kode_trx_ops, 13) + 1 : 1;
        return sprintf('%s%03d', $prefix, $num);
    }

    public static function nextKomisiCode(?string $date = null): string
    {
        $dateStr = $date ? Carbon::parse($date)->format('Ymd') : Carbon::now()->format('Ymd');
        $prefix = "CL-{$dateStr}-";
        $last = Komisi::where('kode_trx_cl', 'like', "{$prefix}%")
            ->orderByRaw('CAST(SUBSTRING(kode_trx_cl, 13) AS UNSIGNED) DESC')
            ->first();

        $num = $last ? (int) substr($last->kode_trx_cl, 12) + 1 : 1;
        return sprintf('%s%03d', $prefix, $num);
    }
}
