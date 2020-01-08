<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Pembayaran extends Model 
{

    protected $table = 'pembayaran';
    public $timestamps = true;

    use SoftDeletes;

    protected $dates = ['deleted_at'];

    protected static function boot() {
        parent::boot();
    
        static::deleted(function ($pembayaran) {
          $pembayaran->detail()->delete();
        });
      }

    public function detail()
    {
        return $this->hasMany(DetailPembayaran::class, 'pembayaran_id');
    }
}