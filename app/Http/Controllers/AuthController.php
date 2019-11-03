<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
Use App\User;

class AuthController extends Controller
{
    public function register(Request $request)
    {
    	$username = $request->input('username');
    	$email = $request->input('email');
    	$nama_lengkap = $request->input('nama_lengkap');
    	$no_telp = $request->input('no_telp');
    	$password = Hash::make($request->input('password'));

    	$register = User::create([
    		"username" => $username,
    		"email" => $email,
    		"password" => $password,
    		"nama_lengkap" => $nama_lengkap,
    		"no_telp" => $no_telp
    	]);

    	if($register)
    	{
    		return response()->json([
    			'success' => true,
    			'message' => 'Register Success!!',
    			'data' => $register
    		],201);
    	}
    	else
    	{
    		return response()->json([
    			'success' => false,
    			'message' => 'Register Fail!!',
    			'data' => ''
    		],400);	
    	}
    }

    public function login(Request $request)
    {
    	// $email = $request->input('email');
    	$username = $request->input('username');
    	$password = $request->input('password');

    	$user = User::where('username',$username)->first();

    	if(Hash::check($password,$user->password))
    	{
    		$apiToken = base64_encode(str_random(40));
    		$user->update([
    			'api_token' => $apiToken
    		]);

    		return response()->json([
    			'success' => true,
    			'message' => 'Login Berhasil',
    			'data' => [
    				'user' => $user,
    				'api_token' => $apiToken
    			]
    		],201);
    	}
    	else
    	{
    		return response()->json([
    			'success' => false,
    			'message' => 'Login Gagal',
    			'data' => ''
    		]);	
    	}
    }

    public function logout()
    {
        $api_token = $request->input('api_token');

        $user = User::where('api_token',$api_token);
        if($user)
        {
            $user->update([
                'api_token' => ''
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Logout Berhasil',
                'data' => [
                    'user' => $user,
                    'api_token' => $apiToken
                ]
            ],201);
                
        }
        else
        {
            return response()->json([
                'success' => false,
                'message' => 'Logout gagal, user tidak ditemukan',
                'data' => ''
            ]); 
        }

    }
}
