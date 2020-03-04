<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Billing extends Model
{
    protected $table = 'billing';
    public $timestamps = true;

    use SoftDeletes;

    protected $dates = ['deleted_at'];
}
