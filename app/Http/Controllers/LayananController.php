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
        $arr_layanan = $request->arr;
        $result = array();

        foreach ($arr_layanan as $layanan_obj) {
        	$layanan = new Layanan();
		   	$layanan->kode_layanan = $layanan_obj['kode_layanan'];
		   	$layanan->nama_layanan = $layanan_obj['nama_layanan'];
		   	$layanan->tarif = $layanan_obj['tarif'];
		   	$layanan->klinik_id = $layanan_obj['klinik_id'];
		   	$layanan->save();
		   	array_push($result,$layanan);
        }

	   	$data['layanan'] = $result;

	   	if(count($data['layanan']) > 0)
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
