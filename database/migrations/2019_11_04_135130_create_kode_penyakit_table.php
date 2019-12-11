<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateKodePenyakitTable extends Migration {

	public function up()
	{
		Schema::create('kode_penyakit', function(Blueprint $table) {
			$table->increments('id');
			$table->string('kode', 10)->index();
			$table->string('description', 255);
			$table->string('icd', 10);
			$table->string('edc_code', 10);
			$table->string('icd_code', 10);
			$table->integer('created_by')->nullable();
			$table->string('updated_by', 50)->nullable();
			$table->string('deleted_by', 50)->nullable();
			$table->timestamps();
			$table->softDeletes();
		});
	}

	public function down()
	{
		Schema::drop('kode_penyakit');
	}
}