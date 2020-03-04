<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PaygateTutorial extends Model
{
    protected $table = 'paygate_tutorial';
    public $timestamps = true;

    use SoftDeletes;

    protected $dates = ['deleted_at'];
}
