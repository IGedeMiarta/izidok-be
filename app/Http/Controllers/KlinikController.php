<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Klinik;
use App\Operator;
use App\KlinikOperator;

class KlinikController extends Controller
{
    public function __construct()
    {
        //nothing
    }

    public function getKlinik(){
        $klinik = Klinik::all();
        if ($klinik === null) {
            return response()->json(['status' => false]);
        }else{
            return response()->json(['status' => true, 'data' => $klinik]);
        }

    }

    public function getKlinikById($id = null){
        $klinik = Klinik::find($id);
        if ($klinik === null) {
            return response()->json(['status' => false]);
        }else{
            return response()->json(['status' => true, 'data' => $klinik]);
        }
    }

    public function saveKlinik(Request $request){
        #save klinik
        $klinik = new Klinik;
        $klinik->nama_klinik = $request->nama_klinik;
        $klinik->nama_pic = $request->nama_pic;
        $klinik->nomor_hp = $request->nomor_hp;
        $klinik->save();

        #save operator
        $operator = new Operator;
        $operator->nama = $request->nama_pic;
        $operator->save();

        #save klinik-operator
        $klinikOperator = new KlinikOperator;
        $klinik->klinik_id = $klinik->id;
        $klinik->operator_id = $operator->id;
        $klinikOperator->save();

        return response()->json(['status' => true, 'data' => $request]);
    }
    
    public function deleteKlinik($id = null){
        $klinik = Klinik::find($id);
        $nama = $klinik->nama;
        $klinik->softDelete();
        return response()->json(['status' => true, 'msg' => 'Klinik '.$nama.' has been deleted']);
    }
}
