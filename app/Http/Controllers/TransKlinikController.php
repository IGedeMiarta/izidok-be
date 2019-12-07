<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\TransKlinik;
use App\Anamnesa;
use App\Constant;
use App\Pasien;
use App\Klinik;
use App\Dokter;
use App\Operator;
use App\User;

// use Carbon\Carbon;

class TransKlinikController extends Controller
{
  public function index(Request $request)
  {

    $this->validate($request, [
      'status' => 'required|string',
      'from' => 'required|date_format:Y-m-d',
      'to' => 'required|date_format:Y-m-d',
    ]);

    $status = $request->status;
    $from = $request->from;
    $to = $request->to;

    $user = User::find($request->user_id);

    if ($user->hasRole(Constant::SUPER_ADMIN)) {
      $trans_klinik = TransKlinik::where('status', $status)
        ->whereBetween('created_at',  [$from, $to])
        ->paginate($request->limit);
      $data['trans_klinik'] = $trans_klinik;

      return response()->json([
        'success' => true,
        'message' => 'success',
        'data' => $data
      ], 201);
    }

    $trans_klinik = TransKlinik::where('created_by', $user->id)
      ->where('status', $status)
      ->whereBetween('created_at',  [$from, $to])->paginate($request->limit);
    $data['trans_klinik'] = $trans_klinik;


    if (!$trans_klinik) {
      return response()->json(['status' => false]);
    } else {
      return response()->json(['status' => true, 'data' => $trans_klinik]);
    }
  }

  public function store(Request $request)
  {
    $this->validate($request, [
      'pasien_id' => 'required|integer',
      'klinik_id' => 'required|integer',
      'examination_by' => 'required|integer',
      'nomor_rekam_medis' => 'required|string',

      'nama_lengkap' => 'required|string',
      'nik' => 'required|string',
      'jenis_kelamin' => 'required|integer|min:0|max:1',
      'nomor_telp' => 'regex:/^([0-9\s\-\+\(\)]*)$/|min:8|max:12',
      'waktu_konsultasi' => 'required|string',
      'tinggi_badan' => 'integer',
      'berat_badan' => 'integer',
      'suhu' => 'integer',
      'tensi_sistole' => 'integer',
      'tensi_diastole' => 'integer',
      'nadi' => 'integer',
      'respirasi' => 'integer',
    ]);

    #klinik exist?
    if (!Klinik::find($request->klinik_id))
      return response()->json(['status' => false, 'message' => 'Klinik not found...'], 422);

    #dokter exist?
    if (!User::find($request->examination_by))
      return response()->json(['status' => false, 'message' => 'Dokter not found...'], 422);

    #pasien exist?
    if (!Pasien::find($request->pasien_id))
      return response()->json(['status' => false, 'message' => 'Pasien not found...'], 422);

    #transaksi
    $trans_klinik = new TransKlinik;
    $trans_klinik->examination_by = $request->examination_by;
    $trans_klinik->pasien_id = $request->pasien_id;
    $trans_klinik->klinik_id = $request->klinik_id;
    $trans_klinik->created_by = $request->user_id;
    $trans_klinik->nomor_antrian = $this->getNextOrderNumber($request->klinik_id);
    $trans_klinik->waktu_konsultasi = $request->waktu_konsultasi;
    $trans_klinik->status = Constant::QUEUED;
    $trans_klinik->save();

    #update pasien
    $pasien = Pasien::find($request->pasien_id)->first();
    if ($pasien) {
      $pasien->tinggi_badan = $request->tinggi_badan;
      $pasien->berat_badan = $request->berat_badan;
      $pasien->suhu = $request->suhu;
      $pasien->tensi_sistole = $request->tensi_sistole;
      $pasien->tensi_diastole = $request->tensi_diastole;
      $pasien->nadi = $request->nadi;
      $pasien->respirasi = $request->respirasi;
      $pasien->save();
    }

    if (!$trans_klinik) {
      return response()->json(['status' => false], 422);
    }

    return response()->json([
      'status' => true,
      'message' => 'data transaction has been saved successfully',
      'data' => $trans_klinik
    ]);
  }

  public function show($id)
  {
    $trans_klinik = TransKlinik::all()->find($id);
    if (!$trans_klinik) {
      return response()->json(['status' => false, 'message' => 'Rawat Jalan not found...']);
    } else {
      return response()->json(['status' => true, 'data' => $trans_klinik]);
    }
  }

  public function update(Request $request)
  {

    $this->validate($request, [
      'status' => 'required',
    ]);

    $trans_klinik = TransKlinik::find($request->id);
    if (!$trans_klinik) {
      return response()->json(['status' => false, 'message' => 'Rawat Jalan not found...']);
    } else {
      $trans_klinik->status = $request->status;
      $trans_klinik->examination_at = date('Y-m-d H:m:s');
      $trans_klinik->save();
      return response()->json(['status' => true, 'data' => $trans_klinik]);
    }
  }

  public function delete($id = null)
  {
    $trans_klinik = TransKlinik::find($id);

    if (!$trans_klinik) {
      return response()->json(['status' => false, 'message' => 'Rawat Jalan not found...']);
    } else {
      $trans_klinik_id = $trans_klinik->id;
      $trans_klinik->delete();
      return response()->json(['status' => true, 'message' => 'Rawat Jalan ID \'' . $trans_klinik_id . '\' has been deleted']);
    }
  }

  public function getNextOrderNumber($klinik_id)
  {
    $trans_klinik = TransKlinik::where('klinik_id', $klinik_id)
      ->orderBy('created_at', 'desc')->first();

    $number = 1;

    if (!$trans_klinik) {
      return $number;
    }

    if ($trans_klinik->created_at->isToday()) {
      $number = $trans_klinik->nomor_antrian;
      return ($number + 1);
    }

    return $number;
  }
}
