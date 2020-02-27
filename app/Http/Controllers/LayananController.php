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

        if(empty($request->column) && empty($request->order)) {
            $column = 'id';
            $order = 'asc';
        } else {
            $column = $request->column;
            $order = $request->order;
        }

        $layanan = Layanan::select('id', 'kode_layanan', 'nama_layanan', 'tarif')
            ->where('kode_layanan', 'like', "%{$request->kode_layanan}%")
            ->where('nama_layanan', 'like', "%{$request->nama_layanan}%")
            ->where('tarif', 'like', "%{$request->tarif}%")
            ->where('klinik_id', $user->klinik_id)
            ->orderBy($column, $order)
            ->paginate($request->limit);

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

        $user = Auth::user();
	    if (!empty($user) && $user->is_first_login !== 0) {
            $user->update(['is_first_login' => 0]);
        }

        $data['layanan'] = $result;
        $data['is_first_login'] = $user->is_first_login;

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
				'message' => 'layanan is not exist'
			], 404);
		} else {
			return response()->json([
				'success' => true,
				'message' => 'success',
				'data' => $layanan
			], 200);
		}
	}

	public function getByNama(Request $request)
	{
		$nama = urldecode($request->nama);
		$user = User::find($request->user_id);

		$layanan = Layanan::where("klinik_id", $user->klinik_id)
			->where("nama_layanan", $nama)
			->get();

		if (count($layanan) == 0) {
			return response()->json([
				'success' => false,
				'message' => 'layanan is not exist'
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
        $user = User::find($request->user_id);

        $customMessages = [
            'unique' => ':attribute sudah ada.',
            'distinct' => ':attribute tidak boleh sama'
        ];

		$this->validate($request, [
			'kode_layanan' => [
                'required', 'string',
                Rule::unique('layanan')->where('klinik_id', $user->klinik_id)
            ],
			'nama_layanan' => [
                'required', 'string',
                Rule::unique('layanan')->where('klinik_id', $user->klinik_id)
            ],
			'tarif' => 'required|integer'
		], $customMessages);

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
