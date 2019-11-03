<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Klinik extends Model
{
    protected $table = 'klinik';

    use SoftDeletes; 
    protected $dates =['deleted_at'];
}
