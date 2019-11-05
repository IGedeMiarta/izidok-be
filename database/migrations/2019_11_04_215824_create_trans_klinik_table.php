<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateTransKlinikTable extends Migration {

	public function up()
	{
		Schema::create('trans_klinik', function(Blueprint $table) {
			$table->increments('id');
			$table->integer('klinik_dokter_id');
			$table->integer('pasien_id');
			$table->integer('nomor_antrian')->nullable();
			$table->integer('klinik_operator_id');
			$table->integer('klinik_id');
			$table->timestamps();
            $table->softDeletes();
		});
	}

	public function down()
	{
		Schema::drop('trans_klinik');
	}
}