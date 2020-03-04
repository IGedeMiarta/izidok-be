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
    		array('id'=>'1', 'key'=> 'forgot_valid','value'=> 'http://izidok.id/forgot-password/','category'=> 'url_redirection','created_at'=>date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')),
            array('id'=>'2', 'key'=> 'forgot_invalid','value'=> 'http://izidok.id/forgot_password/invalid','category'=>'url_redirection','created_at'=>date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')),
        
            array('id'=>'3', 'key'=> 'activation_success','value'=> 'http://izidok.id/verification/success','category'=>'url_redirection','created_at'=>date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')),
            array('id'=>'4', 'key'=> 'activation_failed','value'=> 'http://izidok.id/verification/failed','category'=>'url_redirection','created_at'=>date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')),
            array('id'=>'5', 'key'=> 'verify_email','value'=> '/activate','category'=>'url_redirection','created_at'=>date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')),
            array('id'=>'6', 'key'=> 'already_activated','value'=> 'http://izidok.id/verification/already-activated','category'=>'url_redirection','created_at'=>date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')),
    	
            array('id'=>'7', 'key'=> 'act_opt_valid','value'=> 'http://izidok.id/verification/operator/','category'=> 'url_redirection','created_at'=>date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')),
            array('id'=>'8', 'key'=> 'act_opt_invalid','value'=> 'http://izidok.id/activation_operator/invalid','category'=>'url_redirection','created_at'=>date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')),
    	);

        DB::table('reference')->insert($data_reference);
    }
}
