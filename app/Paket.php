<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Paket extends Model
{
    protected $table = 'paket';
    public $timestamps = true;

    use SoftDeletes;

    protected $dates = ['deleted_at'];
}
