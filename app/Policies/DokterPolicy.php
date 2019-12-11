<?php

namespace App\Policies;

use App\Dokter;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class DokterPolicy
{
    use HandlesAuthorization;

    /**
     * Create a new policy instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    public function before($user) {
        if ($user->hasRole(Constant::SUPER_ADMIN)) {
            return true;
        }
    }

    public function updateOrDelete(User $user, Dokter $dokter)
    {
        return $user->id === $dokter->created_by;
    }
}
