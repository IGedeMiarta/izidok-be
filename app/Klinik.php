<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Klinik extends Model
{
    protected $table = 'klinik';

    use SoftDeletes; 
    protected $dates =['deleted_at'];

    protected $fillable = [
        'nama_klinik', 'nama_pic', 'nomor_hp'
    ];

    public function operators()
    {
        return $this->belongsToMany(Operator::class, 'klinik_operator');
    }

}
