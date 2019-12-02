<?php

use Illuminate\Database\Seeder;
use App\Constant;

class RoleTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
    	$data_role = array(
    		array('id'=>'1', 'role'=> 'internal_admin','created_at'=>date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')),
    		array('id'=>'2', 'role'=> 'klinik_admin','created_at'=>date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')),
    		array('id'=>'3', 'role'=> 'klinik_operator','created_at'=>date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')),
    		array('id'=>'4', 'role'=> 'klinik_owner','created_at'=>date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')),
    		array('id'=>'5', 'role'=> 'dokter','created_at'=>date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s'))
    	);

        DB::table('role')->insert($data_role);
    }
}
