<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Organ;

class OrganController extends Controller
{
    public function index()
  	{
  		$organ = Organ::all();
		$data['organ'] = $organ;
	  	return response()->json([
	  	    			'success' => true,
	  	    			'message' => 'success',
	  	    			'data' => $data
	  	    		],201);
	  }
	  
	  public function show(Request $request)
  	{
	    $organ = Organ::find($request->id);
	    if (empty($organ)) {            
	        return response()->json([
	        	'status' => false,
	        	'message' => "Organ not found",
	        	'data' => ''
	        ]);
	    }else{
	        return response()->json([
	        	'status' => true,
	        	'data' => $organ,
	        	'message' => 'success'
	        ]);
	    }
  	}
}
