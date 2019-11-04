<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreatePemeriksaanFisikTable extends Migration {

	public function up()
	{
		Schema::create('pemeriksaan_fisik', function(Blueprint $table) {
			$table->increments('id');
			$table->timestamps();
			$table->softDeletes();
			$table->integer('organ_id');
			$table->text('notes');
			$table->string('created_by', 50);
			$table->string('updated_by', 50);
			$table->string('deleted_by', 50);
		});
	}

	public function down()
	{
		Schema::drop('pemeriksaan_fisik');
	}
}