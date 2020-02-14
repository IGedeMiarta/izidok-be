<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Provinsi extends Model
{
    protected $table = 'provinsi';
    public $timestamps = false;

    use SoftDeletes;

    protected $dates = ['deleted_at'];
}
