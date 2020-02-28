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
use App\Operator;
use App\Klinik;
use App\Layanan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Carbon\Carbon;

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
        $this->validate($request, [
            'nama' => 'required|string',
            'nomor_telp' => 'required|regex:/^([0-9\s\-\+\(\)]*)$/|min:8|max:15',
            'email' => 'required|email',
            'alamat' => 'string',
        ]);

        $user = User::find($request->id);
        if (!$user) {
            return response()->json(['status' => false]);
        } else {
            if ($user->email !== $request->email) {
                $this->validate($request, [
                    'email' => 'required|unique:users|email',
                ]);
            }

            $user->email = $request->email;
            $user->nama = $request->nama;
            $user->nomor_telp = $request->nomor_telp;
            $user->alamat = $request->alamat;
            $user->save();
            return response()->json(['status' => true, 'data' => $user]);
        }
    }

    public function uploadFotoProfile (Request $request){
        $this->validate($request, [
            'foto_profile' => 'required|file|max:5000',
        ]);

        $user = User::find($request->id);
        if(!$user){
            return response()->json(['status' => false, 'message' => 'user not found...']);
        }

        $res = uploadToCloud('foto_profile', $request->foto_profile);
        $user->foto_profile = $res['url'];

        if(!$user->save()){
            return response()->json(['status' => false, 'message' => 'update user failed...']);
        }

        return response()->json(['status' => true, 'data' => $user]);

    }

    public function store(Request $request)
    {
        //
    }

    public function login(Request $request)
    {
        $username = $request->username;
        $password = $request->password;

        $user = User::where('nomor_telp', $username)->with('roles')->orderBy('id', 'desc')->first();
        if (!$user) {
            $user = User::where('email', $username)->with('roles')->orderBy('id', 'desc')->first();
        }

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'User not found...'
            ]);
        }
        if ($user->roles->first()->name !== Constant::OPERATOR) {
            if ($user->activation->status == 0) {

                // user belum aktivasi di bawah batas expired
                if(Carbon::now() < $user->activation->expired_at) {
                    return response()->json([
                        'status' => false,
                        'message' => 'Please check your email to activate user...',
                        'user_id' => $user->id,
                        'created_at' => $user->activation->created_at,
                    ]);
                }

                // user belum aktivasi lebih dari 10 menit
                return response()->json([
                    'status' => false,
                    'message' => 'User not found...'
                ]);
            }
        }

        if (Hash::check($password, $user->password)) {
            $token = base64_encode(str_random(40));

            #save into tb ApiKey
            $api_key = new Apikey;
            $api_key->user_id = $user->id;
            $api_key->expired_at = date('Y-m-d H:m:s', strtotime('+7 days'));
            $api_key->api_key = $token;
            $api_key->save();

            $position = '';
            $first_login = $user->is_first_login;

            if ($first_login === 1) {
                $first_login = true;
                $op = Operator::where('created_by',$user->id)->exists();
                $trf = Layanan::where('klinik_id',$user->klinik->id)->exists();

                if (is_null($user->klinik->spesialisasi_id)) {
                    $position = 'spesialisasi';
                } elseif (!$op) {
                    $position = 'operator';
                } elseif (!$trf) {
                    $position = 'tarif';
                }
            } else {
                $first_login = false;
            }

            $user->last_session = substr($token, 10,20);
            $user->save();

            $request->userId = $user->id;
            \saveAudits('login',$request, null);

            return response()->json([
                'status' => true,
                'message' => 'Login Berhasil',
                'data' => [
                    'user' => $user,
                    'token' => $token,
                    'first_login' => $first_login,
                    'klinik' => $user->klinik,
                    'init_position' => $position
                ]
            ], 201);
        } else {
            $request->userId = $user->id;
            \saveAudits('failed login',$request, null);
            return response()->json([
                'status' => false,
                'message' => 'Login Gagal',
                'data' => ''
            ]);
        }
    }

    public function logout(Request $request)
    {
        if(!$request->bearerToken()){
            \saveAudits('failed logout, user has not been logged in',$request, null);
            return response()->json([
                'status' => false,
                'message' => 'Logout gagal, user belum melakukan login!',
            ]);
        }

        $api_key = $request->bearerToken();
        $api_key = ApiKey::whereApiKey($api_key)->first();

        if ($api_key) {
            \saveAudits('logout',$request, null);
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
            \saveAudits('failed logout, user not found',$request, null);
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
        $user = User::where('email', '=', $email)->orderBy('id', 'desc')->first();

        if (empty($user)) {
            return response()->json([
                'status' => false,
                'message' => 'User not found'
            ]);
        }
        else
        {
            $all_activation = Activation::where("user_id","=",$user->id)->get();
            $flag = 0;
            foreach ($all_activation as $row)
            {
                if($row->status == 1)
                {
                    $flag = 1;
                }
            }

            if($flag == 0)
            {
                return response()->json([
                    'status' => false,
                    'message' => 'User has not been activated'
                ]);
            }
            else
            {
                $all_forgot = ForgotPassword::where("user_id", "=", $user->id)->get();
                foreach ($all_forgot as $forgot) {
                    $forgot->delete();
                }

                $forgot_password = new ForgotPassword();
                $forgot_password->token = base64_encode(str_random(40));
                $forgot_password->user_id = $user->id;
                $forgot_password->email = $email;
                $forgot_password->expired_at = date('Y-m-d H:i:s', strtotime('+7 days'));
                $forgot_password->save();

                $forgot_url = url(env('APP_PREFIX', 'api/v1') . '/check_forgot/' . $forgot_password->token);
                $current_user_name = User::select('nama')->where('id', $user->id)->value('nama');

                $email_data = [
                    'subject' => 'Forgot Password',
                    'message' => 'Click link below to reset your password: \n ' . $forgot_url,
                    'activation_url' => $forgot_url,
                    'to' => [$forgot_password->email],
                    'from' => 'izidok.dev@gmail.com',
                    'name' =>  $current_user_name,
                ];

                if (\sendEmail($email_data, Constant::FORGOT_EMAIL_TEMPLATE)) {
                    return response()->json([
                        'status' => true,
                        'message' => 'forgot password telah dibuat',
                        'data' => $forgot_password
                    ]);
                }
            }

            // Mail::raw('You can reset password by klik :'.url('/api/v1/forgot_password/'.$forgot_password->token), function($msg) use ($request){
            //     $msg->subject('Hi reset your password');
            //     $msg->to([$request->email]);
            //     $msg->from(['izi-dok@gmail.com']); });


        }
    }

    public function check_forgot($token)
    {
        // echo $token;
        $forgot_password = ForgotPassword::where('token', $token)->orderBy('id', 'desc')->first();

        if (empty($forgot_password)) {
            $key = Constant::FORGOT_INVALID;
            $category = Constant::REDIRECTION;

            $config = Reference::where('key', $key)
                ->where('category', $category)->first();
            $data['url'] = $config->value;

            return redirect($config->value);

            /*return response()->json([
                'status' => false,
                'message' => 'forgot password not found',
                'data' => $data
            ]);*/
        } else if (strtotime(date('Y-m-d H:i:s')) > strtotime($forgot_password->expired_at)) {
            $key = Constant::FORGOT_INVALID;
            $category = Constant::REDIRECTION;
            $config = Reference::where('key', $key)
                ->where('category', $category)->first();
            $data['url'] = $config->value;

            return redirect($config->value);
        } else {
            $key = Constant::FORGOT_VALID;
            $category = Constant::REDIRECTION;
            $config = Reference::where('key', $key)
                ->where('category', $category)->first();
            $data['url'] = $config->value;
            $data['token'] = $token;

            return redirect($config->value . $token);
        }
    }

    public function reset(Request $request)
    {
        $this->validate($request, [
            'password' => 'required|min:6',
            'konfirm_password' => 'required|min:6',
        ]);

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
                'message' => 'password dan konfirm password tidak sama'
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

        if ($activation->status == 1) {
            $url = Reference::where('key', Constant::ALREADY_ACTIVATED)->first();
            return redirect($url->value);
        }

        if (strtotime(date('Y-m-d H:i:s')) > strtotime($activation->expired_at)) {
            $url = Reference::where('key', Constant::ACTIVATION_EXPIRED)->first();
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
        $data['activation_url'] =  url(env('APP_PREFIX', 'api/v1') . $activation_url->value . '/' . $activation->token);

        $email_data = [
            'subject' => 'Konfirmasi Akun izidok',
            'from' => 'izidok.dev@gmail.com',
            'to' => [$user->email],
            'activation_url' => $data['activation_url'],
            'name' => $user->nama,
            'phone' => $user->nomor_telp,
            'email' => $user->email,
            'password' => 'Sesuai dengan yang di input',
        ];

        if (\sendEmail($email_data, Constant::ACTIVATION_EMAIL_TEMPLATE)) {
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

    public function sendEmailOperator($user_id)
    {
        $operator = Operator::with('user')
            ->where('user_id', $user_id)
            ->orderBy('created_at')
            ->first();

        $doctor = User::select(['nama','email'])->where('id', $operator->created_by)->first();

        $data['operator'] = $operator;

        $email_data = [
            'subject' => 'Operator Login Data',
            'from' => 'izidok.dev@gmail.com',
            'to' => [$doctor->email],
            'doctor_name' => $doctor->nama,
            'name' => $operator->nama,
            'phone' => $operator->user->nomor_telp,
            'email' => $operator->user->email,
            'password' => 'Sesuai dengan yang di input',
        ];

        if (\sendEmail($email_data, Constant::OPERATOR_EMAIL_TEMPLATE)) {
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
        $email = User::where('email', $request->email)->orderBy('id', 'desc')->get();
        if ($this->isUserInvalid($email)) {
            return response()->json(['status' => true, 'message' => 'email is valid']);
        } else {
            return response()->json(['status' => false, 'message' => 'email is already in use!']);
        }
    }

    public function verifyPhone(Request $request)
    {
        $phone = User::where('nomor_telp', $request->nomor_telp)->orderBy('id', 'desc')->get();
        if ($this->isUserInvalid($phone)) {
            return response()->json(['status' => true, 'message' => 'phone number is valid']);
        } else {
            return response()->json(['status' => false, 'message' => 'phone is already in use!']);
        }
    }

    public function isUserInvalid($users)
    {
        if ($users) {
            foreach ($users as $item) {
                if (empty($item->activation)) {
                    return false;
                } elseif ($item->activation->status === 0 && $item->activation->expired_at < date('Y-m-d H:i:s')) {
                    return true;
                } else {
                    return false;
                }
            }

            return true;
        }
    }

    public function createRoles()
    {
        Role::create(['name' => 'super_admin']);
        Role::create(['name' => 'admin_klinik']);
        Role::create(['name' => 'dokter_praktek']);
        Role::create(['name' => 'dokter_klinik']);
        Role::create(['name' => 'operator']);
    }

    public function createPermissions()
    {
        Permission::create(['name' => 'read-dashboard']);

        Permission::create(['name' => 'create-transklinik']);
        Permission::create(['name' => 'read-transklinik']);
        Permission::create(['name' => 'update-transklinik']);
        Permission::create(['name' => 'delete-transklinik']);

        Permission::create(['name' => 'create-rekam-medis']);
        Permission::create(['name' => 'read-rekam-medis']);
        Permission::create(['name' => 'update-rekam-medis']);
        Permission::create(['name' => 'delete-rekam-medis']);

        Permission::create(['name' => 'create-klinik']);
        Permission::create(['name' => 'read-klinik']);
        Permission::create(['name' => 'update-klinik']);
        Permission::create(['name' => 'delete-klinik']);

        Permission::create(['name' => 'create-pasien']);
        Permission::create(['name' => 'read-pasien']);
        Permission::create(['name' => 'update-pasien']);
        Permission::create(['name' => 'delete-pasien']);

        Permission::create(['name' => 'create-dokter']);
        Permission::create(['name' => 'read-dokter']);
        Permission::create(['name' => 'update-dokter']);
        Permission::create(['name' => 'delete-dokter']);

        Permission::create(['name' => 'create-operator']);
        Permission::create(['name' => 'read-operator']);
        Permission::create(['name' => 'update-operator']);
        Permission::create(['name' => 'delete-operator']);

        Permission::create(['name' => 'create-tarif']);
        Permission::create(['name' => 'read-tarif']);
        Permission::create(['name' => 'update-tarif']);
        Permission::create(['name' => 'delete-tarif']);
    }

    public function assignPermissions()
    {
        $super_admin = Role::findByName('super_admin');
        $admin_klinik = Role::findByName('admin_klinik');
        $dokter_praktek = Role::findByName('dokter_praktek');
        $dokter_klinik = Role::findByName('dokter_klinik');
        $operator = Role::findByName('operator');

        $super_admin->syncPermissions([
            'read-dashboard',
            'read-transklinik',
            'create-klinik',
            'read-klinik',
            'update-klinik',
            'delete-klinik',
            'read-pasien',
            'create-dokter',
            'read-dokter',
            'update-dokter',
            'delete-dokter',
            'create-operator',
            'read-operator',
            'update-operator',
            'delete-operator',
            'create-tarif',
            'read-tarif',
            'update-tarif',
            'delete-tarif'
        ]);

        $admin_klinik->syncPermissions([
            'read-dashboard',
            'create-transklinik',
            'read-transklinik',
            'update-transklinik',
            'delete-transklinik',
            'create-klinik',
            'read-klinik',
            'update-klinik',
            'delete-klinik',
            'read-pasien',
            'update-pasien',
            'delete-pasien',
            'create-dokter',
            'read-dokter',
            'update-dokter',
            'delete-dokter',
            'create-operator',
            'read-operator',
            'update-operator',
            'delete-operator',
            'create-tarif',
            'read-tarif',
            'update-tarif',
            'delete-tarif'
        ]);
        $dokter_praktek->syncPermissions([
            'read-dashboard',
            'create-transklinik',
            'read-transklinik',
            'update-transklinik',
            'delete-transklinik',
            'create-rekam-medis',
            'read-rekam-medis',
            'update-rekam-medis',
            'delete-rekam-medis',
            'create-klinik',
            'read-klinik',
            'update-klinik',
            'delete-klinik',
            'create-pasien',
            'read-pasien',
            'update-pasien',
            'delete-pasien',
            'create-operator',
            'read-operator',
            'update-operator',
            'delete-operator',
            'create-tarif',
            'read-tarif',
            'update-tarif',
            'delete-tarif'
        ]);
        $dokter_klinik->syncPermissions([
            'read-dashboard',
            'create-rekam-medis',
            'read-rekam-medis',
            'update-rekam-medis',
            'delete-rekam-medis',
            'read-pasien',
            'update-pasien',
            'delete-pasien'
        ]);
        $operator->syncPermissions([
            'read-dashboard',
            'create-transklinik',
            'read-transklinik',
            'update-transklinik',
            'delete-transklinik',
            'create-pasien',
            'read-pasien',
            'update-pasien',
            'delete-pasien',
            'create-tarif',
            'read-tarif',
            'update-tarif',
            'delete-tarif'
        ]);
    }

    public function test()
    {
        $user = Auth::user();
        $user->syncRoles(Role::findByName('super_admin'));
        return $user->roles;
    }

    public function changePassword(Request $request)
    {
        $this->validate($request, [
            'old_password' => 'required|string|min:6',
            'new_password' => 'required|string|min:6',
            'confirm_new_password' => 'required|string|min:6',
        ]);

        //cek dengan password lama
        $user = User::find($request->user_id);
        if(!Hash::check($request->old_password,$user->password))
        {
            return response()->json([
                'status' => false,
                'message' => 'Password salah!'
            ], 400);
        }
        else if($request->new_password != $request->confirm_new_password)
        {
            return response()->json([
                    'status' => false,
                    'message' => 'Konfirmasi Password tidak cocok'
                ], 400);
        }
        else if(Hash::check($request->new_password,$user->password))
        {
            return response()->json([
                    'status' => false,
                    'message' => 'Password telah terdaftar. Silahkan masukkan password lain!'
                ], 400);
        }
        else
        {
            $user->password = app('hash')->make($request->new_password);
            $user->save();
            return response()->json([
                'status' => true,
                'message' => 'Password Berhasil Diperbaharui'
            ], 200);
        }
    }
}
