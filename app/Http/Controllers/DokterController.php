<?php 

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Dokter;
use App\User;
use Illuminate\Support\Facades\Hash;

class DokterController extends Controller 
{
    /**
    * Display a listing of the resource.
    *
    * @return Response
    */
    public function index()
    {
      $all_dokter = Dokter::all();
        return response()->json([
              'success' => true,
              'message' => 'success',
              'data' => $all_dokter
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
            'password' => 'required|min:8'
        ]);

      $user = new User();
      $user->nama = $request->input('nama');
      $user->username = $request->input('username');
      $user->password = Hash::make($request->input('password'));
      $user->email = $request->input('email');
      $user->save();

      $dokter = new Dokter();
      $dokter->nama = $request->input('nama');
      $dokter->user_id = $user->id;
      $status = $dokter->save();

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
      $dokter = Dokter::find($request->id);
      if (empty($dokter)) {            
          return response()->json([
            'status' => false,
            'message' => "operator not found",
            'data' => ''
          ]);
      }else{
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
            'message' => "role not found",
            'data' => ''
          ]);
      }else{
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
        }else{
            $nama = $dokter->nama;
            $user = User::find($dokter->user_id);
            $dokter->delete();
            $user->delete();
            return response()->json([
              'status' => true, 
              'message' => 'Dokter \''.$nama.'\' has been deleted'
            ]);
        }
    }
  
}

?>