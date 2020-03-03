<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PayflagLog extends Model
{
    protected $table = 'payflag_log';

    public $timestamps = false;
    
    protected $dates = ['created_at'];

    protected $fillable = [
		'channelId','currency','transactionNo','transactionAmount',
		'transactionDate','channelType','transactionStatus','transactionMessage',
		'customerAccount','flagType','insertId','authCode','paymentStatus','paymentMessage'
	];
}

/*
CREATE TABLE `payflag_log` (
	`id` BIGINT unsigned NOT NULL AUTO_INCREMENT,
	`channelId` VARCHAR(24),
	`currency` VARCHAR(3),
	`transactionNo` VARCHAR(18),
	`transactionAmount` DECIMAL(10),
	`transactionDate` DATETIME NULL,
	`channelType` VARCHAR(5),
	`transactionStatus` VARCHAR(2),
	`transactionMessage` VARCHAR(200),
	`customerAccount` VARCHAR(50),
	`flagType` VARCHAR(2),
	`insertId` BIGINT(11),
	`authCode` VARCHAR(64),
	`paymentStatus` VARCHAR(2),
	`paymentMessage` VARCHAR(200),
	`created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
	PRIMARY KEY (`id`)
) ENGINE=InnoDB;
*/