<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;
// use App\Izicrypt\IzicryptModelTrait;

class Pasien extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    // use IzicryptModelTrait;

    protected $table = 'pasien';
    public $timestamps = true;

    // public $encrypted = ['nama', 'nik', 'rw'];
    // public $encrypted_except = true;

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

    public function provinsi()
    {
        return $this->hasOne('App\Provinsi', 'id', 'provinsi');
    }

    public function kota()
    {
        return $this->hasOne('App\Kota', 'id', 'kota');
    }

}