<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;

class TransKlinik extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    protected $table = 'trans_klinik';
    public $timestamps = true;

    use SoftDeletes;

    protected $dates = ['deleted_at'];

    public function klinikDokter()
    {
        return $this->belongsTo('App\KlinikDokter', 'klinik_dokter_id', 'id');
    }

    public function pasien()
    {
        return $this->belongsTo('App\Pasien', 'pasien_id', 'id');
    }

    public function klinik()
    {
        return $this->belongsTo('App\Klinik', 'klinik_id', 'id');
    }

    public function rekamMedis()
    {
        return $this->hasMany('App\RekamMedis', 'transklinik_id', 'id');
    }

}