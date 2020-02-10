<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;

class Pasien extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    protected $table = 'pasien';
    public $timestamps = true;

    use SoftDeletes;

    protected $dates = ['deleted_at'];

    public function user()
    {
        return $this->belongsTo('App\User', 'user_id', 'id');
    }

    public function transKlinik()
    {
        return $this->hasMany('App\TransKlinik', 'pasien_id', 'id');
    }

    public function rekamMedis()
    {
        return $this->hasOne('App\RekamMedis', 'nomor_rekam_medis', 'nomor_rekam_medis');
    }

}