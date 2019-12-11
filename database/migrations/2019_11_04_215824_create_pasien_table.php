<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreatePasienTable extends Migration {

	public function up()
	{
		Schema::create('pasien', function(Blueprint $table) {
			$table->increments('id');
			$table->string('nama');
			$table->string('nik', 50)->nullable();
			$table->string('tempat_lahir', 50)->nullable();
			$table->date('tanggal_lahir');
			$table->integer('jenis_kelamin');
			$table->string('golongan_darah', 5)->nullable();
			$table->string('alamat_rumah');
			$table->string('rt', 5)->nullable();
			$table->string('rw',5)->nullable();
			$table->string('kelurahan', 50)->nullable();
			$table->string('kecamatan', 50)->nullable();
			$table->string('status_perkawinan', 50);
			$table->string('pekerjaan', 50)->nullable();
			$table->string('nomor_hp', 30)->nullable();
			$table->string('nama_penjamin',100)->nullable();
			$table->string('nomor_polis', 50)->nullable();
			$table->string('email', 50)->nullable();
			$table->string('nama_penanggung_jawab',100)->nullable();
			$table->integer('tensi_sistole')->nullable();
			$table->integer('tensi_diastole')->nullable();
			$table->integer('nadi')->nullable();
			$table->integer('suhu')->nullable();
			$table->integer('respirasi')->nullable();
			$table->integer('tinggi_badan')->nullable();
			$table->integer('berat_badan')->nullable();
			$table->integer('nomor_rekam_medis')->nullable();
			$table->integer('user_id');
			$table->integer('klinik_id');
			$table->integer('created_by');
			$table->string('updated_by', 50)->nullable();
			$table->string('deleted_by', 50)->nullable();
			$table->timestamps();
            $table->softDeletes();
		});
	}

	public function down()
	{
		Schema::drop('pasien');
	}
}