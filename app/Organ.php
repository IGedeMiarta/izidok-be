<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class Organ extends Model
{
	protected $table = 'pasien';
	public $timestamps = true;

	use SoftDeletes;

    protected $dates = ['deleted_at'];

    //
}
