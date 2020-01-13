<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreatePemeriksaanPenunjangTable extends Migration {

	public function up()
	{
		Schema::create('pemeriksaan_penunjang', function(Blueprint $table) {
			$table->increments('id');
			$table->text('notes')->nullable();
			$table->boolean('is_draw')->nullable();
			$table->string('draw_path')->nullable();
			$table->text('files')->nullable();
			$table->integer('created_by')->nullable();
			$table->string('updated_by', 50)->nullable();
			$table->string('deleted_by', 50)->nullable();
            $table->timestamps();
            $table->softDeletes();
		});
	}

	public function down()
	{
		Schema::drop('pemeriksaan_penunjang');
	}
}