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
        'email', 
        'password', 
        'username', 
        'api_token', 
        'nama', 
        'nomor_telp', 
        'is_first_login',
        'klinik_id',
        'role_id'
    ];

    protected $hidden = [
        'password', 'api_token'
    ];

    public function role()
    {
        return $this->belongsTo(Role::class, 'role_id');
    }

    public function klinik()
    {
        return $this->belongsTo(Klinik::class, 'klinik_id');
    }

    public function operator()
    {
        return $this->hasMany(Operator::class, 'user_id');
    }

    public function activation()
    {
        return $this->hasOne(Activation::class);
    }

    public function layanan()
    {
        return $this->hasMany(Layanan::class, 'created_by');
    }
}
