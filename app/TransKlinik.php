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
    protected $fillable = ['reminder'];

    public function klinikDokter()
    {
        return $this->belongsTo('App\KlinikDokter', 'klinik_dokter_id', 'id');
    }

    public function klinikOperator()
    {
        return $this->belongsTo('App\KlinikOperator', 'klinik_operator_id', 'id');
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

    public function examinationBy()
    {
        return $this->belongsTo('App\User', 'examination_by', 'id');
    }

    public function scopeWithAndWhereHas($query, $relation, $constraint){
        return $query->whereHas($relation, $constraint)
                     ->with([$relation => $constraint]);
    }
}
