<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateOperatorTable extends Migration {

	public function up()
	{
		Schema::create('operator', function(Blueprint $table) {
			$table->increments('id');
			$table->string('nama', 50);
			$table->string('tempat_lahir', 50);
			$table->date('tanggal_lahir');
			$table->integer('jenis_kelamin');
			$table->integer('user_id');
			$table->string('created_by', 50)->nullable();
			$table->string('updated_by', 50)->nullable();
			$table->string('deleted_by', 50)->nullable();
			$table->timestamps();
            $table->softDeletes();
		});
	}

	public function down()
	{
		Schema::drop('operator');
	}
}