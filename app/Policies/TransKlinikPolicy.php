<?php

namespace App\Policies;

use App\Constant;
use App\TransKlinik;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class TransKlinikPolicy
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

    public function updateOrDelete(User $user, TransKlinik $transklinik)
    {
        return $user->klinik_id === $transklinik->klinik_id;
    }
}
