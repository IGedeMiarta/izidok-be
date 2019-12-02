<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateKlinikTable extends Migration {

	public function up()
	{
		Schema::create('klinik', function(Blueprint $table) {
			$table->increments('id');
			$table->string('nama_pic')->nullable();
			$table->string('nama_klinik');
			$table->string('nomor_telp');
			$table->integer('tipe_faskes');
			$table->string('nomor_ijin', 50)->nullable();
			$table->integer('created_by')->nullable();
			$table->string('updated_by', 50)->nullable();
			$table->string('deleted_by', 50)->nullable();
			$table->timestamps();
            $table->softDeletes();
		});
	}

	public function down()
	{
		Schema::drop('klinik');
	}
}