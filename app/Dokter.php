<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Dokter extends Model 
{

    protected $table = 'dokter';
    public $timestamps = true;

    use SoftDeletes;

    protected $dates = ['deleted_at'];

    public function klinikDokter()
    {
        return $this->hasMany('App\KlinikDokter', 'dokter_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo('App\Dokter', 'user_id', 'id');
    }

}