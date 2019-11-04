<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateKodePenyakitTable extends Migration {

	public function up()
	{
		Schema::create('kode_penyakit', function(Blueprint $table) {
			$table->increments('id');
			$table->timestamps();
			$table->softDeletes();
			$table->string('kode', 10)->index();
			$table->string('description', 255);
			$table->string('created_by', 50);
			$table->string('updated_by', 50);
			$table->string('deleted_by', 50);
		});
	}

	public function down()
	{
		Schema::drop('kode_penyakit');
	}
}