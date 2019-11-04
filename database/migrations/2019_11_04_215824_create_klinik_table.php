<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateKlinikTable extends Migration {

	public function up()
	{
		Schema::create('klinik', function(Blueprint $table) {
			$table->increments('id');
			$table->timestamps();
			$table->softDeletes();
			$table->string('nama_pic')->nullable();
			$table->string('nama');
			$table->string('nomor_telp')->nullable();
			$table->integer('tipe');
			$table->string('created_by', 50)->nullable();
			$table->string('updated_by', 50)->nullable();
			$table->string('deleted_by', 50)->nullable();
		});
	}

	public function down()
	{
		Schema::drop('klinik');
	}
}