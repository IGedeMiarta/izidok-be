<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ForgotPassword extends Model 
{

    protected $table = 'forgot_password';
    public $timestamps = true;

    use SoftDeletes;

    protected $dates = ['deleted_at'];

    public function user()
    {
        return $this->belongsTo('App\User', 'user_id', 'id');
    }
}