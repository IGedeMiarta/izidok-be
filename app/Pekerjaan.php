<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class Pekerjaan extends Model
{
	protected $table = 'pekerjaan';
	public $timestamps = true;

	use SoftDeletes;

    protected $dates = ['deleted_at'];

    //
}
