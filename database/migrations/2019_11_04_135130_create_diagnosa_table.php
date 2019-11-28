<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateDiagnosaTable extends Migration {

	public function up()
	{
		Schema::create('diagnosa', function(Blueprint $table) {
			$table->increments('id');
			$table->string('kode_penyakit_id', 500);
			$table->text('notes')->nullable();
			$table->boolean('is_draw')->nullable();
			$table->string('draw_path')->nullable();
			$table->string('created_by', 50)->nullable();
			$table->string('updated_by', 50)->nullable();
			$table->string('deleted_by', 50)->nullable();
			$table->timestamps();
			$table->softDeletes();
		});
	}

	public function down()
	{
		Schema::drop('diagnosa');
	}
}