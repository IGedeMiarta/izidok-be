<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\User;
use App\ApiKey;
use App\ForgotPassword;
use App\Activation;
use App\Reference;
use App\Constant;
use App\Rereference;
use App\Operator;

class UserController extends Controller
{

    public function index(Request $request)
    {
        $user = User::with('roles')->paginate($request->limit);

        if (!$user) {
            return response()->json(['status' => false]);
        } else {
            return response()->json(['status' => true, 'data' => $user]);
        }
    }

    public function show($id = null)
    {
        $user = User::with('roles')->find($id);
        if (!$user) {
            return response()->json(['status' => false]);
        } else {
            return response()->json(['status' => true, 'data' => $user]);
        }
    }

    public function delete($id = null)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json(['status' => false]);
        } else {
            $username = $user->username;
            $user->delete();
            return response()->json(['status' => true, 'message' => 'User \'' . $username . '\' has been deleted']);
        }
    }

    public function update(Request $request)
    {
        $user = User::find($request->id);
        if (!$user) {
            return response()->json(['status' => false]);
        } else {
            $user->email = $request->email;
            $user->nama = $request->nama;
            $user->nomor_telp = $request->nomor_telp;
            $user->save();
            return response()->json(['status' => true, 'data' => $user]);
        }
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'username' => 'required|unique:users|string',
            'nama' => 'required|string',
            'nomor_telp' => 'required|regex:/^([0-9\s\-\+\(\)]*)$/|min:8|max:15',
            'email' => 'required|unique:users|email',
            'password' => 'required|min:6'
        ]);

        $username = $request->input('username');
        $email = $request->input('email');
        $nama = $request->input('nama');
        $nomor_telp = $request->input('nomor_telp');
        $password = Hash::make($request->input('password'));

        $activation_url = Reference::where('key', Constant::VERIFY_EMAIL)->first();

        $user = User::create([
            "username" => $username,
            "email" => $email,
            "password" => $password,
            "nama" => $nama,
            "nomor_telp" => $nomor_telp
        ]);

        #activation token
        $activation = new Activation();
        $activation->token = base64_encode(str_random(10));
        $activation->user_id = $user->id;
        $activation->expired_at = date('Y-m-d H:i:s', strtotime('+7 days'));
        $activation->save();

        $data['user'] = $user;
        $data['activation_url'] =  url(env('APP_PREFIX', 'api/v1').$activation_url->value.'/'. $activation->token);

        $email_data = [
            'subject' => 'User Activatoin',
            'activation_url' => $data['activation_url'],
            'to' => [$user->email],
            'from' => 'izidok.dev@gmail.com',
            'username' => $user->username
        ];

        if ($user) {
            if (\sendEmail($email_data)) {
                return response()->json([
                    'status' => true,
                    'message' => 'Register Success!!',
                    'data' => $data
                ], 201);
            }
        }

        return response()->json([
            'status' => false,
            'message' => 'Register Fail!!',
            'data' => ''
        ], 400);
    }

    public function login(Request $request)
    {
        $username = $request->username;
        $password = $request->password;

        $user = User::where('username', $username)->with('roles')->first();

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'User not found...'
            ]);
        }

        if ($user->activation->status == 0) {
            return response()->json([
                'status' => false,
                'message' => 'Please check your email to activate user...'
            ]);
        }

        if (Hash::check($password, $user->password)) {
            $first_login = false;
            if ($user->is_first_login == 1) {
                $user->update([
                    'is_first_login' => 0
                ]);

                $first_login = true;
            }

            $token = base64_encode(str_random(40));

            #save into tb ApiKey
            $api_key = new Apikey;
            $api_key->user_id = $user->id;
            $api_key->expired_at = date('Y-m-d H:m:s', strtotime('+7 days'));
            $api_key->api_key = $token;
            $api_key->save();

            #get klinik
            $operator = Operator::where('user_id', $user->id)->first();
            $kliniks = null;
            if($operator){
                $kliniks = $operator->kliniks;
            }

            return response()->json([
                'status' => true,
                'message' => 'Login Berhasil',
                'data' => [
                    'user' => $user,
                    'token' => $token,
                    'first_login' => $first_login,
                    'kliniks' => $kliniks
                ]
            ], 201);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Login Gagal',
                'data' => ''
            ]);
        }
    }

    public function logout(Request $request)
    {
        $api_key = $request->bearerToken();
        $api_key = ApiKey::whereApiKey($api_key)->first();

        if ($api_key) {
            // $user->update([
            //     'api_token' => ''
            // ]);

            #update api_key to expired
            $api_key->logout_at = date('Y/m/d h:i:s');
            $api_key->save();

            return response()->json([
                'status' => true,
                'message' => 'Logout Berhasil',
                'data' => [
                    'user' => $api_key->user,
                    'api_key' => $api_key
                ]
            ], 201);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Logout gagal, user tidak ditemukan',
                'data' => ''
            ]);
        }
    }

    public function forgot(Request $request)
    {
        $email = $request->input('email');
        $user = User::where('email', '=', $email)->first();

        if (empty($user)) {
            return response()->json([
                'status' => false,
                'message' => 'User tidak ditemukan'
            ]);
        } else {
            $forgot_password = new ForgotPassword();
            $forgot_password->token = base64_encode(str_random(40));
            $forgot_password->user_id = $user->id;
            $forgot_password->email = $email;
            $forgot_password->expired_at = date('Y-m-d H:i:s', strtotime('+7 days'));
            $forgot_password->save();

            $forgot_url = url(env('APP_PREFIX', 'api/v1') . '/forgot_password/' . $forgot_password->token);

            $email_data = [
                'subject' => 'Forgot Password',
                'message' => 'Click link below to reset your password: \n '. $forgot_url,
                'activation_url' => $forgot_url,
                'to' => ['helmysmp@gmail.com', $forgot_password->email],
                'from' => 'izidok.dev@gmail.com'
            ];

            if(\sendForgotEmail($email_data)){
                return response()->json([
                    'status' => true,
                    'message' => 'forgot password telah dibuat',
                    'data' => $forgot_password
                ]);
            }

            // Mail::raw('You can reset password by klik :'.url('/api/v1/forgot_password/'.$forgot_password->token), function($msg) use ($request){ 
            //     $msg->subject('Hi reset your password'); 
            //     $msg->to([$request->email]); 
            //     $msg->from(['izi-dok@gmail.com']); });


        }
    }

    public function check_forgot($token){
        // echo $token;
        $forgot_password = ForgotPassword::where('token', $token)->first();

        if (empty($forgot_password)) {
            $key = Constant::FORGOT_INVALID;
            $category = Constant::REDIRECTION;

            $config = Reference::where('key', $key)
                ->where('category', $category)->first();
            $data['url'] = $config->value;

            return response()->json([
                'status' => false,
                'message' => 'forgot password not found',
                'data' => $data
            ]);
        } else if (strtotime(date('Y-m-d H:i:s')) > strtotime($forgot_password->expired_at)) {
            $key = Constant::FORGOT_INVALID;
            $category = Constant::REDIRECTION;
            $config = Reference::where('key', $key)
                ->where('category', $category)->first();
            $data['url'] = $config->value;

            return redirect($config->value);
        }
        else
        {
            $key = Constant::FORGOT_VALID;
            $category = Constant::REDIRECTION;
            $config = Reference::where('key', $key)
                ->where('category', $category)->first();
            $data['url'] = $config->value;
            $data['token'] = $token;

            return redirect($config->value);
        }
    }

    public function reset(Request $request)
    {
        $token = $request->token;
        $forgot_password = ForgotPassword::where('token', $token)->first();
        $password = $request->input('password');
        $konfirm_password = $request->input('konfirm_password');

        if (empty($forgot_password)) {
            return response()->json([
                'status' => false,
                'message' => 'user not found'
            ]);
        }
        if ($password != $konfirm_password) {
            return response()->json([
                'status' => false,
                'message' => 'password dan konfirm passowrd tidak sama'
            ]);
        } else if (strtotime(date('Y-m-d H:i:s')) > strtotime($forgot_password->expired_at)) {
            return response()->json([
                'status' => false,
                'message' => 'expired'
            ]);
        } else {
            $user = User::find($forgot_password->user_id);
            $user->password = Hash::make($password);
            $user->save();
            $forgot_password->delete();
            return response()->json([
                'status' => true,
                'message' => 'Password has ben updated'
            ]);
        }
    }

    public function activate($token)
    {
        $activation = Activation::where('token', $token)->first();
        // $reference = Reference::where('key', 'activation_failed')->first();

        if (!$activation) {
            $url = Reference::where('key', Constant::ACTIVATION_FAILED)->first();
            return redirect($url->value);
        }

        if (strtotime(date('Y-m-d H:i:s')) > strtotime($activation->expired_at)) {
            $url = Reference::where('key', Constant::ACTIVATION_FAILED)->first();
            return redirect($url->value);
        }

        if ($activation->status == 1) {
            $url = Reference::where('key', Constant::ALREADY_ACTIVATED)->first();
            return redirect($url->value);
        }

        $activation->status = 1;

        if ($activation->save()) {
            $url = Reference::where('key', Constant::ACTIVATION_SUCCESS)->first();
            return redirect($url->value);
        }
    }

    public function sendEmail($user_id)
    {
        $activation = Activation::with('user')
            ->where('user_id', $user_id)
            ->orderBy('created_at')
            ->first();

        $activation_url = Reference::where('key', Constant::VERIFY_EMAIL)->first();

        if ($activation->status === 1) {
            return response()->json([
                'status' => false,
                'message' => 'User already activated...'
            ]);
        }

        if (strtotime(date('Y-m-d H:i:s')) > strtotime($activation->expired_at)) {
            return response()->json([
                'status' => false,
                'message' => 'Activation token was expired...'
            ]);
        }

        if (!$activation) {
            return response()->json([
                'status' => false,
                'message' => 'Resend email request invalid...'
            ]);
        }


        $user = $activation->user;

        $data['user'] = $user;
        $data['activation_url'] =  url(env('APP_PREFIX', 'api/v1').$activation_url->value.'/'. $activation->token);

        $email_data = [
            'subject' => 'User Activatoin',
            'activation_url' => $data['activation_url'],
            'to' => $user->email,
            'from' => 'izidok.dev@gmail.com',
            'username' => $user->username
        ];

        if (\sendEmail($email_data)) {
            return response()->json([
                'status' => true,
                'message' => 'Email has been re-send successfully...',
                'data' => $data
            ]);
        }

        return response()->json([
            'status' => false,
            'message' => 'There is something wrong...'
        ]);
    }

    public function verifyEmail(Request $request)
    {
        $this->validate($request, [
            'email' => 'unique:users|email',
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Email is available...'
        ], 201);
    }

    public function verifyUsername(Request $request)
    {
        $this->validate($request, [
            'username' => 'unique:users|string',
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Username is available...'
        ], 201);
    }
}
