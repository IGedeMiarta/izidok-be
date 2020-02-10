<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class KlinikOperator extends Model 
{

    protected $table = 'klinik_operator';
    public $timestamps = true;

    use SoftDeletes;

    protected $dates = ['deleted_at'];

    public function klinik()
    {
        return $this->belongsTo('App\Klinik', 'klinik_id', 'id');
    }

    public function operator()
    {
        return $this->belongsTo('App\Operator', 'operator_id', 'id');
    }

}