<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Layanan;
use App\Constant;
use Illuminate\Support\Facades\Auth;
use App\User;
use Illuminate\Validation\Rule;

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

		$layanan = new Layanan;
		if (!$user->hasRole(Constant::SUPER_ADMIN)) {
			$layanan = $layanan->where('klinik_id', $user->klinik_id);
		}

		if ($request->kode_layanan == "0") {
			$layanan = $layanan->where('kode_layanan', 'LIKE', '%0%');
		} else if (!empty($request->kode_layanan)) {
			$layanan = $layanan->where('kode_layanan', 'LIKE', '%{$request->kode_layanan}%');
		}

		if ($request->nama_layanan == "0") {
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
		$user = User::find($request->user_id);

		$this->validate($request, [
			'arr' => 'required|array',
			'arr.*.kode_layanan' => [
				'required','string',
				Rule::unique('layanan')->where('klinik_id', $user->klinik_id)
			],
			'arr.*.nama_layanan' => [
				'required','string',
				Rule::unique('layanan')->where('klinik_id', $user->klinik_id)
			],
			'arr.*.tarif' => 'required|integer'
		],['unique' => 'Nama atau kode layanan tidak boleh sama']);

		

		$arr_layanan = $request->arr;
		$result = array();

		foreach ($arr_layanan as $layanan_obj) {
			$layanan = new Layanan();
			$layanan->kode_layanan = $layanan_obj['kode_layanan'];
			$layanan->nama_layanan = $layanan_obj['nama_layanan'];
			$layanan->tarif = $layanan_obj['tarif'];
			$layanan->klinik_id = $user->klinik_id;
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

		$layanan = Layanan::where("klinik_id", $user->klinik_id)
			->where("kode_layanan", $kode)
			->get();

		if (count($layanan) == 0) {
			return response()->json([
				'success' => false,
				'message' => 'layanan is not exsist'
			], 404);
		} else {
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
		$user = User::find($request->user_id);

		$cek_layanan_klinik = Layanan::where('klinik_id',$user->klinik_id)->where("id",$request->id)->get();
		// dd($cek_layanan_klinik);
		if(count($cek_layanan_klinik) == 0)
		{
			return response()->json([
					'status' => false,
					'message' => "Tidak ada layanan dengan id ".$request->id." di klinik anda" ,
					'data' => ''
				],422);
		}

		$cek_layanan = Layanan::where('klinik_id', $user->klinik_id)
						->where(function ($q) use ($request) {
					    	$q->where('kode_layanan', $request->kode_layanan)->orWhere('nama_layanan', $request->nama_layanan);
						})->get();

		if(count($cek_layanan) > 1)
		{
			return response()->json([
					'status' => false,
					'message' => "Nama atau kode layanan tidak boleh sama",
					'data' => ''
				],422);
		}
		else if(count($cek_layanan) == 1)
		{
			if($cek_layanan->first()->id == $request->id)
			{
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
			else
			{
				return response()->json([
					'status' => false,
					'message' => "Nama atau kode layanan tidak boleh sama",
					'data' => ''
				],422);
			}
		}
		else
		{
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
