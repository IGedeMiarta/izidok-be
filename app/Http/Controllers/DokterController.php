<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Dokter;
use App\User;
use App\Operator;
use App\Constant;
use App\KlinikDokter;
use Illuminate\Support\Facades\Hash;

class DokterController extends Controller
{
  /**
   * Display a listing of the resource.
   *
   * @return Response
   */
  public function index(Request $request)
  {
    $user_id = $request->user_id;
    $user = User::find($user_id);

    if ($user->hasRole(Constant::SUPER_ADMIN)) {
      $dokter = Dokter::paginate($request->limit);
      $data['dokter'] = $dokter;
      return response()->json([
        'success' => true,
        'message' => 'success',
        'data' => $data
      ], 201);
    }
    $operator = Operator::where('user_id', $user_id)->first();
    $dokter = KlinikDokter::where('klinik_id', $operator->klinik_id)->with('dokter')->paginate();
    $data['dokter'] = $dokter;
    return response()->json([
      'success' => true,
      'message' => 'success',
      'data' => $data
    ], 201);

  }

  /**
   * Store a newly created resource in storage.
   *
   * @return Response
   */
  public function store(Request $request)
  {
    $this->validate($request, [
      'username' => 'required|unique:users|string',
      'nama' => 'required|string',
      'email' => 'required|unique:users|email',
      'password' => 'required|min:8',
      'nomor_telp' => 'required|regex:/^([0-9\s\-\+\(\)]*)$/|min:8|max:12',
    ]);

    $user = new User();
    $user->nama = $request->input('nama');
    $user->username = $request->input('username');
    $user->password = Hash::make($request->input('password'));
    $user->email = $request->input('email');
    $user->nomor_telp = $request->input('nomor_telp');
    $user->save();
    $user->assignRole(Constant::DOKTER_KLINIK);

    $dokter = new Dokter();
    $dokter->nama = $request->input('nama');
    $dokter->user_id = $user->id;
    $status = $dokter->save();

    if ($status) {
      return response()->json([
        'success' => true,
        'message' => 'success',
        'data' => $user
      ], 201);
    } else {
      return response()->json([
        'success' => false,
        'message' => 'failed',
        'data' => ''
      ], 400);
    }
  }

  /**
   * Display the specified resource.
   *
   * @param  int  $id
   * @return Response
   */
  public function show(Request $request)
  {
    $dokter = Dokter::find($request->id);
    if (empty($dokter)) {
      return response()->json([
        'status' => false,
        'message' => "Dokter not found",
        'data' => ''
      ]);
    } else {
      return response()->json([
        'status' => true,
        'data' => $dokter,
        'message' => 'success'
      ]);
    }
  }

  /**
   * Update the specified resource in storage.
   *
   * @param  int  $id
   * @return Response
   */
  public function update(Request $request)
  {
    //echo $request->id;
    $dokter = Dokter::find($request->id);

    if (empty($dokter)) {
      return response()->json([
        'status' => false,
        'message' => "dokter not found",
        'data' => ''
      ]);
    } else {
      $dokter->nama = $request->nama;
      $user = User::find($dokter->user_id);
      $user->nama = $request->nama;
      $user->save();
      $dokter->save();
      return response()->json([
        'status' => true,
        'data' => $dokter,
        'message' => 'success'
      ]);
    }
  }

  /**
   * Remove the specified resource from storage.
   *
   * @param  int  $id
   * @return Response
   */
  public function delete($id = null)
  {
    $dokter = Dokter::find($id);

    if (empty($dokter)) {
      return response()->json([
        'status' => false,
        'data' => '',
        'message' => 'dokter not found'
      ]);
    } else {
      $nama = $dokter->nama;
      $user = User::find($dokter->user_id);
      $dokter->delete();
      $user->delete();
      return response()->json([
        'status' => true,
        'message' => 'Dokter \'' . $nama . '\' has been deleted'
      ]);
    }
  }
}
