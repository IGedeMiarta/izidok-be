<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Kota extends Model
{
    protected $table = 'kota';
    public $timestamps = true;

    use SoftDeletes;

    protected $dates = ['deleted_at'];
}