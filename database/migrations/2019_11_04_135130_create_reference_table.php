<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateReferenceTable extends Migration {

	public function up()
	{
		Schema::create('reference', function(Blueprint $table) {
			$table->increments('id');
			$table->string('key', 255)->index();
			$table->string('value', 255);
			$table->string('category', 255);
			$table->integer('created_by')->nullable();
			$table->string('updated_by', 50)->nullable();
			$table->string('deleted_by', 50)->nullable();
			$table->timestamps();
			$table->softDeletes();
		});
	}

	public function down()
	{
		Schema::drop('reference');
	}
}