<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateTransKlinikTable extends Migration {

	public function up()
	{
		Schema::create('trans_klinik', function(Blueprint $table) {
			$table->increments('id');
			$table->integer('examination_by');
			$table->timestamp('examination_at')->nullable();
			$table->integer('pasien_id');
			$table->integer('klinik_id');
			$table->integer('nomor_antrian')->nullable();
			$table->timestamp('waktu_konsultasi')->nullable();
			$table->integer('durasi_konsultasi')->nullable();
			$table->string('status');
			$table->integer('created_by');
			$table->string('updated_by', 50)->nullable();
			$table->string('deleted_by', 50)->nullable();
			$table->timestamps();
            $table->softDeletes();
		});
	}

	public function down()
	{
		Schema::drop('trans_klinik');
	}
}