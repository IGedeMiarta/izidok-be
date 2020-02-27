<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PaygateLog extends Model
{
    protected $table = 'paygate_log';
    public $timestamps = true;

    use SoftDeletes;

    protected $dates = ['deleted_at'];
}
