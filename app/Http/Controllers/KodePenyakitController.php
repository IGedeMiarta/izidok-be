<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\KodePenyakit;
use App\Constant;

class KodePenyakitController extends Controller
{
    /**
   * Display a listing of the resource.
   *
   * @return Response
   */
	public function index()
	{
		$all_kode = KodePenyakit::all();
	    return response()->json([
	    			'success' => true,
	    			'message' => 'success',
	    			'data' => $all_kode
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
            'kode' => 'required|string',
            'description' => 'required|string'
        ]);

	   	$kode_penyakit = new KodePenyakit();
	   	$kode_penyakit->kode = $request->input('kode');
      $kode_penyakit->description = $request->input('description');
	   	$status = $kode_penyakit->save();

	   	if($status)
	   	{
	   		return response()->json([
	    			'success' => true,
	    			'message' => 'success',
	    			'data' => $kode_penyakit
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
	    $kode_penyakit = KodePenyakit::find($request->id);
	    if (empty($kode_penyakit)) {            
	        return response()->json([
	        	'status' => false,
	        	'message' => "kode not found",
	        	'data' => ''
	        ]);
	    }else{
	        return response()->json([
	        	'status' => true,
	        	'data' => $kode,
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
    	$kode_penyakit = KodePenyakit::find($request->id);
    	
	    if (empty($kode_penyakit)) {            
	        return response()->json([
	        	'status' => false,
	        	'message' => "kode not found",
	        	'data' => ''
	        ]);
	    }else{
	    	$kode_penyakit->kode = $request->kode;
        $kode_penyakit->description = $request->description;
	    	$kode_penyakit->save();
	        return response()->json([
	        	'status' => true,
	        	'data' => $kode_penyakit,
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
    	$kode_penyakit = KodePenyakit::find($id);

        if (empty($kode_penyakit)) {
            return response()->json([
            	'status' => false,
            	'data' => '',
            	'message' => 'kode not found'
            ]);
        }else{
            $nama = $kode_penyakit->kode;
            $kode_penyakit->delete();
            return response()->json([
            	'status' => true, 
            	'message' => 'Kode Penyakit \''.$nama.'\' has been deleted'
            ]);
        }
  	}
}
