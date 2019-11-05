<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Dokter extends Model 
{

    protected $table = 'dokter';
    public $timestamps = true;

    use SoftDeletes;

    protected $fillable = [
        'nama', 'user_id'
    ];

    protected $dates = ['deleted_at'];

    public function kliniks()
    {
        return $this->belongsToMany(Klinik::class, 'klinik_dokter');
    }

    public function user()
    {
        return $this->belongsTo('App\Dokter', 'user_id', 'id');
    }

    

}