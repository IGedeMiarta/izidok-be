<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Operator extends Model
{
    protected $table = 'operator';

    protected $fillable = [
        'nama'
    ];

    public function kliniks()
    {
        return $this->belongsToMany(Klinik::class, 'klinik_operator');
    }

    public function user()
    {
        return $this->belongsTo('User');
    }
}
