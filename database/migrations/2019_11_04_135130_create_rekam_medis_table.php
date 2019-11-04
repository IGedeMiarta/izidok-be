<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateRekamMedisTable extends Migration {

	public function up()
	{
		Schema::create('rekam_medis', function(Blueprint $table) {
			$table->increments('id');
			$table->integer('nomor_rekam_medis')->index();
			$table->integer('anamnesa_id');
			$table->integer('pemeriksaan_fisik_id');
			$table->integer('diagnosa_id');
			$table->integer('transklinik_id');
			$table->string('created_by', 50)->nullable();
			$table->string('updated_by', 50)->nullable();
			$table->string('deleted_by', 50)->nullable();
			$table->timestamps();
			$table->softDeletes();
		});
	}

	public function down()
	{
		Schema::drop('rekam_medis');
	}
}