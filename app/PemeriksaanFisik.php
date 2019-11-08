<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;

class PemeriksaanFisik extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    protected $table = 'pemeriksaan_fisik';
    public $timestamps = true;

    use SoftDeletes;

    protected $dates = ['deleted_at'];
    protected $fillable = array('notes');

}