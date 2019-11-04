<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Anamnesa extends Model 
{

    protected $table = 'anamnesa';
    public $timestamps = true;

    use SoftDeletes;

    protected $dates = ['deleted_at'];
    protected $fillable = array('tensi', 'nadi', 'suhu', 'respirasi', 'tinggi_badan', 'berat_badan', 'notes');

}