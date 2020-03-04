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
        'nama_pic', 'nama_klinik', 'nomor_telp', 'tipe_faskes', 'nomor_ijin', 'spesialisasi_id'
    ];

    public function user()
    {
        return $this->hasOne(User::class, 'klinik_id');
    }

    public function dokters()
    {
        return $this->belongsToMany(Dokter::class, 'klinik_dokter');
    }

    public function spesialisasi()
    {
        return $this->belongsTo(Spesialisasi::class, 'spesialisasi_id');
    }

    public function layanan()
    {
        return $this->hasMany('App\Layanan', 'klinik_id', 'id');
    }

    public function operator()
    {
        return $this->hasMany('App\Operator','klinik_id','id');
    }

}
