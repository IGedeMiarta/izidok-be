<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateApiKeyTable extends Migration {

	public function up()
	{
		Schema::create('api_key', function(Blueprint $table) {
			$table->increments('id');
			$table->integer('user_id');
			$table->string('api_key', 255);
			$table->timestamp('logout_at')->nullable();
			$table->timestamp('expired_at')->nullable();
			$table->timestamps();
			$table->softDeletes();
		});
	}

	public function down()
	{
		Schema::drop('api_key');
	}
}