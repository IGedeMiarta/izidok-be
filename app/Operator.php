<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Operator extends Model 
{

    protected $table = 'operator';
    public $timestamps = true;

    use SoftDeletes;

    protected $fillable = [
        'nama', 'user_id'
    ];

    protected $dates = ['deleted_at'];

    public function kliniks()
    {
        return $this->belongsToMany(Klinik::class, 'klinik_operator');
    }


    public function user()
    {
        return $this->belongsTo('App\User', 'user_id', 'id');
    }

}