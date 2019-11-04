<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateDokterTable extends Migration {

	public function up()
	{
		Schema::create('dokter', function(Blueprint $table) {
			$table->increments('id');
			$table->timestamps();
			$table->softDeletes();
			$table->string('nama');
			$table->integer('user_id');
			$table->string('created_by', 50)->nullable();
			$table->string('updated_by', 50)->nullable();
			$table->string('deleted_by', 50)->nullable();
		});
	}

	public function down()
	{
		Schema::drop('dokter');
	}
}