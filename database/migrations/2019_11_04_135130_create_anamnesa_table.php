<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateAnamnesaTable extends Migration {

	public function up()
	{
		Schema::create('anamnesa', function(Blueprint $table) {
			$table->increments('id');
			$table->timestamps();
			$table->softDeletes();
			$table->integer('tensi');
			$table->integer('nadi');
			$table->integer('suhu');
			$table->integer('respirasi');
			$table->integer('tinggi_badan');
			$table->integer('berat_badan');
			$table->text('notes');
			$table->string('created_by', 50);
			$table->string('updated_by', 50);
			$table->string('deleted_by', 50);
		});
	}

	public function down()
	{
		Schema::drop('anamnesa');
	}
}