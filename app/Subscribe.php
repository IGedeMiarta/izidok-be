<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Subscribe extends Model
{
    protected $table = 'klinik_subscribe';
    public $timestamps = true;

    use SoftDeletes;

    protected $dates = ['deleted_at'];
}
