<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Komisi;
use Illuminate\Auth\Access\Response;

class KomisiPolicy
{
    public function viewAny(User $user): Response
    {
        return $user->isOwner()
            ? Response::allow()
            : Response::deny('Anda tidak berwenang melakukan aksi ini.');
    }

    public function create(User $user): Response
    {
        return $user->isOwner()
            ? Response::allow()
            : Response::deny('Anda tidak berwenang melakukan aksi ini.');
    }

    public function update(User $user, Komisi $komisi): Response
    {
        return $user->isOwner()
            ? Response::allow()
            : Response::deny('Anda tidak berwenang melakukan aksi ini.');
    }

    public function delete(User $user, Komisi $komisi): Response
    {
        return $user->isOwner()
            ? Response::allow()
            : Response::deny('Anda tidak berwenang melakukan aksi ini.');
    }
}
