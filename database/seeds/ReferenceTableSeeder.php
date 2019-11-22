<?php

use Illuminate\Database\Seeder;
use App\Constant;

class ReferenceTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
    	$data_reference = array(
    		array('id'=>'1', 'key'=> 'forgot_valid','value'=> 'localhost/forgot_password/valid','category'=> 'url_redirection','created_at'=>date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')),
    		array('id'=>'2', 'key'=> 'forgot_invalid','value'=> 'localhost/forgot_password/invalid','category'=>'url_redirection','created_at'=>date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')),
            array('id'=>'3', 'key'=> 'act_opt_valid','value'=> 'localhost/activation_operator/valid','category'=> 'url_redirection','created_at'=>date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')),
            array('id'=>'4', 'key'=> 'act_opt_invalid','value'=> 'localhost/activation_operator/invalid','category'=>'url_redirection','created_at'=>date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')),
    	);

        DB::table('reference')->insert($data_reference);
    }
}
