<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Role extends Model
{
    protected $table = 'role';

    use SoftDeletes;

    public function user()
    {
        return $this->hasOne(User::class, 'role_id');
    }
}
