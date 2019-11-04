<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Klinik extends Model 
{

    protected $table = 'klinik';
    public $timestamps = true;

    use SoftDeletes;

    protected $dates = ['deleted_at'];

    public function klinikDokter()
    {
        return $this->hasMany('App\KlinikDokter', 'klinik_id', 'id');
    }

    public function klinikOperator()
    {
        return $this->hasMany('App\KlinikOperator', 'klinik_id', 'id');
    }

    public function transKlinik()
    {
        return $this->hasMany('App\TransKlinik', 'klinik_id', 'id');
    }

}