<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateRekamMedisTable extends Migration {

	public function up()
	{
		Schema::create('rekam_medis', function(Blueprint $table) {
			$table->increments('id');
			$table->timestamps();
			$table->softDeletes();
			$table->integer('nomor_rekam_medis')->index();
			$table->integer('anamnesa_id')->unsigned();
			$table->integer('pemeriksaan_fisik_id');
			$table->integer('diagnosa_id');
			$table->integer('transklinik_id');
			$table->string('created_by', 50);
			$table->string('updated_by', 50);
			$table->string('deleted_by', 50);
		});
	}

	public function down()
	{
		Schema::drop('rekam_medis');
	}
}