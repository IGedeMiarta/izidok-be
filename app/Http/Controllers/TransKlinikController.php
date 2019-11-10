<?php 

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\TransKlinik;

class TransKlinikController extends Controller 
{
  public function index()
  {
    $trans_klinik = TransKlinik::all()->paginate($request->limit);
        
        if (!$trans_klinik) {
            return response()->json(['status' => false]);
        }else{
            return response()->json(['status' => true, 'data' => $trans_klinik]);
        }
  }

  public function store(Request $request)
  {
    $this->validate($request,[
      'no_rekam_medis' => 'required|unique:rekam_medis|integer',
      'no_ktp' => 'required|string',
      'pasien_id' => 'required|pasien',
      'jenis_kelamin' => 'required|integer|min:0|max:1',
      'nomo_telp' => 'required|regex:/^([0-9\s\-\+\(\)]*)$/|min:8|max:12',
      'dokter_id' => 'required|dokter',
      'waktu_konsultasi' => 'required|string',

      'tinggi_badan' => 'integer',
      'berat_badan' => 'integer',
      'suhu_badan' => 'integer',
      'sistole' => 'integer',
      'diastole' => 'integer',
      'nadi' => 'integer',
    ]);

      $trans_klinik = new TransKlinik;
      $trans_klinik->klinik_dokter_id = $request->trans_dokter_id;
      $trans_klinik->pasien_id = $request->pasien_id;
      $trans_klinik->nomor_antrian = $request->nomor_antrian;
      $trans_klinik->klinik_operator_id = $request->klinik_operator_id;
      $trans_klinik->klinik_id = $requst->klinik_id;
      $trans_klinik->save();

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

  public function update($id)
  {
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