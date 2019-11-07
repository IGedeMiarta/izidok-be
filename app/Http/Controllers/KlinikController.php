<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Auth;
use App\Klinik;
use App\Operator;
use App\KlinikOperator;
use App\User;
use App\UserRole;
use App\Constant;
use App\Dokter;
use App\Activation;


class KlinikController extends Controller
{
    public function __construct()
    {
        //nothing
    }

    public function index(Request $request){
        $user_id = Auth::user()->id;
        $klinik = Klinik::whereHas('operators', function($q) use($user_id) {
                        $q->where('operator.user_id', $user_id);
                    })->paginate($request->limit);

        $data['klinik'] = $klinik;
        
        if ($data['klinik'] == null) {
            return response()->json(['status' => false], 422);
        }else{
            return response()->json(['status' => true, 'data' => $data]);
        }
    }

    public function show($id = null){
        $klinik = Klinik::with('operators')->find($id);
        if ($klinik == null) {
            return response()->json(['status' => false], 422);
        }else{
            return response()->json(['status' => true, 'data' => $klinik]);
        }
    }

    public function store(Request $request){
         $rules = [
            'tipe_faskes' => 'required|min:1:max:2',
            'nama_klinik' => 'required|string',
            'nomor_telp' => 'required|regex:/^([0-9\s\-\+\(\)]*)$/|min:8|max:12',
            'email' => 'required|unique:users|email',
            'nomor_ijin' => 'string',
            'password' => 'required|confirmed|min:8'
        ];
        
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
            'nomor_telp' => $request->nomor_telp
        ]);
   
        #data user
        $user = User::create([
    		"username" => $request->email,
    		"email" => $request->email,
    		"password" => app('hash')->make($request->password),
    		"nama" => $nama_pic,
    		"no_telp" => $request->nomor_telp
        ]);
        $user->roles()->attach(Constant::KLINIK_OPERATOR);

        if(!$isKlinik){
            #data dokter
            $dokter = new Dokter([
                'nama' => $nama_dokter,
                'user_id' => $user->id
            ]);
            $klinik->dokters()->save($dokter);
        }else{
            #data operator
            $operator = new Operator([
                'nama' => $nama_pic,
                'user_id' => $user->id
            ]);
            $klinik->operators()->save($operator);
        }

        #activation token
        $activation = new Activation();
        $activation->token = base64_encode(str_random(40));
        $activation->user_id = $user->id;
        $activation->expired_at = date('Y-m-d H:i:s', strtotime('+7 days'));
        $activation->save();

        #send activation via email here...
        // Mail::raw('Message here...', function($msg) use ($request){ 
        //     $msg->subject('Hi '.$request->nama_klinik.', please verify your Klinik account'); 
        //     $msg->to([$request->email]); 
        //     $msg->from(['izi-dok@gmail.com']); });
        

        $data['klinik_id'] = $klinik->id;
        $data['activation_url'] = url('/api/v1/activate/'.$activation->token);

        if(!$data['klinik_id']){
            return response()->json(['status' => false], 422);
        }

        return response()->json(['status' => true, 'data' => $data]);
    }

    public function update(Request $request){
        $klinik = Klinik::find($request->id);
        if ($klinik == null) {            
            return response()->json(['status' => false]);
        }else{
            $klinik->nama_klinik = $request->nama_klinik;
            $klinik->save();
            return response()->json(['status' => true, 'data' => $klinik]);
        }
    }
    
    public function delete($id = null){
        $klinik = Klinik::find($id);

        if ($klinik == null) {            
            return response()->json(['status' => false]);
        }else{
            $nama = $klinik->nama_klinik;
            $klinik->delete();
            return response()->json(['status' => true, 'message' => 'Klinik \''.$nama.'\' has been deleted']);
        }
    }
}
