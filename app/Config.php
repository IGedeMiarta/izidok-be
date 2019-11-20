<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Config extends Model 
{

    protected $table = 'config';
    public $timestamps = true;

    use SoftDeletes;

    protected $dates = ['deleted_at'];

}