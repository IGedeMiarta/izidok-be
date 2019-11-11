<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Pasien;

class PasienController extends Controller
{
    public function index(Request $request)
  	{
  		$pasien = Pasien::paginate($request->limit);
      	$data['pasien'] = $pasien;
  	  	return response()->json([
  	    			'success' => true,
  	    			'message' => 'success',
  	    			'data' => $data
  	    		],201);
  	}

  	public function store(Request $request)
  	{
  		$this->validate($request, [
            'nama' => 'required|string',
            'nik' => 'required|string',
            'tempat_lahir' => 'required|string',
            'tanggal_lahir' => 'required|date',
            'jenis_kelamin' => 'required|string',
            'golongan_darah' => 'required|string',
            'alamat_rumah' => 'required|string',
            'rt' => 'required|string',
            'rw' => 'required|string',
            'kelurahan' => 'required|string',
            'kecamatan' => 'required|string',
            'nama' => 'required|string',
            'status_perkawinan' => 'required|string',
            'pekerjaan' => 'required|string',
            'nomor_hp' => 'required|string',
            'nama_penjamin' => 'required|string',
            'nomor_polis' => 'required|string',
            'email' => 'required|string',
            'nama_penanggung_jawab' => 'required|string',
            'tensi' => 'required|integer',
            'nadi' => 'required|integer'
            'suhu' => 'required|integer',
            'respirasi' => 'required|integer',
            'tinggi_badan' => 'required|integer',
            'berat_badan' => 'required|integer',
            'nomor_pasien' => 'required|integer',
            'user_id' => 'required|integer',
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
}
