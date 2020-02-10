<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateActivationTable extends Migration {

	public function up()
	{
		Schema::create('activation', function(Blueprint $table) {
			$table->increments('id');
			$table->integer('user_id');
			$table->integer('status')->default('0');
			$table->timestamp('expired_at');
			$table->string('token', 255);
			$table->timestamps();
			$table->softDeletes();
		});
	}

	public function down()
	{
		Schema::drop('activation');
	}
}