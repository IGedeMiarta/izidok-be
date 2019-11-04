<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateUserRoleTable extends Migration {

	public function up()
	{
		Schema::create('user_role', function(Blueprint $table) {
			$table->increments('id');
			$table->timestamps();
			$table->softDeletes();
			$table->integer('user_id');
			$table->integer('role_id');
			$table->string('created_by', 50);
			$table->string('updated_by', 50);
			$table->string('deleted_by', 50);
		});
	}

	public function down()
	{
		Schema::drop('user_role');
	}
}