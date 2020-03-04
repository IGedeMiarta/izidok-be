<?php

use Illuminate\Database\Seeder;


class OrganTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data_reference = array(
            array('id' => 1, 'nama' => 'Dada', 'sub_nama' => 'Paru - paru', 'gambar' => 'http://localhost:9001/api/v1/image?path=organs/dada.png', 'created_by' => 0),
            array('id' => 2, 'nama' => 'Mulut', 'sub_nama' => 'Amandel', 'gambar' => 'http://localhost:9001/api/v1/image?path=organs/mulut.jpg', 'created_by' => 0),
            array('id' => 3, 'nama' => 'Organ Reproduksi Pria', 'sub_nama' => 'Penis, Testis', 'gambar' => 'http://localhost:9001/api/v1/image?path=organs/organ_reproduksi_pria.jpg', 'created_by' => 0),
            array('id' => 4, 'nama' => 'Organ Reproduksi Wanita', 'sub_nama' => 'Rahim', 'gambar' => 'http://localhost:9001/api/v1/image?path=organs/organ_reproduksi_wanita.jpg', 'created_by' => 0),
            array('id' => 5, 'nama' => 'Tangan', 'sub_nama' => 'Tangan', 'gambar' => 'http://localhost:9001/api/v1/image?path=organs/tangan.jpg', 'created_by' => 0),
            array('id' => 6, 'nama' => 'Telinga', 'sub_nama' => 'Telinga', 'gambar' => 'http://localhost:9001/api/v1/image?path=organs/telinga.jpg', 'created_by' => 0),
        );

        DB::table('organ')->insert($data_reference);
    }
}
