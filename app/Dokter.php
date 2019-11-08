<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;

class Dokter extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;

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