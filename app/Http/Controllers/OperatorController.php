<?php 

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Operator;
use App\User;
use App\Constant;
use Illuminate\Support\Facades\Hash;

class OperatorController extends Controller 
{
    /**
    * Display a listing of the resource.
    *
    * @return Response
    */
    public function index()
    {
      $all_operator = Operator::all();
        return response()->json([
              'success' => true,
              'message' => 'success',
              'data' => $all_operator
            ],201);
    }

    /**
    * Store a newly created resource in storage.
    *
    * @return Response
    */
    public function store(Request $request)
    {

      $user = new User();
      $user->nama = $request->input('nama');
      $user->username = $request->input('username');
      $user->password = Hash::make($request->input('password'));
      $user->email = $request->input('email');
      $user->save();

      $operator = new Operator();
      $operator->nama = $request->input('nama');
      $operator->user_id = $user->id;
      $status = $operator->save();

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
      if (empty($operator)) {            
          return response()->json([
            'status' => false,
            'message' => "operator not found",
            'data' => ''
          ]);
      }else{
          return response()->json([
            'status' => true,
            'data' => $operator,
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