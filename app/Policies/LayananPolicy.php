<?php

namespace App\Policies;

use App\Constant;
use App\Layanan;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class LayananPolicy
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

    public function updateOrDelete(User $user, Layanan $layanan)
    {
        return $user->klinik_id === $layanan->klinik_id;
    }

}
