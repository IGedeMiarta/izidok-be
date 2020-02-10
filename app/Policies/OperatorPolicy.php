<?php

namespace App\Policies;

use App\Constant;
use App\Operator;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class OperatorPolicy
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

    public function updateOrDelete(User $user, Operator $operator)
    {
        return $user->id === $operator->created_by;
    }
}
