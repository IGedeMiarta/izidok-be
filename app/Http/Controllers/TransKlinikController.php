<?php 

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\TransKlinik;
use App\Anamnesa;
use App\Constant;
use App\Pasien;

class TransKlinikController extends Controller 
{
  public function index(Request $request)
  {
    $trans_klinik = TransKlinik::paginate($request->limit);
        
        if (!$trans_klinik) {
            return response()->json(['status' => false]);
        }else{
            return response()->json(['status' => true, 'data' => $trans_klinik]);
        }
  }

  public function store(Request $request)
  {
    $this->validate($request,[
      'pasien_id' => 'required',
      'klinik_id' => 'required',
      'dokter_id' => 'required',
      'operator_id' => 'required',
      'nomor_rekam_medis' => 'required|unique:rekam_medis|integer',
      'no_ktp' => 'required|string',
      'jenis_kelamin' => 'required|integer|min:0|max:1',
      'nomor_telp' => 'regex:/^([0-9\s\-\+\(\)]*)$/|min:8|max:12',
      'waktu_konsultasi' => 'required|string',

      'tinggi_badan' => 'integer',
      'berat_badan' => 'integer',
      'suhu' => 'integer',
      'sistole' => 'integer',
      'diastole' => 'integer',
      'nadi' => 'integer',
    ]);

      #get operator from user
      

      #transaksi
      $trans_klinik = new TransKlinik;
      $trans_klinik->dokter_id = $request->dokter_id;
      $trans_klinik->pasien_id = $request->pasien_id;
      $trans_klinik->operator_id = $request->operator_id;
      $trans_klinik->klinik_id = $request->klinik_id;

      $trans_klinik->nomor_antrian = $request->nomor_antrian;
      $trans_klinik->waktu_konsultasi = $request->waktu_konsultasi;
      $trans_klinik->status = Constant::QUEUED;
      $trans_klinik->save();

      #update pasien
      $pasien = Pasien::find($request->pasien_id)->first();
      if($pasien){
        $pasien->tinggi_badan = $request->tinggi_badan;
        $pasien->berat_badan = $request->berat_badan;
        $pasien->suhu = $request->suhu;
        $pasien->tensi = $request->sistole;
        $pasien->nadi = $request->diastole;
        $pasien->respirasi = $request->respirasi;
        $pasien->save();
      }

      if(!$trans_klinik){
        return response()->json(['status' => false], 422);
      }
      
      return response()->json([ 'status' => true, 
                                'message' => 'data transaction has been saved successfully',
                                'data' => $trans_klinik]);

  }

  public function show($id)
  {
    $trans_klinik = TransKlinik::all()->find($id);
    if (!$trans_klinik) {
        return response()->json(['status' => false]);
    }else{
        return response()->json(['status' => true, 'data' => $trans_klinik]);
    }
  }

  public function update(Request $request)
  {

    $this->validate($request,[
      'status' => 'required',
    ]);

    $trans_klinik = TransKlinik::find($request->id);
      if (!$trans_klinik) {            
          return response()->json(['status' => false]);
      }else{
          $trans_klinik->status = $request->status;
          $trans_klinik->save();
          return response()->json(['status' => true, 'data' => $trans_klinik]);
      }
  }

  public function delete($id = null)
  {
    $trans_klinik = TransKlinik::find($id);

    if (!$trans_klinik) {            
        return response()->json(['status' => false]);
    }else{
        $trans_klinik_id = $trans_klinik->id;
        $trans_klinik->delete();
        return response()->json(['status' => true, 'message' => 'Transaksi ID \''.$trans_klinik_id.'\' has been deleted']);
    }
  }
  
}

?>