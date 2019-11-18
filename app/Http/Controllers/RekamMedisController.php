<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\RekamMedis;

class RekamMedisController extends Controller
{
    public function index(Request $request)
  	{

  		$rekam_medis = RekamMedis::join('trans_klinik', function($join)){
  			$join->on('trans_klinik_id','=','trans_klinik.id')
  				->where('pasien_id','=',$request->pasien_id);
  		})->get();
		
  	}

	  /**
   	* Store a newly created resource in storage.
   	*
   	* @return Response
   	*/
  	public function store(Request $request)
  	{
  		$this->validate($request, [
            'role' => 'required|string'
        ]);

	   	$role = new Role();
	   	$role->role = $request->input('role');
	   	$status = $role->save();

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
