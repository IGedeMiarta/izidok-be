<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Role extends Model
{
    protected $table = 'role';

    use SoftDeletes;

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_role');
    }
}
