<?php

use Illuminate\Database\Seeder;
use App\Constant;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
    	$data_reference = array( 
            array('id'=>1, 'username'=> 'rian','email'=> 'calledme.rian@gmail.com','password'=> '$2y$10$2f910Vl5BrJl/QNLU5s9u.wT.CkwUu2lAiakyRc.hYZVhNEuChZSa','nama'=>'rian', 'is_first_login' => 0,'created_at'=>date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')),
            array('id'=>2, 'username'=> 'riann','email'=> 'rian.nugraha@medlinx.co.id','password'=> '$2y$10$wxc8bMmAyzD3.xZfjSr.TuDcxhF62JbMTURjpl.fz4fe0Q7Kk7UCC','nama'=>'rian', 'is_first_login' => 1,'created_at'=>date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')),
            array('id'=>3, 'username'=> 'bayu074','email'=> 'bayu074@gmail.com','password'=> '$2y$10$YZ4TlLTgPZs9uqS9yBTsQO9NkFlgLL21rgxY8m7Kh3jFP1FcBKxRO','nama'=>'Bayu', 'is_first_login' => 0,'created_at'=>date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')),
            array('id'=>4, 'username'=> 'widya','email'=> 'widyasari.oktaviani@gmail.com','password'=> '$2y$10$3tInJ2LiJRiEFCJO4VyOyONWfLAuzDsZQx6J7fI60bOhjDXHS88y6','nama'=>'widya', 'is_first_login' => 0,'created_at'=>date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')),
    	);

        DB::table('users')->insert($data_reference);
    }
}
