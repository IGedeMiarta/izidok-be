<?php

namespace App\Policies;

use App\Constant;
use App\Pasien;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class PasienPolicy
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

    public function updateOrDelete(User $user, Pasien $pasien)
    {
        return $user->klinik_id === $pasien->klinik_id;
    }
}
