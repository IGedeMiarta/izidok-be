<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;

class RekamMedis extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    protected $table = 'rekam_medis';
    public $timestamps = true;

    use SoftDeletes;

    protected $dates = ['deleted_at'];
    protected $fillable = array('nomor_rekam_medis');

    public function transKlinik()
    {
        return $this->belongsTo('App\TransKlinik', 'transklinik_id', 'id');
    }

}