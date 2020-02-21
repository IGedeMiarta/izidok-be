<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Operator;
use App\User;
use App\Constant;
use App\Reference;
use App\Activation;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class OperatorController extends Controller
{
  public $user;

	public function __construct(){
		$this->user = Auth::user();
  }

  public function index(Request $request)
  {
    $user = $this->user;
    $operator = new Operator;

    if (!$user->hasRole(Constant::SUPER_ADMIN)) {
      $operator = $operator->wherehas('user', function ($data) use ($user) {
        $data->where('users.klinik_id', $user->klinik_id);
      });
    }

    $operator = $operator->paginate($request->limit);

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
      'data' => $data,
    ], 201);
  }

  public function store(Request $request)
  {
    $this->validate($request, [
      'nama' => 'required|string',
      'email' => 'required|email',
      'nomor_telp' => 'required|regex:/^([0-9\s\-\+\(\)]*)$/|min:8|max:15',
      'password' => 'required|confirmed|min:6',
    ]);

     #cek email
     $email = User::where('email', $request->email)->exists();
     if ($email) {
         return response()->json(['status' => false, 'message' => 'email is already in used!']);
     }

     #cek nomor_telp
     $nomor_telp = User::where('nomor_telp', $request->nomor_telp)->exists();
     if ($nomor_telp) {
         return response()->json(['status' => false, 'message' => 'no handphone is already in used!']);
     }

    $logged_user = $this->user;

    $user = new User();
    $user->username = $request->email;
    $user->email = $request->email;
    $user->password = Hash::make($request->password);
    $user->nama = $request->nama;
    $user->nomor_telp = $request->nomor_telp;
    $user->is_first_login = 0;
    $user->klinik_id = $logged_user->klinik_id;
    $user->save();
    $user->assignRole(Constant::OPERATOR);

    $operator = new Operator();
    $operator->nama = $request->nama;
    $operator->user_id = $user->id;
    $operator->created_by = $request->user_id;
    $operator->save();

    /*$activation = new Activation();
    $activation->token = base64_encode(str_random(30));
    $activation->user_id = $user->id;
    $activation->status = 1;
    $activation->expired_at = date('Y-m-d H:i:s', strtotime('+7 days'));
    $activation->save();*/

    $data['user'] = $user;
    $data['operator'] = $operator;
    //$data['activation'] = $activation;

    //$act_url = url(env('APP_PREFIX', 'api/v1') . '/operator/check/' . $activation->token);*
    $current_user_email = User::select('email')->where('id', $request->user_id)->value('email');
    $current_user_name = User::select('nama')->where('id', $request->user_id)->value('nama');

    $email_data = [
      'subject' => 'Operator Login Data',
      'from' => 'posmaster@esindo.net',
      'to' => [$current_user_email],
      'doctor_name' => $current_user_name,
      'name' => $request->nama,
      'phone' => $user->nomor_telp,
      'email' => $user->email,
      'password' => $request->password,
    ];

    if (\sendEmail($email_data, Constant::OPERATOR_EMAIL_TEMPLATE)) {
      return response()->json([
        'status' => true,
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
    $user = $this->user;

    if ($user->cant('updateOrDelete', $operator)) {
			abort(403);
		}

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
    $user = $this->user;

    if ($user->cant('updateOrDelete', $operator)) {
			abort(403);
    }

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

  public function isUserExist($users)
  {
      if ($users) {
          foreach ($users as $item) {
              if ($item->activation->status == 1 || $item->activation->expired_at < date('Y-m-d H:i:s')) {
                  return true;
              }
          }
      }

      return false;
  }
}
