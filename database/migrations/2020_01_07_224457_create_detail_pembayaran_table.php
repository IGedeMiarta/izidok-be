<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateDetailPembayaranTable extends Migration {

	public function up()
	{
		Schema::create('detail_pembayaran', function(Blueprint $table) {
			$table->increments('id');
			$table->integer('pembayaran_id');
			$table->string('kode_layanan', 20);
			$table->string('nama_layanan', 50);
			$table->float('tarif');
			$table->integer('quantity');
			$table->integer('created_by')->nullable();
			$table->string('updated_by', 50)->nullable();
			$table->string('deleted_by', 50)->nullable();
            $table->timestamps();
            $table->softDeletes();
		});
	}

	public function down()
	{
		Schema::drop('detail_pembayaran');
	}
}