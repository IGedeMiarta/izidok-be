<?php

namespace App\Http\Controllers;

use Google\Cloud\Vision\V1\ImageAnnotatorClient;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use App\Pasien;
use App\Constant;
use App\User;
use Illuminate\Support\Facades\Auth;

class PasienController extends Controller
{
	public $user;

	public function __construct(){
		$this->user = Auth::user();
	}
	
	public function index(Request $request)
	{
		$user = $this->user;

		if ($user->hasRole(Constant::SUPER_ADMIN)) {
			$pasien = Pasien::paginate($request->limit);
			$data['pasien'] = $pasien;
			return response()->json([
				'success' => true,
				'message' => 'success',
				'data' => $data
			], 201);
		}

		$pasien = Pasien::where('created_by', $user->id)->paginate($request->limit);
		$data['pasien'] = $pasien;

		if (!$pasien) {
			return response()->json([
				'success' => false,
				'message' => 'failed, you dont have role to see this',
				'data' => $data
			], 201);
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
			'nama' => 'required|string',
			'nik' => 'string',
			'tempat_lahir' => 'string',
			'tanggal_lahir' => 'required|date_format:Y-m-d',
			'jenis_kelamin' => 'required|integer',
			'golongan_darah' => 'string',
			'alamat_rumah' => 'required|string',
			'rt' => 'string',
			'rw' => 'string',
			'kelurahan' => 'string',
			'kecamatan' => 'string',
			'status_perkawinan' => 'required|string',
			'pekerjaan' => 'string',
			'nomor_hp' => 'string',
			'nama_penjamin' => 'string',
			'nomor_polis' => 'string',
			'email' => 'string',
			'nama_penanggung_jawab' => 'string',
			'tensi_sistole' => 'integer',
			'tensi_diastole' => 'integer',
			'nadi' => 'integer',
			'suhu' => 'integer',
			'respirasi' => 'integer',
			'tinggi_badan' => 'integer',
			'berat_badan' => 'integer',
			'nomor_rekam_medis' => 'integer',
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
		$pasien->tensi_sistole = $request->input('tensi_sistole');
		$pasien->tensi_diastole = $request->input('tensi_diastole');
		$pasien->nadi = $request->input('nadi');
		$pasien->suhu = $request->input('suhu');
		$pasien->respirasi = $request->input('respirasi');
		$pasien->tinggi_badan = $request->input('tinggi_badan');
		$pasien->berat_badan = $request->input('berat_badan');
		$pasien->nomor_rekam_medis = $request->input('nomor_rekam_medis');
		$pasien->user_id = $request->user_id;
		$user = User::find($request->user_id);
		$pasien->klinik_id = $request->klinik_id;
		$pasien->created_by = $request->user_id;
		$status = $pasien->save();

		if ($status) {
			return response()->json([
				'success' => true,
				'message' => 'success',
				'data' => $pasien
			], 201);
		} else {
			return response()->json([
				'success' => false,
				'message' => 'failed',
				'data' => ''
			], 400);
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
		} else {
			return response()->json([
				'status' => true,
				'data' => $pasien,
				'message' => 'success'
			]);
		}
	}

	public function update(Request $request)
	{
		$this->validate($request, [
			'nama' => 'required|string',
			'nik' => 'string',
			'tempat_lahir' => 'string',
			'tanggal_lahir' => 'required|date_format:Y-m-d',
			'jenis_kelamin' => 'required|integer',
			'golongan_darah' => 'string',
			'alamat_rumah' => 'required|string',
			'rt' => 'string',
			'rw' => 'string',
			'kelurahan' => 'string',
			'kecamatan' => 'string',
			'status_perkawinan' => 'required|string',
			'pekerjaan' => 'string',
			'nomor_hp' => 'string',
			'nama_penjamin' => 'string',
			'nomor_polis' => 'string',
			'email' => 'string',
			'nama_penanggung_jawab' => 'string',
			'tensi_sistole' => 'integer',
			'tensi_diastole' => 'integer',
			'nadi' => 'integer',
			'suhu' => 'integer',
			'respirasi' => 'integer',
			'tinggi_badan' => 'integer',
			'berat_badan' => 'integer',
			'nomor_rekam_medis' => 'integer',
		]);

		$pasien = Pasien::find($request->id);
		$user = $this->user;

		if ($user->cant('updateOrDelete', $pasien)) {
			abort(403);
		}

		if (empty($pasien)) {
			return response()->json([
				'status' => false,
				'message' => "pasien not found",
				'data' => ''
			]);
		}

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
		$pasien->tensi_sistole = $request->input('tensi_sistole');
		$pasien->tensi_diastole = $request->input('tensi_diastole');
		$pasien->nadi = $request->input('nadi');
		$pasien->suhu = $request->input('suhu');
		$pasien->respirasi = $request->input('respirasi');
		$pasien->tinggi_badan = $request->input('tinggi_badan');
		$pasien->berat_badan = $request->input('berat_badan');
		$pasien->nomor_rekam_medis = $request->input('nomor_rekam_medis');
		$status = $pasien->save();

		if (!$status) {
			return response()->json([
				'status' => false,
				'message' => "something went wrong...",
			]);
		}

		return response()->json([
			'status' => true,
			'data' => $pasien,
			'message' => 'success'
		]);
	}

	public function delete($id = null)
	{
		$pasien = Pasien::find($id);
		$user = $this->user;

		if ($user->cant('updateOrDelete', $pasien)) {
			abort(403);
		}

		if (empty($pasien)) {
			return response()->json([
				'status' => false,
				'data' => '',
				'message' => 'pasien not found'
			]);
		} else {
			$nama = $pasien->nama;
			$pasien->delete();
			return response()->json([
				'status' => true,
				'message' => 'Pasien \'' . $nama . '\' has been deleted'
			]);
		}
	}

	public function getText(Request $request)
	{

		$this->validate($request, [
			'file' => 'required|file|max:5000',
		]);

		if (!$request->file) {
			return response()->json([
				'status' => false,
				'message' => 'File is invalid...',
			]);
		}

		$credentialsFilePath = storage_path('izi-gvision-dev-053a0a7e0799.json');
		$imageAnnotator = new ImageAnnotatorClient([
			'credentials' => $credentialsFilePath
		]);

		if ($request->file) {
			$path =  Storage::disk('minio')->put('ktp', $request->file);
		}

		$image = Storage::disk('minio')->get($path);

		if (!$image) {
			return response()->json([
				'status' => false,
				'message' => 'File image not found...',
			]);
		}

		$response = $imageAnnotator->textDetection($image);
		$texts = $response->getTextAnnotations();
		$text = $texts[0]->getDescription();
		$wordToReplace = [
			'gol. darah', 'nik', 'kewarganegaraan', 'nama',
			'status perkawinan', 'berlaku hingga', 'alamat', 'agama',
			'tempat/tgl lahir', 'jenis kelamin', 'gol darah', 'rt/rw',
			'kel', 'desa', 'kecamatan'
		];
		$lines = preg_split('/\r\n|\r|\n/', $text);

		$res = [];
		foreach ($lines as $line) {
			foreach ($wordToReplace as $word) {
				$line = str_ireplace($word, '', $line);
				$line = str_ireplace(':', '', $line);
			}
			if ($line != '') {
				array_push($res, $line);
			}
		}

		$result = [
			"province" => $res[0],
			"city" => $res[1],
			"nik" => $res[2],
			"name" => $res[3],
			"birthdate" => $res[4],
			"gender" => $res[5],
			// "alamat" => $res[7],
			// "rt/rw" => $res[8],
			// "desa" => $res[9],
			// "kecamatan" => $res[10],
		];

		$imageAnnotator->close();

		$data['detected_text'] = $res;
		$data['result'] = $result;

		return response()->json([
			'status' => true,
			'message' => 'Text detection has done successfully...',
			'data' => $data
		]);
	}

	public function verifyPasien(Request $request)
	{
		$pasien = Pasien::where('id', $request->id)
			->whereHas('transKlinik', function ($data) {
				$data->where('status', Constant::QUEUED);
			})->count();

		if($pasien){
			return response()->json([
				'status' => false,
				'message' => 'this customer has an active transaction...',
				'data' => $pasien
			]);
		}

		return response()->json([
			'status' => true,
			'message' => 'this customer is available for transaction...',
		]);
	}
}
