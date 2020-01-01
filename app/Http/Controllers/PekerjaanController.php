<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Pekerjaan;

class PekerjaanController extends Controller
{
    public function index()
	{
		$pekerjaan = Pekerjaan::all();
		$data['pekerjaan'] = $pekerjaan;
		return response()->json([
			'success' => true,
			'message' => 'success',
			'data' => $data
		], 200);
	}

	public function search(Request $request)
	{
		$pekerjaan = Pekerjaan::where('pekerjaan', 'LIKE', "%{$request->pekerjaan}%")->get();
		$data['pekerjaan'] = $pekerjaan;
		if(count($pekerjaan) != 0)
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
