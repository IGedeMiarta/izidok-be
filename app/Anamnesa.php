<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;

class Anamnesa extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    protected $table = 'anamnesa';
    public $timestamps = true;

    use SoftDeletes;

    protected $dates = ['deleted_at'];
    protected $fillable = array('tensi_sistole', 'tensi_diastole', 'nadi', 'suhu', 'respirasi', 'tinggi_badan', 'berat_badan', 'notes');

}