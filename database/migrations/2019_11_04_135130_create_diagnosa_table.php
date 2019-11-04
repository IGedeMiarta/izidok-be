<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateDiagnosaTable extends Migration {

	public function up()
	{
		Schema::create('diagnosa', function(Blueprint $table) {
			$table->increments('id');
			$table->integer('kode_penyakit_id');
			$table->text('notes');
			$table->string('created_by', 50)->nullable();
			$table->string('updated_by', 50)->nullable();
			$table->string('deleted_by', 50)->nullable();
			$table->timestamps();
			$table->softDeletes();
		});
	}

	public function down()
	{
		Schema::drop('diagnosa');
	}
}