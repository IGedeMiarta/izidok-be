<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreatePembayaranTable extends Migration {

	public function up()
	{
		Schema::create('pembayaran', function(Blueprint $table) {
			$table->increments('id');
			$table->integer('transklinik_id');
			$table->integer('klinik_id');
			$table->string('jaminan', 10);
			$table->float('potongan')->nullable();
			$table->string('status', 50);
			$table->integer('created_by')->nullable();
			$table->string('updated_by', 50)->nullable();
			$table->string('deleted_by', 50)->nullable();
            $table->timestamps();
            $table->softDeletes();
		});
	}

	public function down()
	{
		Schema::drop('pembayaran');
	}
}