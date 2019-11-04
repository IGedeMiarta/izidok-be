<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Klinik;
use App\Operator;
use App\KlinikOperator;
use App\User;
use App\UserRole;
use App\Constant;


class KlinikController extends Controller
{
    public function __construct()
    {
        //nothing
    }

    public function index(){
        $klinik = Klinik::with('operators')->get();
        if ($klinik === null) {
            return response()->json(['status' => false]);
        }else{
            return response()->json(['status' => true, 'data' => $klinik]);
        }
    }

    public function show($id = null){
        $klinik = Klinik::with('operators')->find($id);
        if ($klinik === null) {
            return response()->json(['status' => false]);
        }else{
            return response()->json(['status' => true, 'data' => $klinik]);
        }
    }

    public function store(Request $request){
        $this->validate($request, [
            'nama_klinik' => 'required|string',
            'nama_pic' => 'required|string',
            'nomor_hp' => 'required|regex:/^([0-9\s\-\+\(\)]*)$/|min:8|max:10',
            'email' => 'required|string|email',
            'password' => 'required'
        ]);

        #data klinik
        $klinik = Klinik::create([
            'nama_klinik' => $request->nama_klinik,
            'nama_pic' => $request->nama_pic,
            'nomor_hp' => $request->nomor_hp
        ]);
        
        #data operator
        $operator = new Operator([
            'nama' => $request->nama_pic
        ]);
        $klinik->operators()->save($operator);
   
        #data user
        $user = User::create([
    		"username" => $request->email,
    		"email" => $request->email,
    		"password" => app('hash')->make($request->password),
    		"nama_lengkap" => $request->nama_lengkap,
    		"no_telp" => $request->nomor_hp
        ]);
        $user->roles()->attach(Constant::KLINIK_OPERATOR);

        #send email right here...

        $data['klinik_id'] = $klinik->id;
        return response()->json(['status' => true, 'data' => $data]);
    }

    public function update(Request $request){
        $klinik = Klinik::find($request->id);
        if ($klinik === null) {            
            return response()->json(['status' => false]);
        }else{
            $klinik->nama_klinik = $request->nama_klinik;
            $klinik->save();
            return response()->json(['status' => true, 'data' => $klinik]);
        }
    }
    
    public function delete($id = null){
        $klinik = Klinik::find($id);

        if ($klinik === null) {            
            return response()->json(['status' => false]);
        }else{
            $nama = $klinik->nama_klinik;
            $klinik->delete();
            return response()->json(['status' => true, 'message' => 'Klinik \''.$nama.'\' has been deleted']);
        }
    }
}
