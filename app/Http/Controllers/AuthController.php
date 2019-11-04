<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
Use App\User;

class AuthController extends Controller
{

	public function index(){
		$user = User::with('roles')->get();
        if ($user === null) {
            return response()->json(['status' => false]);
        }else{
            return response()->json(['status' => true, 'data' => $user]);
        }
	}

	public function show($id = null){
		$user = User::with('roles')->find($id);
        if ($user === null) {
            return response()->json(['status' => false]);
        }else{
            return response()->json(['status' => true, 'data' => $user]);
        }
	}

	public function delete($id = null){
		$user = User::find($id);

        if ($user === null) {            
            return response()->json(['status' => false]);
        }else{
            $username = $user->username;
            $user->delete();
            return response()->json(['status' => true, 'message' => 'User \''.$username.'\' has been deleted']);
        }
	}

	public function update(Request $request){
		$user = User::find($request->id);
        if ($user === null) {            
            return response()->json(['status' => false]);
        }else{
			$user->email = $request->email;
			$user->nama = $request->nama;
			$user->nomor_telp = $request->nomor_telp;
            $user->save();
            return response()->json(['status' => true, 'data' => $user]);
        }
	}


    public function store(Request $request)
    {
    	$username = $request->input('username');
    	$email = $request->input('email');
    	$nama = $request->input('nama');
    	$no_telp = $request->input('no_telp');
    	$password = Hash::make($request->input('password'));

    	$register = User::create([
    		"username" => $username,
    		"email" => $email,
    		"password" => $password,
    		"nama" => $nama,
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

    public function logout(Request $request)
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
                    'api_token' => $api_token
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
