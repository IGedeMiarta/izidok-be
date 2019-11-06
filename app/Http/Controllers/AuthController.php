<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
Use App\User;
Use App\ApiKey;
use App\ForgotPassword;

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
        $this->validate($request,[
            'username' => 'required|unique:users|string',
            'nama' => 'required|string',
            'nomor_telp' => 'required|regex:/^([0-9\s\-\+\(\)]*)$/|min:8|max:10',
            'email' => 'required|unique:users|email',
            'password' => 'required|min:8'
        ]);

    	$username = $request->input('username');
    	$email = $request->input('email');
    	$nama = $request->input('nama');
    	$nomor_telp = $request->input('nomor_telp');
    	$password = Hash::make($request->input('password'));

    	$register = User::create([
    		"username" => $username,
    		"email" => $email,
    		"password" => $password,
    		"nama" => $nama,
    		"nomor_telp" => $nomor_telp
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
    	$username = $request->username;
		$password = $request->password;
		
    	$user = User::where('username', $username)->first();

    	if(Hash::check($password, $user->password))
    	{
    		$token = base64_encode(str_random(40));
    		// $user->update([
    		// 	'api_token' => $api_key
			// ]);
			
			#save into tb ApiKey
			$api_key = new Apikey;
			$api_key->user_id = $user->id;
			$api_key->expired_at = date('Y-m-d H:m:s', strtotime('+7 days'));
			$api_key->api_key = $token;
			$api_key->save();

    		return response()->json([
    			'success' => true,
    			'message' => 'Login Berhasil',
    			'data' => [
    				'user' => $user,
    				'token' => $token
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
        $api_key = $request->bearerToken();
		$api_key = ApiKey::whereApiKey($api_key)->first();
		
        if($api_key)
        {
            // $user->update([
            //     'api_token' => ''
            // ]);

			#update api_key to expired
			$api_key->logout_at = date('Y/m/d h:i:s');
			$api_key->save();

            return response()->json([
                'success' => true,
                'message' => 'Logout Berhasil',
                'data' => [
                    'user' => $api_key->user,
                    'api_key' => $api_key
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

    public function forgot(Request $request)
    {
        $email = $request->input('email');
        $user = User::where('email','=',$email)->first();

        if(empty($user))
        {
            return response()->json([
                'success' => false,
                'message' => 'User tidak ditemukan'
            ]);
        }
        else
        {
            $forgot_password = new ForgotPassword();
            $forgot_password->token = base64_encode(str_random(40));
            $forgot_password->user_id = $user->id;
            $forgot_password->email = $email;
            $forgot_password->expired_at = date('Y-m-d H:i:s', strtotime('+7 days'));
            $forgot_password->save();

            return response()->json([
                'success' => true,
                'message' => 'forgot password telah dibuat',
                'data' => $forgot_password
            ]);
        }
    }

    public function reset(Request $request)
    {
        $token = $request->bearerToken();
        $forgot_password = ForgotPassword::where('token',$token)->first();
        $password = $request->input('password');
        $konfirm_password = $request->input('konfirm_password');

        if(empty($forgot_password))
        {
            return response()->json([
                'success' => false,
                'message' => 'user not found'
            ]);
        }
        if($password != $konfirm_password)
        {
            return response()->json([
                'success' => false,
                'message' => 'password dan konfirm passowrd tidak sama'
            ]);   
        }
        else if(strtotime(date('Y-m-d H:i:s')) > strtotime($forgot_password->expired_at))
        {
            return response()->json([
                'success' => false,
                'message' => 'expired'
            ]);      
        }
        else
        {
            $user = User::find($forgot_password->user_id);
            $user->password = Hash::make($password);
            $user->save();
            $forgot_password->delete();
            return response()->json([
                'success' => true,
                'message' => 'Password has ben updated'
            ]); 
        }
    }
}
