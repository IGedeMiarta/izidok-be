<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class KlinikDokter extends Model 
{

    protected $table = 'klinik_dokter';
    public $timestamps = true;

    use SoftDeletes;

    protected $dates = ['deleted_at'];

    public function klinik()
    {
        return $this->belongsTo('App\Klinik', 'klinik_id', 'id');
    }

    public function dokter()
    {
        return $this->belongsTo('App\Dokter', 'dokter_id', 'id');
    }

    public function transKlinik()
    {
        return $this->hasMany('App\TransKlinik', 'klinik_dokter_id', 'id');
    }

}