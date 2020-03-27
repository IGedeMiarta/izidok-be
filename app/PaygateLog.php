<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PaygateLog extends Model
{
    protected $table = 'paygate_log';
    public $timestamps = true;

    use SoftDeletes;

    protected $fillable = [
		'channelId','serviceCode','currency','transactionNo','transactionAmount',
		'transactionDate','transactionExpire','description','customerAccount',
		'customerName','authCode','rc','created_by','insertId'
	];
    protected $dates = ['deleted_at'];
}
