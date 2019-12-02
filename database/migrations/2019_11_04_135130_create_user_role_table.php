<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateUserRoleTable extends Migration {

	public function up()
	{
		Schema::create('user_role', function(Blueprint $table) {
			$table->increments('id');
			$table->integer('user_id');
			$table->integer('role_id');
			$table->integer('created_by');
			$table->string('updated_by', 50)->nullable();
			$table->string('deleted_by', 50)->nullable();
			$table->timestamps();
			$table->softDeletes();
		});
	}

	public function down()
	{
		Schema::drop('user_role');
	}
}