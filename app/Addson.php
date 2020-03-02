<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Addson extends Model
{
    protected $table = 'addson';
    public $timestamps = true;

    use SoftDeletes;

    protected $dates = ['deleted_at'];
}
