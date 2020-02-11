<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Spesialisasi extends Model
{
    protected $table = 'spesialisasi';
    public $timestamps = true;

    use SoftDeletes;

    protected $dates = ['deleted_at'];
}
