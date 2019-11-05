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

    protected $fillable = [
        'tipe_klinik', 'nama_klinik', 'nama_pic', 'nomor_telp'
    ];

    public function operators()
    {
        return $this->belongsToMany(Operator::class, 'klinik_operator');
    }

    public function dokters()
    {
        return $this->belongsToMany(Dokter::class, 'klinik_dokter');
    }

}