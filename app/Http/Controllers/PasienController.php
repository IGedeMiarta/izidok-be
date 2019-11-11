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
            'status_perkawinan' => 'required|string',
            'pekerjaan' => 'required|string',
            'nomor_hp' => 'required|string',
            'nama_penjamin' => 'required|string',
            'nomor_polis' => 'required|string',
            'email' => 'required|string',
            'nama_penanggung_jawab' => 'required|string',
            'tensi' => 'required|integer',
            'nadi' => 'required|integer',
            'suhu' => 'required|integer',
            'respirasi' => 'required|integer',
            'tinggi_badan' => 'required|integer',
            'berat_badan' => 'required|integer',
            'nomor_pasien' => 'required|integer',
            'user_id' => 'required|integer',
            'klinik_id' => 'required|integer',
        ]);

	   	$pasien = new Pasien();
	   	$pasien->nama = $request->input('nama');
	   	$pasien->nik = $request->input('nik');
	   	$pasien->tempat_lahir = $request->input('tempat_lahir');
	   	$pasien->tanggal_lahir = $request->input('tanggal_lahir');
	   	$pasien->jenis_kelamin = $request->input('jenis_kelamin');
	   	$pasien->golongan_darah = $request->input('golongan_darah');
	   	$pasien->alamat_rumah = $request->input('alamat_rumah');
	   	$pasien->rt = $request->input('rt');
	   	$pasien->rw = $request->input('rw');
	   	$pasien->kelurahan = $request->input('kelurahan');
	   	$pasien->kecamatan = $request->input('kecamatan');
	   	$pasien->status_perkawinan = $request->input('status_perkawinan');
	   	$pasien->pekerjaan = $request->input('pekerjaan');
	   	$pasien->nomor_hp = $request->input('nomor_hp');
	   	$pasien->nama_penjamin = $request->input('nama_penjamin');
	   	$pasien->nomor_polis = $request->input('nomor_polis');
	   	$pasien->email = $request->input('email');
	   	$pasien->nama_penanggung_jawab = $request->input('nama_penanggung_jawab');
	   	$pasien->tensi = $request->input('tensi');
	   	$pasien->nadi = $request->input('nadi');
	   	$pasien->suhu = $request->input('suhu');
	   	$pasien->respirasi = $request->input('respirasi');
	   	$pasien->tinggi_badan = $request->input('tinggi_badan');
	   	$pasien->berat_badan = $request->input('berat_badan');
	   	$pasien->nomor_pasien = $request->input('nomor_pasien');
	   	$pasien->user_id = $request->input('user_id');
	   	$pasien->klinik_id = $request->input('klinik_id');
	   	$status = $pasien->save();

	   	if($status)
	   	{
	   		return response()->json([
	    			'success' => true,
	    			'message' => 'success',
	    			'data' => $pasien
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
	    $pasien = Pasien::find($request->id);
	    if (empty($pasien)) {            
	        return response()->json([
	        	'status' => false,
	        	'message' => "pasien not found",
	        	'data' => ''
	        ]);
	    }else{
	        return response()->json([
	        	'status' => true,
	        	'data' => $pasien,
	        	'message' => 'success'
	        ]);
	    }
  	}

  	public function update(Request $request)
  	{
    	$pasien = Pasien::find($request->id);
    	
	    if (empty($pasien)) {            
	        return response()->json([
	        	'status' => false,
	        	'message' => "pasien not found",
	        	'data' => ''
	        ]);
	    }else{
	    	$pasien->nama = $request->input('nama');
		   	$pasien->nik = $request->input('nik');
		   	$pasien->tempat_lahir = $request->input('tempat_lahir');
		   	$pasien->tanggal_lahir = $request->input('tanggal_lahir');
		   	$pasien->jenis_kelamin = $request->input('jenis_kelamin');
		   	$pasien->golongan_darah = $request->input('golongan_darah');
		   	$pasien->alamat_rumah = $request->input('alamat_rumah');
		   	$pasien->rt = $request->input('rt');
		   	$pasien->rw = $request->input('rw');
		   	$pasien->kelurahan = $request->input('kelurahan');
		   	$pasien->kecamatan = $request->input('kecamatan');
		   	$pasien->status_perkawinan = $request->input('status_perkawinan');
		   	$pasien->pekerjaan = $request->input('pekerjaan');
		   	$pasien->nomor_hp = $request->input('nomor_hp');
		   	$pasien->nama_penjamin = $request->input('nama_penjamin');
		   	$pasien->nomor_polis = $request->input('nomor_polis');
		   	$pasien->email = $request->input('email');
		   	$pasien->nama_penanggung_jawab = $request->input('nama_penanggung_jawab');
		   	$pasien->tensi = $request->input('tensi');
		   	$pasien->nadi = $request->input('nadi');
		   	$pasien->suhu = $request->input('suhu');
		   	$pasien->respirasi = $request->input('respirasi');
		   	$pasien->tinggi_badan = $request->input('tinggi_badan');
		   	$pasien->berat_badan = $request->input('berat_badan');
		   	$pasien->nomor_pasien = $request->input('nomor_pasien');
		   	$pasien->user_id = $request->input('user_id');
		   	$pasien->klinik_id = $request->input('klinik_id');
		   	$status = $pasien->save();
	        return response()->json([
	        	'status' => true,
	        	'data' => $pasien,
	        	'message' => 'success'
	        ]);
	    }
  	}

  	public function delete($id = null)
  	{
    	$pasien = Pasien::find($id);

        if (empty($pasien)) {
            return response()->json([
            	'status' => false,
            	'data' => '',
            	'message' => 'pasien not found'
            ]);
        }else{
            $nama = $pasien->nama;
            $pasien->delete();
            return response()->json([
            	'status' => true, 
            	'message' => 'Pasien \''.$nama.'\' has been deleted'
            ]);
        }
  	}
}
