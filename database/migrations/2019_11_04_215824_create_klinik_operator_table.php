<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateKlinikOperatorTable extends Migration {

	public function up()
	{
		Schema::create('klinik_operator', function(Blueprint $table) {
			$table->increments('id');
			$table->timestamps();
			$table->softDeletes();
			$table->integer('operator_id');
			$table->integer('klinik_id');
			$table->string('created_by', 50)->nullable();
			$table->string('updated_by', 50)->nullable();
			$table->string('deleted_by', 50)->nullable();
		});
	}

	public function down()
	{
		Schema::drop('klinik_operator');
	}
}