<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Layanan;

class LayananController extends Controller
{
    public function index(Request $request)
  	{
  		$layanan = Layanan::paginate($request->limit);
      	$data['layanan'] = $layanan;
  	  	return response()->json([
  	    			'success' => true,
  	    			'message' => 'success',
  	    			'data' => $data
  	    		],201);
  	}

  	public function store(Request $request)
  	{
  		$this->validate($request, [
            'kode_layanan' => 'required|string',
            'nama_layanan' => 'required|string',
            'tarif' => 'required|integer',
            'klinik_id' => 'required|integer'
        ]);

	   	$layanan = new Layanan();
	   	$layanan->kode_layanan = $request->input('kode_layanan');
	   	$layanan->nama_layanan = $request->input('nama_layanan');
	   	$layanan->tarif = $request->input('tarif');
	   	$layanan->klinik_id = $request->input('klinik_id');
	   	$status = $layanan->save();

	   	$data['layanan'] = $layanan;

	   	if($status)
	   	{
	   		return response()->json([
	    			'success' => true,
	    			'message' => 'success',
	    			'data' => $data
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

  	public function show(Request $request)
  	{
	    $layanan = Layanan::find($request->id);
	    if (empty($layanan)) {            
	        return response()->json([
	        	'status' => false,
	        	'message' => "layanan not found",
	        	'data' => ''
	        ]);
	    }else{
	        return response()->json([
	        	'status' => true,
	        	'data' => $layanan,
	        	'message' => 'success'
	        ]);
	    }
  	}

  	public function update(Request $request)
  	{
  		//echo $request->id;
    	$layanan = Layanan::find($request->id);
    	
	    if (empty($layanan)) {            
	        return response()->json([
	        	'status' => false,
	        	'message' => "layanan not found",
	        	'data' => ''
	        ]);
	    }else{
	    	$layanan->nama_layanan = $layanan->nama_layanan;
	    	$layanan->kode_layanan = $layanan->kode_layanan;
	    	$layanan->tarif = $layanan->tarif;
	    	$layanan->klinik_id = $layanan->klinik_id;
	    	$layanan->save();
	        return response()->json([
	        	'status' => true,
	        	'data' => $layanan,
	        	'message' => 'success'
	        ]);
	    }
  	}

  	public function delete($id = null)
  	{
    	$layanan = Layanan::find($id);

        if (empty($layanan)) {
            return response()->json([
            	'status' => false,
            	'data' => '',
            	'message' => 'layanan not found'
            ]);
        }else{
            $nama = $layanan->nama_layanan;
            $layanan->delete();
            return response()->json([
            	'status' => true, 
            	'message' => 'Layanan \''.$nama.'\' has been deleted'
            ]);
        }
  	}


}
