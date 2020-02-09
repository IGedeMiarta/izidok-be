<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Layanan;
use App\Constant;
use Illuminate\Support\Facades\Auth;
use App\User;

class LayananController extends Controller
{
	public $user;

	public function __construct()
	{
		$this->user = Auth::user();
	}

	public function index(Request $request)
	{
		$user = $this->user;

		// if ($request->kode) {
		// 	#get by kode
		// }

		// if ($request->name) {
		// 	#get by name
		// }

		if ($user->hasRole(Constant::SUPER_ADMIN)) {
			$layanan = Layanan::paginate($request->limit);
			$data['layanan'] = $layanan;
			if ($layanan) {
				return response()->json([
					'success' => true,
					'message' => 'success',
					'data' => $data
				], 201);
			}
		}

		$layanan = Layanan::where('klinik_id', $user->klinik_id);
		

		if($request->kode_layanan == "0")
		{	
			$layanan = $layanan->where('kode_layanan', 'LIKE', '%0%');
		}
		else if (!empty($request->kode_layanan)) {
			$layanan = $layanan->where('kode_layanan', 'LIKE', '%{$request->kode_layanan}%');
		}

		if($request->nama_layanan == "0")
		{	
			$layanan = $layanan->where('nama_layanan', 'LIKE', '%0%');
		}
		if (!empty($request->nama_layanan)) {
			$layanan = $layanan->where('nama_layanan', 'LIKE', "%{$request->nama_layanan}%");
		}

		$layanan = 	$layanan->paginate($request->limit);
		$data['layanan'] = $layanan;

		if (!$layanan) {
			return response()->json([
				'success' => false,
				'message' => 'failed, you dont have role to see this',
				'data' => $data
			], 400);
		}

		return response()->json([
			'success' => true,
			'message' => 'success',
			'data' => $data
		], 201);
	}

	public function store(Request $request)
	{
		$this->validate($request, [
			'arr' => 'required|array',
			'arr[*].klinik_id' => 'required|integer',
			'arr[*].kode_layanan' => 'required|string',
			'arr[*].nama_layanan' => 'required|string',
			'arr[*].tarif' => 'required|integer'
		]);

		$arr_layanan = $request->arr;
		$result = array();

		$flag = 0;
		$error_layanan = array();
		$user = User::find($request->user_id);

		foreach ($arr_layanan as $row) {

			$cek_kode_layanan = Layanan::where("klinik_id",$user->klinik_id)
					->where("kode_layanan",$row['kode_layanan'])
					->get();

			$cek_nama_layanan = Layanan::where("klinik_id",$user->klinik_id)
					->where("nama_layanan",$row['nama_layanan'])
					->get();

			if(!empty($cek_nama_layanan) && !empty($cek_kode_layanan))
			{
				return response()->json([
					'success' => false,
					'message' => 'nama layanan dan kode layanan tidak boleh sama',
				], 400);
			}
			else if(!empty($cek_nama_layanan))
			{
				return response()->json([
					'success' => false,
					'message' => 'nama layanan tidak boleh sama',
				], 400);
			}
			else if(!empty($cek_kode_layanan))
			{
				return response()->json([
					'success' => false,
					'message' => 'nama layanan tidak boleh sama',
				], 400);
			}
		}

		foreach ($arr_layanan as $layanan_obj) {
			$layanan = new Layanan();
			$layanan->kode_layanan = $layanan_obj['kode_layanan'];
			$layanan->nama_layanan = $layanan_obj['nama_layanan'];
			$layanan->tarif = $layanan_obj['tarif'];
			$layanan->klinik_id = $layanan_obj['klinik_id'];
			$layanan->created_by = $request->user_id;
			$layanan->save();
			array_push($result, $layanan);
		}

		$data['layanan'] = $result;

		if (count($data['layanan']) > 0) {
			return response()->json([
				'success' => true,
				'message' => 'success',
				'data' => $data
			], 201);
		} else {
			return response()->json([
				'success' => false,
				'message' => 'failed',
				'data' => ''
			], 400);
		}
	}

	public function getByKode(Request $request)
	{
		$kode = $request->kode;
		$user = User::find($request->user_id);

		$layanan = Layanan::where("klinik_id",$user->klinik_id)
					->where("kode_layanan",$kode)
					->get();

		if(count($layanan) == 0)
		{
			return response()->json([
				'success' => false,
				'message' => 'layanan is not exsist'
			], 404);	
		}
		else
		{
			return response()->json([
				'success' => true,
				'message' => 'success',
				'data' => $layanan
			], 200);
		}
	}

	public function show(Request $request)
	{
		$layanan = Layanan::find($request->id);

		if (!$layanan) {
			return response()->json([
				'status' => false,
				'message' => "layanan not found",
			]);
		}

		if ($layanan->created_by !== $request->user_id) {
			return response()->json(['status' => false, 'message' => 'you have no access to this layanan...'], 422);
		}

		return response()->json([
			'status' => true,
			'data' => $layanan,
			'message' => 'success'
		]);
	}

	public function update(Request $request)
	{
		$this->validate($request, [
			'kode_layanan' => 'required|string',
			'nama_layanan' => 'required|string',
			'tarif' => 'required|integer'
		]);

		$layanan = Layanan::find($request->id);
		$user = $this->user;

		if ($user->cant('updateOrDelete', $layanan)) {
			abort(403);
		}

		if (empty($layanan)) {
			return response()->json([
				'status' => false,
				'message' => "layanan not found",
				'data' => ''
			]);
		} else {
			$layanan->nama_layanan = $request->nama_layanan;
			$layanan->kode_layanan = $request->kode_layanan;
			$layanan->tarif = $request->tarif;
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
		$user = $this->user;

		if ($user->cant('updateOrDelete', $layanan)) {
			abort(403);
		}

		if (empty($layanan)) {
			return response()->json([
				'status' => false,
				'data' => '',
				'message' => 'layanan not found'
			]);
		} else {
			$nama = $layanan->nama_layanan;
			$layanan->delete();
			return response()->json([
				'status' => true,
				'message' => 'Layanan \'' . $nama . '\' has been deleted'
			]);
		}
	}
}
