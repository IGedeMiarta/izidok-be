<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ApiKey extends Model 
{

    protected $table = 'api_key';
    public $timestamps = true;

    use SoftDeletes;

    protected $dates = ['deleted_at'];
    protected $fillable = array('user_id', 'api_key', 'logout_at', 'expired_at');

    public function user()
    {
        return $this->belongsTo('App\User', 'user_id', 'id');
    }
}