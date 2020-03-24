<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Provinsi;
use App\Kota;

class ProvinsiKotaController extends Controller
{
    public function getProvince()
	{
		$provinsi = Provinsi::all();
        $data['provinsi'] = $provinsi;

        return response()->json([
            'success' => true,
            'message' => 'success',
            'data' => $data,
        ], 200);
	}

	public function getCityByProvince($id)
	{
        $city = Kota::where('provinsi_id', $id)->get();
		$data['kota'] = $city;
		if(count($city) != 0)
		{
			return response()->json([
				'success' => true,
				'message' => 'success',
				'data' => $data
			], 200);
		}
		else
		{
			return response()->json([
				'success' => false,
				'message' => 'not found',
				'data' => ""
			], 404);
		}
	}
}
