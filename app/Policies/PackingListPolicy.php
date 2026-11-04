<?php

namespace App\Policies;

use App\Models\User;
use App\Models\PackingList;
use Illuminate\Auth\Access\Response;
use Carbon\Carbon;

class PackingListPolicy
{
    /**
     * Boleh melihat list transaksi
     */
    public function viewAny(User $user): Response
    {
        return Response::allow();
    }

    /**
     * Boleh mengedit packing list
     */
    public function update(User $user, PackingList $pl): Response
    {
        if ($user->isOwner()) {
            return Response::allow();
        }

        if ($user->isAdmin()) {
            // Cek apakah cabang transaksi sesuai dengan cabang admin
            if ($pl->id_cab !== $user->id_cab) {
                return Response::deny('Anda tidak berwenang melakukan aksi ini.');
            }

            // Cek apakah dibuat hari ini di timezone Asia/Jakarta
            $createdAt = $pl->created_at ? $pl->created_at->copy()->timezone('Asia/Jakarta') : Carbon::now('Asia/Jakarta');
            $today = Carbon::now('Asia/Jakarta');

            if ($createdAt->toDateString() !== $today->toDateString()) {
                return Response::deny('Packing List hanya dapat diubah pada hari yang sama saat dibuat. Hubungi Owner.');
            }

            return Response::allow();
        }

        return Response::deny('Anda tidak berwenang melakukan aksi ini.');
    }

    /**
     * Boleh menghapus packing list
     */
    public function delete(User $user, PackingList $pl): Response
    {
        if ($user->isOwner()) {
            return Response::allow();
        }

        return Response::deny('Anda tidak berwenang melakukan aksi ini.');
    }
}
