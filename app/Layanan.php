<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Layanan extends Model
{
    protected $table = 'layanan';
    use SoftDeletes;
    protected $dates = ['deleted_at'];
    public $timestamps = true;

    public function klinik()
    {
        return $this->belongsTo('App\Klinik', 'klinik_id', 'id');
    }
}
