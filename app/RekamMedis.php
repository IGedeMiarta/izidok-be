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

    public function pasien()
    {
        return $this->belongsTo('App\Pasien', 'nomor_rekam_medis', 'nomor_rekam_medis');
    }

    public function anamnesa()
    {
        return $this->belongsTo('App\Anamnesa', 'anamnesa_id', 'id');
    }

    public function pemeriksaan_fisik()
    {
        return $this->belongsTo('App\PemeriksaanFisik', 'pemeriksaan_fisik_id', 'id');
    }

    public function diagnosa()
    {
        return $this->belongsTo('App\Diagnosa', 'diagnosa_id', 'id');
    }

    public function pemeriksaan_penunjang()
    {
        return $this->belongsTo('App\PemeriksaanPenunjang', 'pemeriksaan_penunjang_id', 'id');
    }

}