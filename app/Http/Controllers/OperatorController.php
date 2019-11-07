<?php 

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Operator;
use App\User;
use App\UserRole;
use App\Constant;
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
      $operator = Operator::paginate($request->limit);
      $data['operator'] = $operator;
        return response()->json([
              'success' => true,
              'message' => 'success',
              'data' => $operator
            ],201);
    }

    /**
    * Store a newly created resource in storage.
    *
    * @return Response
    */
    public function store(Request $request)
    {
      $this->validate($request,[
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

      $operator = new Operator();
      $operator->nama = $request->input('nama');
      $operator->user_id = $user->id;
      $status = $operator->save();

      $user_role = new UserRole();
      $user_role->user_id = $user->id;
      $user_role->role_id = Constant::KLINIK_OPERATOR;
      $user_role->save();

      if($status)
      {
        return response()->json([
            'success' => true,
            'message' => 'success',
            'data' => $user
          ],201);
      }
      else
      {
        return response()->json([
            'success' => false,
            'message' => 'failed',
            'data' => ''
          ],400); 
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
      $operator = Operator::find($request->id);
      $user = User::find($operator->user_id);
      $data['operator'] = $operator;
      $data['user'] = $user;
      if (empty($operator)) {            
          return response()->json([
            'status' => false,
            'message' => "operator not found",
            'data' => ''
          ]);
      }else{
          return response()->json([
            'status' => true,
            'data' => $data,
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
      $operator = Operator::find($request->id);
      
      if (empty($operator)) {            
          return response()->json([
            'status' => false,
            'message' => "operator not found",
            'data' => ''
          ]);
      }else{
        $user = User::find($operator->user_id);
        $user->nama = $request->nama;
        $user->nomor_telp = $request->nomor_telp;
        $user->save();
        $operator->nama = $request->nama;
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
              'message' => 'role not found'
            ]);
        }else{
            $nama = $operator->nama;
            $user = User::find($operator->user_id);
            $operator->delete();
            $user->delete();
            return response()->json([
              'status' => true, 
              'message' => 'Operator \''.$nama.'\' has been deleted'
            ]);
        }
    }
  
}

?>