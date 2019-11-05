<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateKlinikOperatorTable extends Migration {

	public function up()
	{
		Schema::create('klinik_operator', function(Blueprint $table) {
			$table->increments('id');
			$table->integer('operator_id');
			$table->integer('klinik_id');
			$table->string('created_by', 50)->nullable();
			$table->string('updated_by', 50)->nullable();
			$table->string('deleted_by', 50)->nullable();
			$table->timestamps();
            $table->softDeletes();
		});
	}

	public function down()
	{
		Schema::drop('klinik_operator');
	}
}