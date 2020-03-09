<?php

namespace App\Http\Controllers;

use Google\Cloud\Vision\V1\ImageAnnotatorClient;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use App\Pasien;
use App\Constant;
use App\User;
use Illuminate\Support\Facades\Auth;
use App\Spesialisasi;
use App\Klinik;
use Carbon\Carbon;
use DB;
use App\Http\Controllers\RekamMedisController;

class PasienController extends Controller
{
	public $user;

	public function __construct()
	{
		$this->user = Auth::user();
	}

	public function index(Request $request)
	{
		$user = $this->user;
        $pasien = new Pasien;

        if(empty($request->column) && empty($request->order)) {
            $column = 'id';
            $order = 'desc';
        } else {
            $column = $request->column;
            $order = $request->order;
        }

        $man = "laki-laki";
        $women = "perempuan";
		$gender = ''; // jika karakter yg di search kosong atau ada di "perempuan" dan "laki-laki"

		if(!empty($request->jenis_kelamin)) {
			$male = $female = false;

			if (strpos($man, $request->jenis_kelamin) !== false) {
				$male = true;
			}
			if (strpos($women, $request->jenis_kelamin) !== false) {
				$female = true;
			}

			if(!$male) $gender = 0; // jika perempuan
			elseif(!$female) $gender = 1; // jika laki2
		}

		$pasien = Pasien::select('id', DB::raw("CONCAT(nama,' (',DATE_FORMAT(tanggal_lahir, '%d-%m-%Y'),')') as nama"),'nomor_rekam_medis','jenis_kelamin','nomor_hp', 'klinik_id', 'tensi_sistole', 'tensi_diastole', 'nadi', 'suhu', 'tinggi_badan', 'berat_badan')
				->where('nomor_rekam_medis', 'like', "%{$request->nomor_rekam_medis}%")
				->where('nama', 'like', "%{$request->nama_pasien}%")
				->where('jenis_kelamin', 'like', "%{$gender}%")
				->where('nomor_hp', 'like', "%{$request->nomor_hp}%")
				->where('klinik_id', $user->klinik_id)
				->orderBy($column, $order);

		if($request->paginate === '0') {
			$pasien = $pasien->get();
		}
		else {
			$pasien = $pasien->paginate($request->limit);
		}

        $data['role'] = $user->roles->first()->name;
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
            'jenis_identitas' => 'string|nullable',
			'nik' => 'string|nullable',
			'tempat_lahir' => 'string',
			'tanggal_lahir' => 'required|date_format:Y-m-d',
			'jenis_kelamin' => 'required|integer',
			'golongan_darah' => 'string|nullable',
			'alamat_rumah' => 'required|string',
			//'rt' => 'string',
			//'rw' => 'string',
			//'kelurahan' => 'string',
            //'kecamatan' => 'string',
            'provinsi' => 'integer|nullable',
            'kota' =>  'integer|nullable',
			'status_perkawinan' => 'string|nullable',
			//'pekerjaan' => 'string',
			'nomor_hp' => 'required|regex:/^([0-9\s\-\+\(\)]*)$/|max:30',
			//'nama_penjamin' => 'string',
			//'nomor_polis_asuransi' => 'string',
			//'nomor_member_asuransi' => 'string',
			'email' => 'string|nullable',
			'nama_penanggung_jawab' => 'string|nullable',
			'tensi_sistole' => 'integer',
			'tensi_diastole' => 'integer',
			'nadi' => 'integer',
			'suhu' => 'integer',
			'respirasi' => 'integer',
			'tinggi_badan' => 'integer',
			'berat_badan' => 'integer',
			'nomor_hp_penanggung_jawab' => 'regex:/^([0-9\s\-\+\(\)]*)$/|min:8|max:15|nullable'
		]);

        $dob = $request->tanggal_lahir;
        if ($this->verifyDOB($dob)) {
            return response()->json(['status' => false, 'message' => 'Birth date must be no later than today']);
        }

		$user = User::find($request->user_id);
		$klinik = Klinik::find($user->klinik_id);

		$check_pasien = Pasien::where("nama",$request->nama)
		->where("tanggal_lahir",$request->tanggal_lahir)
		->where("nomor_hp",$request->nomor_hp)
		->where("klinik_id",$user->klinik_id)
		->first();

		if(!empty($check_pasien))
		{
			return response()->json([
				'success' => false,
				'message' => 'Data Pasien telah Terdaftar, Silahkan cek ulang data Anda!',
				'data' => ''
			], 400);
		}

		$pasien = new Pasien();
        $pasien->nama = $request->input('nama');
        $pasien->jenis_identitas = $request->input('jenis_identitas');
		$pasien->nik = $request->input('nik');
		$pasien->tempat_lahir = $request->input('tempat_lahir');
		$pasien->tanggal_lahir = $request->input('tanggal_lahir');
		$pasien->jenis_kelamin = $request->input('jenis_kelamin');
		$pasien->golongan_darah = $request->input('golongan_darah');
		$pasien->alamat_rumah = $request->input('alamat_rumah');
		//$pasien->rt = $request->input('rt');
		//$pasien->rw = $request->input('rw');
		//$pasien->kelurahan = $request->input('kelurahan');
        //$pasien->kecamatan = $request->input('kecamatan');
        $pasien->provinsi = empty($request->input('provinsi')) ? null : $request->input('provinsi');
        $pasien->kota = empty($request->input('kota')) ? null : $request->input('kota');
		$pasien->status_perkawinan = $request->input('status_perkawinan');
		//$pasien->pekerjaan = $request->input('pekerjaan');
		$pasien->nomor_hp = $request->input('nomor_hp');
		//$pasien->nama_penjamin = $request->input('nama_penjamin');
		//$pasien->nomor_polis_asuransi = $request->input('nomor_polis_asuransi');
		//$pasien->nomor_member_asuransi = $request->input('nomor_member_asuransi');
		$pasien->email = $request->input('email');
		$pasien->nama_penanggung_jawab = $request->input('nama_penanggung_jawab');
		$pasien->nomor_hp_penanggung_jawab = $request->input('nomor_hp_penanggung_jawab');
		$pasien->tensi_sistole = $request->input('tensi_sistole');
		$pasien->tensi_diastole = $request->input('tensi_diastole');
		$pasien->nadi = $request->input('nadi');
		$pasien->suhu = $request->input('suhu');
		$pasien->respirasi = $request->input('respirasi');
		$pasien->tinggi_badan = $request->input('tinggi_badan');
		$pasien->berat_badan = $request->input('berat_badan');
		$pasien->user_id = $request->user_id;

        $jenis_faskes = "";
		if ($klinik->tipe_faskes == Constant::TIPE_KLINIK) {
			$jenis_faskes = Constant::TIPE_FASKES_KLINIK;
		} else if ($klinik->tipe_faskes == Constant::DOKTER_PRAKTIK) {
			$jenis_faskes = Constant::TIPE_FASKES_DOKTER_PRAKTIK;
        }

        $spesialisasi = Spesialisasi::select('id', 'nama')->where('id', $klinik->spesialisasi_id)->value('nama');
        $gigi = 'gigi';
        $spesialis = 'spesialis';
        $pos_gigi = stripos($spesialisasi, $gigi);
        $pos_spesialis = stripos($spesialisasi, $spesialis);
        $kategori_dokter = "";
        if ($pos_gigi !== false && $pos_spesialis !== false) {
            $kategori_dokter = 40;
        } else if ($pos_spesialis !== false) {
            $kategori_dokter = 30;
        } else if ($pos_gigi !== false) {
            $kategori_dokter = 20;
        } else {
            $kategori_dokter = 10;
        }

		$last_pasien = Pasien::withTrashed()
			->where('klinik_id', $user->klinik_id)
			->orderBy('created_at','desc')
            ->first();
		$n_pasien = 0;
		if(empty($last_pasien) || empty($last_pasien->nomor_pasien)) {
			$n_pasien = 1;
	    } else {
			$n_pasien = $last_pasien->nomor_pasien + 1;
        }

        $nomor_dokter = sprintf('%07d', $klinik->kode_faskes);
        $nomor_pasien = sprintf('%07d', $n_pasien);
        $rekam_medis = $jenis_faskes . $kategori_dokter . $nomor_dokter . $nomor_pasien;
        $arr_rekam_medis = str_split($rekam_medis, 4);
        $arrayKeys = array_keys($arr_rekam_medis);
        $lastArrayKey = array_pop($arrayKeys);
        $str_rekam_medis = "";

		foreach ($arr_rekam_medis as $k => $v) {
			$str_rekam_medis .= $v;
			if($k != $lastArrayKey) {
				$str_rekam_medis .= "-";
			}
        }

        $pasien->nomor_pasien = $n_pasien;
		$pasien->nomor_rekam_medis = $str_rekam_medis;
		$pasien->klinik_id = $user->klinik_id;
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
		$klinikId = Auth::user()->klinik_id;
		$pasien = Pasien::with([
			'provinsi' => function($q) {
				$q->select('id', 'provinsi_nama');
			},
			'kota' => function($q) {
				$q->select('id', 'nama');
			}
		])->where('id',$request->id)->where('klinik_id',$klinikId)->first();

		
		if (empty($pasien)) {
			return response()->json([
				'status' => false,
				'message' => "pasien not found",
				'data' => '',
				'data_rm' => false,
			]);
		} else {
			$rm = new RekamMedisController();
			$request->pasien_id = $pasien->id;
			$dtaRm = json_decode(json_encode($rm->getAllKodePenyakitByPasien($request)), true);
			
			return response()->json([
				'status' => true,
				'message' => 'success',
				'data' => $pasien,
				'data_rm' => $dtaRm['original']['success'],
			]);
		}
	}

	public function getByDate(Request $request)
	{
		$tanggal = $request->tanggal_lahir;
		$user = User::find($request->user_id);
		$pasien = Pasien::where("tanggal_lahir",$tanggal)
					->where("klinik_id",$user->klinik_id)
					->get();
		if (count($pasien) == 0) {
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
            'tanggal_lahir' => 'required|date_format:Y-m-d',
            'jenis_kelamin' => 'required|integer',
            'alamat_rumah' => 'required|string',
            'nomor_hp' => 'required|regex:/^([0-9\s\-\+\(\)]*)$/|max:30',
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

		$check_pasien = Pasien::where("nama",$request->nama)
		->where("tanggal_lahir",$request->tanggal_lahir)
		->where("nomor_hp",$request->nomor_hp)
		->where("klinik_id",$user->klinik_id)
		->first();

		if(!empty($check_pasien))
		{
			if($pasien->id != $check_pasien->id)
			{
				return response()->json([
					'success' => false,
					'message' => 'failed, pasien is already exists',
					'data' => ''
				], 400);
			}
		}

        $pasien->nama = $request->input('nama');
        $pasien->jenis_identitas = $request->input('jenis_identitas');
		$pasien->nik = $request->input('nik');
		$pasien->tempat_lahir = $request->input('tempat_lahir');
		$pasien->tanggal_lahir = $request->input('tanggal_lahir');
		$pasien->jenis_kelamin = $request->input('jenis_kelamin');
		$pasien->golongan_darah = $request->input('golongan_darah');
		$pasien->alamat_rumah = $request->input('alamat_rumah');
		$pasien->provinsi = $request->provinsi ? $request->input('provinsi') : null;
		$pasien->kota = $request->kota ? $request->input('kota') : null;
		//$pasien->kelurahan = $request->input('kelurahan');
		//$pasien->kecamatan = $request->input('kecamatan');
		$pasien->status_perkawinan = $request->input('status_perkawinan');
		$pasien->pekerjaan = $request->input('pekerjaan');
		$pasien->nomor_hp = $request->input('nomor_hp');
		//$pasien->nama_penjamin = $request->input('nama_penjamin');
		//$pasien->nomor_polis_asuransi = $request->input('nomor_polis_asuransi');
		//$pasien->nomor_member_asuransi = $request->input('nomor_member_asuransi');
		$pasien->email = $request->input('email');
		$pasien->nama_penanggung_jawab = $request->input('nama_penanggung_jawab');
		$pasien->tensi_sistole = $request->input('tensi_sistole');
		$pasien->tensi_diastole = $request->input('tensi_diastole');
		$pasien->nadi = $request->input('nadi');
		$pasien->suhu = $request->input('suhu');
		$pasien->respirasi = $request->input('respirasi');
		$pasien->tinggi_badan = $request->input('tinggi_badan');
		$pasien->berat_badan = $request->input('berat_badan');
		$pasien->nomor_hp_penanggung_jawab = $request->input('nomor_hp_penanggung_jawab');
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

		if (sizeof($pasien->transKlinik)) {
			return response()->json([
				'status' => false,
				'message' => 'delete gagal! pasien atas nama \'' . $pasien->nama . '\' telah memiliki transaksi rawat jalan...'
			]);
		}

		if (empty($pasien)) {
			return response()->json([
				'status' => false,
				'data' => '',
				'message' => 'pasien not found'
			]);
		} else {
			$pasien->delete();
			return response()->json([
				'status' => true,
				'message' => 'Pasien \'' . $pasien->nama . '\' has been deleted'
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
			$path =  Storage::cloud()->put('ktp', $request->file);
		}

		$image = Storage::cloud()->get($path);

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

		#check data
		$check_words = array(
			'birthdate' => 'birthdate',
			'comma' => ','
		);

		$gender = array(
			'male' => 'LAKI-LAKI',
			'female' => 'PEREMPUAN'
		);

		#switch birthdate
		foreach($result as $key => $item){
			if (strpos($item, $check_words['comma']) !== false && $key !== $check_words['birthdate']) {
				$tmp = $result['birthdate'];
				$result['birthdate'] = $item;
				$result[$key] = $tmp;
			}
		}

		#check gender
		if(!in_array($result['gender'], $gender)){
			foreach($res as $key => $item){
				if(in_array(trim($item), $gender)){
					$result['gender'] = $item;
				}
			}
		}

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
		$this->validate($request, [
			'nama' => 'required|string',
			'tanggal_lahir' => 'required|date:Y-m-d',
			'nomor_hp' => 'required|regex:/^([0-9\s\-\+\(\)]*)$/|min:8|max:15'
		  ]);

		$conditions = array(
			'nama' => $request->nama,
			'tanggal_lahir' => $request->tanggal_lahir,
			'nomor_hp' => $request->nomor_hp
		);
		$pasien = Pasien::where($conditions);
		$is_exist = $pasien->count();

		if(!$is_exist){
			return response()->json([
				'status' => false,
				'message' => 'patient not found...',
			]);
		}

		$available = $pasien->whereHas('transKlinik', function ($data) {
										$data->where('status', Constant::TRX_MENUNGGU);
									})->count();
		$pasien = $pasien->first();

		if ($available) {
			return response()->json([
				'status' => false,
				'message' => 'this patient has an active transaction...',
			]);
		}

		return response()->json([
			'status' => true,
			'message' => 'this patient is available for transaction...',
			'data' => $pasien
		]);
    }

    public function verifyDOB($dob)
    {
        $dob = $dob > Carbon::now()->toDateString();

        if ($dob) {
            return true;
        }

        return false;
    }

    public function verifyIdentity(Request $request)
    {
        $klinikId = Auth::user()->klinik_id;
        $identity = Pasien::where('nik', $request->nik)->where('klinik_id', $klinikId)->exists();

        if ($identity && (!is_null($request->nik) || !empty($request->nik))) {
            return response()->json(['status' => false, 'message' => 'identity is already in use!!']);
        } else {
            return response()->json(['status' => true, 'message' => 'identity valid']);
        }
    }

}
