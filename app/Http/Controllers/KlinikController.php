<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Klinik;
use App\Operator;
use App\User;
use App\UserRole;
use App\Constant;
use App\Dokter;
use App\Activation;
use App\Reference;


class KlinikController extends Controller
{
    public function __construct()
    {
        //nothing
    }

    public function index(Request $request){
        $user_id = Auth::user()->id;
        $user_role = UserRole::where('user_id',$user_id)->first();

        if($user_role->role_id == Constant::INTERNAL_ADMIN)
        {
            $klinik = Klinik::whereHas('operator', function($q) use($user_id) {
                        $q->where('operator.user_id', $user_id);
                    })->paginate($request->limit);    
        }
        else if($user_role->role_id == Constant::KLINIK_OPERATOR ||  $user_role->role_id == Constant::KLINIK_ADMIN)
        {
            $operator = Operator::where('user_id',$user_id)->first();
            $klinik = Klinik::find($operator->klinik_id);
        }
        
        if (!$klinik) {
            return response()->json(['status' => false], 422);
        }else{
            $data['klinik'] = $klinik;
            return response()->json(['status' => true, 'data' => $data]);
        }
    }

    public function show($id = null){
        $klinik = Klinik::with('operator')->find($id);
        if (!$klinik) {
            return response()->json(['status' => false, 'message' => 'Klinik not found...'], 422);
        }else{
            return response()->json(['status' => true, 'data' => $klinik]);
        }
    }

    public function store(Request $request){
         $rules = [
            'tipe_faskes' => 'required|min:1:max:2',
            'nama_klinik' => 'required|string',
            'nomor_telp' => 'required|regex:/^([0-9\s\-\+\(\)]*)$/|min:8|max:15',
            'email' => 'required|unique:users|email',
            'username' => 'required|unique:users|string',
            'password' => 'required|confirmed|min:6'
        ];
        
        $activation_url = Reference::where('key', Constant::VERIFY_EMAIL)->first();
        $isKlinik = false;
        if($request->tipe_faskes == Constant::TIPE_KLINIK){
            $rules['nama_pic'] = 'required|string';
            $isKlinik = true;
        }
        $this->validate($request,$rules);


        if($isKlinik){
            $nama_pic = $request->nama_pic;
        }
        else{
            $nama_pic = $request->nama_klinik;
            $nama_dokter = $request->nama_klinik;
        }

        #data klinik
        $klinik = Klinik::create([
            'tipe_faskes' => $request->tipe_faskes,
            'nama_klinik' => $request->nama_klinik,
            'nama_pic' => $nama_pic,
            'nomor_ijin' => $request->nomor_ijin,
            'nomor_telp' => $request->nomor_telp
        ]);
   
        #data user
        $user = User::create([
    		"username" => $request->username,
    		"email" => $request->email,
    		"password" => app('hash')->make($request->password),
    		"nama" => $nama_pic,
    		"no_telp" => $request->nomor_telp
        ]);
        $user->roles()->attach(Constant::KLINIK_OPERATOR);

        #data operator
        $operator = new Operator([
            'nama' => $nama_pic,
            'user_id' => $user->id
        ]);
        $klinik->operator()->save($operator);

        if(!$isKlinik){
            #data dokter
            $dokter = new Dokter([
                'nama' => $nama_dokter,
                'user_id' => $user->id
            ]);
            $klinik->dokters()->save($dokter);
            $user->roles()->attach(Constant::DOKTER);
        }

        #activation token
        $activation = new Activation();
        $activation->token = base64_encode(str_random(10));
        $activation->user_id = $user->id;
        $activation->expired_at = date('Y-m-d H:i:s', strtotime('+10 minutes'));
        $activation->save();

        $data['klinik_id'] = $klinik->id;
        $data['user_id'] = $user->id;
        $data['activation_url'] =  url(env('APP_PREFIX', 'api/v1').$activation_url->value.'/'. $activation->token);       

        $email_data = [
            'subject' => 'User Activation',
            'activation_url' => $data['activation_url'],
            'to' => [$user->email],
            'from' => 'izidok.dev@gmail.com',
            'username' => $user->username
        ];

        \sendEmail($email_data, Constant::ACTIVATION_EMAIL_TEMPLATE);

        if(!$data['klinik_id']){
            return response()->json(['status' => false], 422);
        }

        return response()->json(['status' => true, 'data' => $data]);
    }

    public function update(Request $request){
        $klinik = Klinik::find($request->id);
        if (!$klinik) {            
            return response()->json(['status' => false]);
        }else{
            $klinik->nama_klinik = $request->nama_klinik;
            $klinik->save();
            return response()->json(['status' => true, 'data' => $klinik]);
        }
    }
    
    public function delete($id = null){
        $klinik = Klinik::find($id);

        if (!$klinik) {            
            return response()->json(['status' => false]);
        }else{
            $nama = $klinik->nama_klinik;
            $klinik->delete();
            return response()->json(['status' => true, 'message' => 'Klinik \''.$nama.'\' has been deleted']);
        }
    }
}
