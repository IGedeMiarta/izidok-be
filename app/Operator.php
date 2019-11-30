<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;

class Operator extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    protected $table = 'operator';
    public $timestamps = true;

    use SoftDeletes;

    protected $fillable = [
        'nama', 'user_id'
    ];

    protected $dates = ['deleted_at'];

    public function user()
    {
        return $this->belongsTo('App\User', 'user_id', 'id');
    }

    public function klinik()
    {
        return $this->belongsTo('App\Klinik','klinik_id','id');
    }



}