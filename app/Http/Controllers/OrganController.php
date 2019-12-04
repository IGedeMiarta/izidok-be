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
		], 201);
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
		} else {
			return response()->json([
				'status' => true,
				'data' => $organ,
				'message' => 'success'
			]);
		}
	}

	public function getByName(Request $request)
	{
		$organs = Organ::where('nama', 'like', '%' . $request->name . '%')->get();
		//echo $request->name;

		if (count($organs) == 0) {
			return response()->json([
				'status' => false,
				'message' => 'not found',
			], 200);
		} else {
			$data['organs'] = $organs;
			return response()->json([
				'status' => true,
				'message' => 'success',
				'data' => $data
			], 201);
		}
	}
}
