<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Operasional;
use Illuminate\Auth\Access\Response;

class OperasionalPolicy
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

    public function update(User $user, Operasional $operasional): Response
    {
        return $user->isOwner()
            ? Response::allow()
            : Response::deny('Anda tidak berwenang melakukan aksi ini.');
    }

    public function delete(User $user, Operasional $operasional): Response
    {
        return $user->isOwner()
            ? Response::allow()
            : Response::deny('Anda tidak berwenang melakukan aksi ini.');
    }
}
