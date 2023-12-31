<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('username')->nullable();
            $table->string('email');
            $table->string('password')->nullable();
            $table->string('nama');
            $table->string('nomor_telp')->nullable();
            $table->string('alamat', 255)->nullable();
            $table->string('foto_profile', 255)->nullable();
            $table->integer('is_first_login')->default(1);
            $table->integer('klinik_id');
            $table->integer('created_by')->nullable();
			$table->string('updated_by', 50)->nullable();
			$table->string('deleted_by', 50)->nullable();
            $table->timestamps();
            $table->softDeletes();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('klinik_operator');
        Schema::dropIfExists('role');
        Schema::dropIfExists('user_role');
    }
}
