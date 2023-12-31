<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\TransKlinik;
use App\Constant;
use App\Dokter;
use App\Operator;
use App\Pasien;
use App\Klinik;
use App\User;
use App\DateFormat;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use DateTime;

// use Carbon\Carbon;

class TransKlinikController extends Controller
{
    public $user;

    public function __construct()
    {
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
            $order = 'asc';
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

        if(empty($request->waktu_konsultasi)) {
            $consultation_time = Carbon::today();
        } else {
            $consultation_time = $request->waktu_konsultasi;
        }

        //$consultation_date = [$consultation_time, date('Y-m-d', strtotime('-1 day', strtotime($consultation_time)))];
        $status = [Constant::TRX_MENUNGGU, Constant::TRX_KONSULTASI];

        $trans_klinik = TransKlinik::select([
            'trans_klinik.id',
            'waktu_konsultasi',
            'nomor_antrian',
            'status',
            'extend',
            'anamnesa',
            'pasien.id as pasien_id',
            DB::raw("CONCAT(pasien.nama,' (',DATE_FORMAT(pasien.tanggal_lahir, '%d-%m-%Y'),')') as nama"),
            'pasien.jenis_kelamin',
            'pasien.nomor_hp',
            'tensi_sistole',
            'tensi_diastole',
            'nadi',
            'suhu',
            'tinggi_badan',
            'berat_badan',
            'respirasi',
          ])
          ->join('pasien', 'pasien.id', '=', 'trans_klinik.pasien_id')
          ->where(DB::raw('date(waktu_konsultasi)'), $consultation_time)
          ->where('nomor_antrian', 'like', "%{$request->nomor_antrian}%")
          ->where('status', 'like', "%{$request->status}%")
          ->where('trans_klinik.klinik_id', $user->klinik_id)
          ->whereIn('status', $status)
          ->where('pasien.nama', 'like', "%{$request->nama_pasien}%")
          ->where('pasien.jenis_kelamin', 'like', "%{$gender}%")
          ->where('pasien.nomor_hp', 'like', "%{$request->nomor_hp}%")
          ->orderBy($column, $order)
          ->paginate($request->limit);

        $data['role'] = $user->roles->first()->name;
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
      //'examination_by' => 'required|integer',
      'nomor_rekam_medis' => 'required|string',

      'nama_lengkap' => 'required|string',
      'nik' => 'string',
      'jenis_kelamin' => 'required|integer|min:0|max:1',
      'nomor_telp' => 'regex:/^([0-9\s\-\+\(\)]*)$/|min:8|max:15',
      'tinggi_badan' => 'regex:/^(\d+(?:[\,]\d{1,9})?)$/',
      'berat_badan' => 'regex:/^(\d+(?:[\,]\d{1,9})?)$/',
      'suhu' => 'regex:/^(\d+(?:[\,]\d{1,9})?)$/',
      'tensi_sistole' => 'integer',
      'tensi_diastole' => 'integer',
      'nadi' => 'integer',
    ]);

    #klinik exist?
    if (!Klinik::find($request->klinik_id))
      return response()->json(['status' => false, 'message' => 'Klinik not found...'], 422);

    #dokter exist?
    /*if (!User::find($request->examination_by))
      return response()->json(['status' => false, 'message' => 'Dokter not found...'], 422);*/

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
    $dokter = Dokter::where('user_id', $user->id)->first();
    if (!empty($dokter)) {
      $data = $user->id;
    } else {
      $userId = Operator::where('user_id', $user->id)->first();
      $user = User::where('id', $userId->created_by)->first();
      $data = $user->id;
    }

    $trans_klinik = new TransKlinik;
    $trans_klinik->examination_by = $data;
    $trans_klinik->pasien_id = $request->pasien_id;
    $trans_klinik->klinik_id = $user->klinik_id;
    $trans_klinik->created_by = $request->user_id;
    $trans_klinik->waktu_konsultasi = Carbon::now();
    $trans_klinik->nomor_antrian = $this->getNextOrderNumber($user->klinik_id, $consultation_time);
    $trans_klinik->anamnesa = $request->anamnesis;
    $trans_klinik->status = Constant::TRX_MENUNGGU;
    $trans_klinik->save();

    #update pasien
    $pasien = Pasien::find($request->pasien_id);
    if ($pasien) {
      $pasien->tinggi_badan = str_replace(',','.',$request->tinggi_badan);
      $pasien->berat_badan = str_replace(',','.',$request->berat_badan);
      $pasien->suhu = str_replace(',','.',$request->suhu);
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
    $trans_klinik = TransKlinik::with(['pasien', 'examinationBy:id,nama'])->find($id);
    if (!$trans_klinik) {
      return response()->json(['status' => false, 'message' => 'Rawat Jalan not found...']);
    } else {
      if (Auth::user()->klinik_id == $trans_klinik->klinik_id) {
          return response()->json(['status' => true, 'data' => $trans_klinik]);
      }else{
          return response()->json(['status' => false, 'message' => 'Rawat Jalan not found...']);
      }
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
      ->where(DB::raw('date(waktu_konsultasi)'), $consultation_time)
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
        $status = [Constant::TRX_BATAL, Constant::TRX_SELESAI];

        $exist = TransKlinik::where('pasien_id', $pasien_id)
            ->where('klinik_id', '=', $klinik_id)
            ->where(DB::raw('date(waktu_konsultasi)'), '=', $consultation_time)
            ->whereNotIn('status', $status)
            ->exists();

        if ($exist) {
            return true;
        }

        return false;
    }

    public function emailReminder()
    {
        $list = TransKlinik::select([
            'trans_klinik.id',
            'waktu_konsultasi',
            'tgl_next_konsultasi',
            'users.nama AS nama_dokter',
            'users.nomor_telp',
            'users.email',
            'pasien.nama AS nama_pasien',
            'pasien.email AS email_pasien',
            'klinik.alamat'
        ])
        ->join('users', 'trans_klinik.examination_by', '=', 'users.id')
        ->join('pasien', 'trans_klinik.pasien_id', '=', 'pasien.id')
        ->join('klinik', 'trans_klinik.klinik_id', '=', 'klinik.id')
        ->where('reminder', Constant::REMIND)
        ->whereDate('tgl_next_konsultasi', Carbon::tomorrow())
        ->get();

        if(count($list)) {
            //setlocale(LC_TIME, 'id_ID');
            foreach ($list as $l) {
                $email_data = [
                    'subject' => 'Jadwal Konsultasi Lanjutan_'.$l->nama_dokter.'_'.DateFormat::convertDate(strftime('%a, %d %b %Y', strtotime($l->tgl_next_konsultasi))),
                    'to' => $l->email_pasien,
                    'from' => env('MAIL_USERNAME'),
                    'nama_pasien' => $l->nama_pasien,
                    'waktu_konsultasi' => DateFormat::convertDate(strftime('%a, %d %b %Y', strtotime($l->waktu_konsultasi))),
                    'next_konsultasi' => DateFormat::convertDate(strftime('%a, %d %b %Y', strtotime($l->tgl_next_konsultasi))),
                    'nama_dokter' => $l->nama_dokter,
                    'alamat' => $l->alamat,
                    'nomor_telp' =>  $l->nomor_telp,
                    'email' => $l->email
                ];
                \sendEmail($email_data, Constant::EMAIL_REMINDER);
                $trans_klinik = TransKlinik::find($l->id);
                $trans_klinik->update(['reminder' => 2]);
            }
            return response()->json([
                'status' => true,
                'message' => 'Email berhasil dikirim',
            ]);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Email list kosong',
            ]);
        }
    }

    public function moveQueue()
    {
        $klinikId = Auth::user()->klinik_id;
        $status = [Constant::TRX_MENUNGGU, Constant::TRX_KONSULTASI];

        $queue = TransKlinik::where('klinik_id', $klinikId)
            ->where('waktu_konsultasi', '=', Carbon::today())
            ->whereIn('status', $status)
            ->get();

        foreach ($queue as $value) {
            $value->extend = 1;
            $value->save();
        }

        return response()->json([
            'status' => true,
            'message' => 'queue data successfully moved',
            'data' => $queue,
        ]);
    }

}
