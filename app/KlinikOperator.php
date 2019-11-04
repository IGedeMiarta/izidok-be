<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class KlinikOperator extends Model
{
    protected $table = 'klinik_operator';

    protected $fillable = [
        'klinik_id', 'operator_id'
    ];
}
