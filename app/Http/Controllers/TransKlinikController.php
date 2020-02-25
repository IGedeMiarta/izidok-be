<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\TransKlinik;
use App\Constant;
use App\Pasien;
use App\Klinik;
use App\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use DateTime;

// use Carbon\Carbon;

class TransKlinikController extends Controller
{

  public $user;

	public function __construct(){
		$this->user = Auth::user();
    }

    public function index(Request $request)
	{
		$user = $this->user;
        $trans_klinik = new TransKlinik();

        if (!$user->hasRole(Constant::SUPER_ADMIN)) {
			$trans_klinik = $trans_klinik->where('klinik_id', $user->klinik_id);
		}

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

        $trans_klinik = TransKlinik::select('id', 'waktu_konsultasi', 'nomor_antrian', 'pasien_id', 'status')
                ->with(array('pasien' => function($query) use ($request, $gender) {
                    $query->select('id', 'nama', 'nomor_hp');
                    $query->where('nama', 'like', "%{$request->nama_pasien}%");
                    $query->where('jenis_kelamin', 'like', "%{$gender}%");
                    $query->where('nomor_hp', 'like', "%{$request->nomor_hp}%");
                }))
				->where('waktu_konsultasi', 'like', "%{$request->waktu_konsultasi}%")
				->where('nomor_antrian', 'like', "%{$request->nomor_antrian}%")
				->where('status', 'like', "%{$request->status}%")
                ->where('klinik_id', $user->klinik_id)
				->orderBy($column, $order)
                ->paginate($request->limit);

		$data['trans_klinik'] = $trans_klinik;

		if (!$trans_klinik) {
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
      'pasien_id' => 'required|integer',
      'klinik_id' => 'required|integer',
      'examination_by' => 'required|integer',
      'nomor_rekam_medis' => 'required|string',

      'nama_lengkap' => 'required|string',
      'nik' => 'string',
      'jenis_kelamin' => 'required|integer|min:0|max:1',
      'nomor_telp' => 'regex:/^([0-9\s\-\+\(\)]*)$/|min:8|max:15',
      'tinggi_badan' => 'integer',
      'berat_badan' => 'integer',
      'suhu' => 'integer',
      'tensi_sistole' => 'integer',
      'tensi_diastole' => 'integer',
      'nadi' => 'integer',
    ]);

    #klinik exist?
    if (!Klinik::find($request->klinik_id))
      return response()->json(['status' => false, 'message' => 'Klinik not found...'], 422);

    #dokter exist?
    if (!User::find($request->examination_by))
      return response()->json(['status' => false, 'message' => 'Dokter not found...'], 422);

    #pasien exist?
    if (!Pasien::find($request->pasien_id))
      return response()->json(['status' => false, 'message' => 'Pasien not found...'], 422);

    $pasien_id = $request->pasien_id;
    $klinik_id = $request->klinik_id;
    $consultation_time = Carbon::today();

    if ($this->verifyConsultationDate($pasien_id, $klinik_id, $consultation_time)) {
        return response()->json(['status' => false, 'message' => 'Patient already registered']);
    }

    #transaksi
  	$user = User::find($request->user_id);

    $trans_klinik = new TransKlinik;
    $trans_klinik->examination_by = $request->examination_by;
    $trans_klinik->pasien_id = $request->pasien_id;
    $trans_klinik->klinik_id = $user->klinik_id;
    $trans_klinik->created_by = $request->user_id;
    $trans_klinik->waktu_konsultasi = $consultation_time;
    $trans_klinik->nomor_antrian = $this->getNextOrderNumber($user->klinik_id, $consultation_time);
    $trans_klinik->anamnesa = $request->anamnesis;
    $trans_klinik->status = Constant::TRX_MENUNGGU;
    $trans_klinik->save();

    #update pasien
    $pasien = Pasien::find($request->pasien_id)->first();
    if ($pasien) {
      $pasien->tinggi_badan = $request->tinggi_badan;
      $pasien->berat_badan = $request->berat_badan;
      $pasien->suhu = $request->suhu;
      $pasien->tensi_sistole = $request->tensi_sistole;
      $pasien->tensi_diastole = $request->tensi_diastole;
      $pasien->nadi = $request->nadi;
      $pasien->save();
    }

    if (!$trans_klinik) {
      return response()->json(['status' => false], 422);
    }

    return response()->json([
      'status' => true,
      'message' => 'data transaction has been saved successfully',
      'data' => $trans_klinik
    ]);
  }

  public function show($id)
  {
    $trans_klinik = TransKlinik::with('pasien')->with('examinationBy:id,nama')->find($id);
    if (!$trans_klinik) {
      return response()->json(['status' => false, 'message' => 'Rawat Jalan not found...']);
    } else {
      return response()->json(['status' => true, 'data' => $trans_klinik]);
    }
  }

  public function update(Request $request)
  {

    $this->validate($request, [
      'status' => 'required',
    ]);

    $trans_klinik = TransKlinik::find($request->id);
    $user = $this->user;

		// if ($user->cant('updateOrDelete', $trans_klinik)) {
		// 	abort(403);
		// }

    if (!$trans_klinik) {
      return response()->json(['status' => false, 'message' => 'Rawat Jalan not found...']);
    } else {
      $trans_klinik->status = $request->status;
      $trans_klinik->examination_at = date('Y-m-d H:m:s');
      $trans_klinik->save();
      return response()->json(['status' => true, 'data' => $trans_klinik]);
    }
  }

  public function delete($id = null)
  {
    $trans_klinik = TransKlinik::find($id);
    $user = $this->user;
		if ($user->cant('updateOrDelete', $trans_klinik)) {
			abort(403);
		}

    if (!$trans_klinik) {
      return response()->json(['status' => false, 'message' => 'Rawat Jalan not found...']);
    } else {
      $trans_klinik_id = $trans_klinik->id;
      $trans_klinik->delete();
      return response()->json(['status' => true, 'message' => 'Rawat Jalan ID \'' . $trans_klinik_id . '\' has been deleted']);
    }
  }

  public function getNextOrderNumber($klinik_id, $consultation_time)
  {
    $trans_klinik = TransKlinik::where('klinik_id', $klinik_id)
      ->where('waktu_konsultasi', $consultation_time)
      ->orderBy('nomor_antrian', 'desc')->first();

    $number = 1;

    if (!$trans_klinik) {
        return $number;
    } else {
    	$number = $trans_klinik->nomor_antrian + 1;
        return $number;
    }
  }

    public function verifyConsultationDate($pasien_id, $klinik_id, $consultation_time)
    {
        $exist = TransKlinik::where('pasien_id', $pasien_id)
            ->where('klinik_id', '=', $klinik_id)
            ->where('waktu_konsultasi', '=', $consultation_time)
            ->where('status', '!=', Constant::TRX_BATAL)
            ->exists();

        if ($exist) {
            return true;
        }

        return false;
    }
}
