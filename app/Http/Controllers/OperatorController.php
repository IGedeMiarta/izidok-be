<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Operator;
use App\User;
use App\UserRole;
use App\Constant;
use App\Reference;
use App\Activation;
use Illuminate\Support\Facades\Hash;

class OperatorController extends Controller
{
  /**
   * Display a listing of the resource.
   *
   * @return Response
   */
  public function index(Request $request)
  {
    $user = User::find($request->user_id);

    if ($user->hasRole(Constant::SUPER_ADMIN)) {
      $operator = Operator::all()->paginate($request->limit);
      $data['operator'] = $operator;
      return response()->json([
        'success' => true,
        'message' => 'success',
        'data' => $data
      ], 201);
    }

    $operator = Operator::wherehas('user', function ($data) use ($user) {
      $data->where('users.klinik_id', $user->klinik_id);
    })->get();

    $data['operator'] = $operator;

    if (!$operator) {
      return response()->json([
        'success' => false,
        'message' => 'operator not found...',
      ], 201);
    }

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
      'nama' => 'required|string',
      'email' => 'required|unique:users|email'
    ]);

    $logged_user = User::find($request->user_id);

    $user = new User();
    $user->nama = $request->input('nama');
    $user->email = $request->input('email');
    $user->klinik_id = $logged_user->klinik_id;
    $user->save();
    $user->assignRole(Constant::OPERATOR);

    $operator = new Operator();
    $operator->nama = $request->input('nama');
    $operator->user_id = $user->id;
    $operator->created_by = $request->user_id;
    $operator->save();

    $activation = new Activation();
    $activation->token = base64_encode(str_random(30));
    $activation->user_id = $user->id;
    $activation->expired_at = date('Y-m-d H:i:s', strtotime('+7 days'));
    $activation->save();

    $data['user'] = $user;
    $data['operator'] = $operator;
    $data['activation'] = $activation;

    $act_url = url(env('APP_PREFIX', 'api/v1') . '/operator/check/' . $activation->token);

    $email_data = [
      'subject' => 'Operator Activation',
      'message' => 'Click link below to activate operator: \n ' . $act_url,
      'activation_url' => $act_url,
      'to' => ['helmysmp@gmail.com', $user->email],
      'from' => 'izidok.dev@gmail.com',
      'nama' => $user->nama,
    ];

    if (\sendEmail($email_data, Constant::OPERATOR_EMAIL_TEMPLATE)) {
      return response()->json([
        'status' => true,
        'message' => 'aktivasi telah dibuat',
        'data' => $data
      ]);
    }
  }

  public function check_activation($token)
  {
    // echo $token;
    $activation = Activation::where('token', $token)->first();

    if (empty($activation)) {
      $key = Constant::ACT_OPT_INVALID;
      $category = Constant::REDIRECTION;

      $config = Reference::where('key', $key)
        ->where('category', $category)->first();
      $data['url'] = $config->value;

      return response()->json([
        'status' => false,
        'message' => 'activation not found',
        'data' => $data
      ]);
    } else if (strtotime(date('Y-m-d H:i:s')) > strtotime($activation->expired_at)) {
      $key = Constant::ACT_OPT_INVALID;
      $category = Constant::REDIRECTION;
      $config = Reference::where('key', $key)
        ->where('category', $category)->first();
      $data['url'] = $config->value;

      return redirect($config->value);
    } else {
      $key = Constant::ACT_OPT_VALID;
      $category = Constant::REDIRECTION;
      $config = Reference::where('key', $key)
        ->where('category', $category)->first();
      $data['url'] = $config->value;
      $data['token'] = $token;

      return redirect($config->value . $token);
    }
  }

  public function activation(Request $request)
  {
    $this->validate($request, [
      'username' => 'required|unique:users|string',
      'password' => 'required|string',
      'konfirm_password' => 'required|string',
      'telepon' => 'required|string',
      'tanggal_lahir' => 'required|date',
      'jenis_kelamin' => 'required|string',
      'token' => 'required|string'
    ]);

    $token = $request->token;
    $activation = Activation::where('token', $token)->first();

    $username = $request->input('username');
    $password = $request->input('password');
    $konfirm_password = $request->input('konfirm_password');
    $telepon = $request->input('telepon');
    $tanggal_lahir = $request->input('tanggal_lahir');
    $jenis_kelamin = $request->input('jenis_kelamin');

    if (empty($activation)) {
      return response()->json([
        'status' => false,
        'message' => 'activation not found'
      ]);
    }
    if ($password != $konfirm_password) {
      return response()->json([
        'status' => false,
        'message' => 'password dan konfirm passowrd tidak sama'
      ]);
    } else if (strtotime(date('Y-m-d H:i:s')) > strtotime($activation->expired_at)) {
      return response()->json([
        'status' => false,
        'message' => 'expired'
      ]);
    } else {
      $user = User::find($activation->user_id);
      $user->password = Hash::make($password);
      $user->username = $username;
      $user->nomor_telp = $telepon;
      $user->save();

      $operator = Operator::where('user_id', $user->id)->first();
      $operator->tanggal_lahir = $tanggal_lahir;
      $operator->jenis_kelamin = $jenis_kelamin;
      $operator->save();

      $activation->status = 1;
      $activation->save();
      // $activation->delete();
      return response()->json([
        'status' => true,
        'message' => 'Account has been active'
      ]);
    }
  }

  public function show(Request $request)
  {
    $operator = Operator::find($request->id);

    if (empty($operator)) {
      return response()->json([
        'status' => false,
        'message' => "operator not found",
        'data' => ''
      ]);
    }

    $user = User::find($operator->user_id);
    $data['operator'] = $operator;
    $data['user'] = $user;

    return response()->json([
      'status' => true,
      'data' => $data,
      'message' => 'success'
    ]);
  }

  /**
   * Update the specified resource in storage.
   *
   * @param  int  $id
   * @return Response
   */
  public function update(Request $request)
  {
    $this->validate($request, [
      'nama' => 'required|string',
      'nomor_telp' => 'required|string',
      'tanggal_lahir' => 'required|date',
      'jenis_kelamin' => 'required|string',
    ]);

    $operator = Operator::find($request->id);

    if (empty($operator)) {
      return response()->json([
        'status' => false,
        'message' => "operator not found",
        'data' => ''
      ]);
    } else {
      $user = User::find($operator->user_id);
      $user->nama = $request->nama;
      $user->nomor_telp = $request->nomor_telp;
      $user->save();
      $operator->nama = $request->nama;
      $operator->tanggal_lahir = $request->tanggal_lahir;
      $operator->jenis_kelamin = $request->jenis_kelamin;
      $operator->save();
      return response()->json([
        'status' => true,
        'data' => $operator,
        'message' => 'success'
      ]);
      // print_r($user);
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
    $operator = Operator::find($id);

    if (empty($operator)) {
      return response()->json([
        'status' => false,
        'data' => '',
        'message' => 'operator not found'
      ]);
    } else {
      $nama = $operator->nama;
      $user = User::find($operator->user_id);
      $operator->delete();
      $user->delete();
      return response()->json([
        'status' => true,
        'message' => 'Operator \'' . $nama . '\' has been deleted'
      ]);
    }
  }
}
