<?php

use Illuminate\Database\Seeder;
use App\Constant;

class UserRoleTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
    	$data_reference = array( 
            array('id'=>1, 'user_id'=>1, 'role_id'=>4),
            array('id'=>2, 'user_id'=>2, 'role_id'=>4),
            array('id'=>3, 'user_id'=>3, 'role_id'=>4),
            array('id'=>4, 'user_id'=>4, 'role_id'=>4),
    	);

        DB::table('user_role')->insert($data_reference);
    }
}
