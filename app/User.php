<?php

namespace App;

use Illuminate\Auth\Authenticatable;
use Laravel\Lumen\Auth\Authorizable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;

class User extends Model implements AuthenticatableContract, AuthorizableContract, Auditable
{
    use Authenticatable, Authorizable;
    use SoftDeletes;
    use \OwenIt\Auditing\Auditable;

    protected $table = 'users';

    protected $fillable = [
        'email', 'password','username','api_token','nama','nomor_telp'
    ];

    protected $hidden = [
        'password', 'api_token'
    ];

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'user_role');
    }

    public function operator()
    {
        return $this->hasOne('Operator');
    }

    public function activation()
    {
        return $this->hasOne(Activation::class);
    }
}
