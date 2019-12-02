<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateDokterTable extends Migration {

	public function up()
	{
		Schema::create('dokter', function(Blueprint $table) {
			$table->increments('id');
			$table->string('nama');
			$table->integer('user_id');
			$table->integer('created_by');
			$table->string('updated_by', 50)->nullable();
			$table->string('deleted_by', 50)->nullable();
			$table->timestamps();
            $table->softDeletes();
		});
	}

	public function down()
	{
		Schema::drop('dokter');
	}
}