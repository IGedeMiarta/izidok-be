<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;

class Klinik extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    protected $table = 'klinik';
    public $timestamps = true;

    use SoftDeletes;

    protected $dates = ['deleted_at'];

    protected $fillable = [
        'tipe_faskes', 'nama_klinik', 'nama_pic', 'nomor_telp', 'nomor_ijin'
    ];

    public function operators()
    {
        return $this->belongsToMany(Operator::class, 'klinik_operator');
    }

    public function dokters()
    {
        return $this->belongsToMany(Dokter::class, 'klinik_dokter');
    }

    public function layanan()
    {
        return $this->hasMany('App\Layanan', 'klinik_id', 'id');
    }

}