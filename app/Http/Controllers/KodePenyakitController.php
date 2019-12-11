<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\KodePenyakit;
use App\Constant;
use Rap2hpoutre\FastExcel\FastExcel;

class KodePenyakitController extends Controller
{
	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index(Request $request)
	{
		$kode_penyakit = KodePenyakit::paginate($request->limit);
		$data['kode_penyakit'] = $kode_penyakit;
		return response()->json([
			'status' => true,
			'message' => 'success',
			'data' => $data
		], 201);
	}

	public function getByName(Request $request)
	{
		$kode_penyakit = KodePenyakit::where('description', 'like', '%' . $request->query('query') . '%')->get();

		if (count($kode_penyakit) == 0) {
			return response()->json([
				'status' => false,
				'message' => 'not found',
			], 200);
		} else {
			$data['kode_penyakit'] = $kode_penyakit;
			return response()->json([
				'status' => true,
				'message' => 'success',
				'data' => $data
			], 201);
		}
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store(Request $request)
	{
		$this->validate($request, [
			'kode' => 'required|string',
			'description' => 'required|string'
		]);

		$kode_penyakit = new KodePenyakit();
		$kode_penyakit->kode = $request->input('kode');
		$kode_penyakit->description = $request->input('description');
		$status = $kode_penyakit->save();

		if ($status) {
			return response()->json([
				'status' => true,
				'message' => 'success',
				'data' => $kode_penyakit
			], 201);
		} else {
			return response()->json([
				'status' => false,
				'message' => 'failed',
				'data' => ''
			], 400);
		}
	}

	public function store_excel(Request $request)
	{
		$collection = (new FastExcel)->import($request->file);
		foreach ($collection as $record) {
			$line = $this->checkData($record);
			$kode_penyakit = new KodePenyakit();
			$kode_penyakit->kode = $line['kode'];
			$kode_penyakit->description = $line['description'];
			$kode_penyakit->save();
		}

		if ($collection) {
			return response()->json([
				'status' => true,
				'message' => 'success'
			], 201);
		} else {
			return response()->json([
				'status' => false,
				'message' => 'failed'
			], 400);
		}
	}

	public function checkData($data)
	{
		$arr = array();
		foreach ($data as $key => $item) {
			$item = ($item == '') ? null : $item;
			$arr[$key] = $item;
		}
		return $arr;
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
		} else {
			return response()->json([
				'status' => true,
				'data' => $kode_penyakit,
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
		} else {
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
				'message' => 'kode penyakit not found'
			]);
		} else {
			$nama = $kode_penyakit->kode;
			$kode_penyakit->delete();
			return response()->json([
				'status' => true,
				'message' => 'Kode Penyakit \'' . $nama . '\' has been deleted'
			]);
		}
	}
}
