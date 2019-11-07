<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Activation extends Model 
{

    protected $table = 'activation';
    public $timestamps = true;

    use SoftDeletes;

    protected $dates = ['deleted_at'];
    protected $fillable = array('user_id', 'status', 'expired_at', 'token');

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}