<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Paygate extends Model
{
    protected $table = 'paygate';
    public $timestamps = true;

    use SoftDeletes;

    protected $dates = ['deleted_at'];
}
