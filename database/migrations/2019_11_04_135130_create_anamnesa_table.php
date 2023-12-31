<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateAnamnesaTable extends Migration {

	public function up()
	{
		Schema::create('anamnesa', function(Blueprint $table) {
			$table->increments('id');
			$table->integer('tensi_sistole')->nullable();
			$table->integer('tensi_diastole')->nullable();
			$table->integer('nadi')->nullable();
			$table->integer('suhu')->nullable();
			$table->integer('respirasi')->nullable();
			$table->integer('tinggi_badan')->nullable();
			$table->integer('berat_badan')->nullable();
			$table->text('notes')->nullable();
			$table->boolean('is_draw')->nullable();
			$table->string('draw_path')->nullable();
			$table->integer('created_by');
			$table->string('updated_by', 50)->nullable();
			$table->string('deleted_by', 50)->nullable();
			$table->timestamps();
			$table->softDeletes();
		});
	}

	public function down()
	{
		Schema::drop('anamnesa');
	}
}