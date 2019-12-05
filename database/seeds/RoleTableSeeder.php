<?php

use Illuminate\Database\Seeder;
use App\Constant;
use Illuminate\Support\Facades\DB;

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
    		array('id'=>'1', 'role'=> 'super_admin','created_at'=>date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')),
    		array('id'=>'2', 'role'=> 'admin_klinik','created_at'=>date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')),
    		array('id'=>'3', 'role'=> 'operator','created_at'=>date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')),
    		array('id'=>'4', 'role'=> 'dokter_praktek','created_at'=>date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')),
    		array('id'=>'5', 'role'=> 'dokter_klinik','created_at'=>date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s'))
    	);

        DB::table('role')->insert($data_role);
    }
}
