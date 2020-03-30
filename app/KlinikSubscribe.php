<?php

namespace App;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class KlinikSubscribe extends Model
{
    protected $table = 'klinik_subscribe';
    public $timestamps = true;

    use SoftDeletes;

    protected $dates = ['deleted_at'];
}
