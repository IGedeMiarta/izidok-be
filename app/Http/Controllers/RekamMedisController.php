<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\RekamMedis;
use App\TransKlinik;
use App\Pasien;

class RekamMedisController extends Controller
{
    public function index(Request $request)
  	{
  		 $all_klinik = TransKlinik::where('pasien_id',$request->pasien_id)->with('rekan_medis')->get();
       print_r($all_klinik);
  	}

  	public function store(Request $request)
  	{
  		

	   	if($status)
	   	{
	   		return response()->json([
	    			'success' => true,
	    			'message' => 'success',
	    			'data' => $role
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
	    $role = Role::find($request->id);
	    if (empty($role)) {            
	        return response()->json([
	        	'status' => false,
	        	'message' => "role not found",
	        	'data' => ''
	        ]);
	    }else{
	        return response()->json([
	        	'status' => true,
	        	'data' => $role,
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
    	$role = Role::find($request->id);
    	
	    if (empty($role)) {            
	        return response()->json([
	        	'status' => false,
	        	'message' => "role not found",
	        	'data' => ''
	        ]);
	    }else{
	    	$role->role = $request->role;
	    	$role->save();
	        return response()->json([
	        	'status' => true,
	        	'data' => $role,
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
    	$role = Role::find($id);

        if (empty($role)) {
            return response()->json([
            	'status' => false,
            	'data' => '',
            	'message' => 'role not found'
            ]);
        }else{
            $nama = $role->role;
            $role->delete();
            return response()->json([
            	'status' => true, 
            	'message' => 'Role \''.$nama.'\' has been deleted'
            ]);
        }
  	}
}
