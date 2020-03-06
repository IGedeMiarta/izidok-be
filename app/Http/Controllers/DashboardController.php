<?php

namespace App\Http\Controllers;

use App\Constant;
use App\Operator;
use Illuminate\Http\Request;
use App\Pasien;
use App\Pembayaran;
use App\TransKlinik;
use App\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
	public function getPasien(Request $request)
	{
		$user = User::find($request->user_id);
        $trans_klinik = new TransKlinik();

        if (!$user->hasRole(Constant::SUPER_ADMIN)) {
			$trans_klinik = $trans_klinik->where('klinik_id', $user->klinik_id);
        }

        $consultation_time = Carbon::today();
        $status = [Constant::TRX_MENUNGGU, Constant::TRX_KONSULTASI, Constant::TRX_SELESAI];

        $trans_klinik = TransKlinik::where('trans_klinik.klinik_id', $user->klinik_id)
          ->where('waktu_konsultasi', $consultation_time)
          ->whereIn('status', $status)
          ->count();

		$data['today_patient'] = $trans_klinik;

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

	public function getPasienRawatJalan(Request $request)
	{
		$this->validate($request, [
			'from' => 'required|date_format:Y-m-d',
			'to' => 'required|date_format:Y-m-d',
		]);

		$user = User::find($request->user_id);
		$klinik = $user->klinik;
		$from = $request->from;
		$to = $request->to;

		$trans_klinik = TransKlinik::where('klinik_id', $klinik->id)
			->whereBetween('created_at', [$from, $to])
			->get();

		if ($trans_klinik) {
			return response()->json(['status' => true, 'data' => $trans_klinik]);
		} else {
			return response()->json(['status' => false, 'message' => 'data is unavailable...'], 422);
		}
	}

	public function getLastAntrian(Request $request)
	{
		$this->validate($request, [
			'from' => 'required|date_format:Y-m-d',
			'to' => 'required|date_format:Y-m-d',
		]);

		$user = User::find($request->user_id);
		$klinik = $user->klinik;

		$trans_klinik = TransKlinik::select('nomor_antrian')
			->where('klinik_id', $klinik->id)
			->whereBetween(
				DB::raw('date(created_at)'),
				[$request->from, $request->to]
			)
			->orderBy('nomor_antrian', 'desc')
			->first();

		if ($trans_klinik) {
			return response()->json(['status' => true, 'data' => $trans_klinik->nomor_antrian]);
		} else {
			return response()->json(['status' => false, 'message' => 'data is unavailable...'], 422);
		}
	}

	public function getPendapatan(Request $request)
	{
		$pembayaran = Pembayaran::with('detail')
			->where('created_by', $request->user_id)
			->where('status', Constant::LUNAS)
			->get();

		$total = 0;
		foreach ($pembayaran as $item) {
			$total += $item->detail->sum('tarif');
		}

		return response()->json(['status' => true, 'data' => $total]);
	}

	private function pasienTotal($user_id, $from, $to)
	{
		$pasien_baru = Pasien::where('user_id', $user_id)
			->whereBetween(
				DB::raw('date(created_at)'),
				[$from, $to]
			)->count();
		return response()->json(['status' => true, 'data' => $pasien_baru]);
	}

	private function rawatJalanTotal($user_id, $from, $to)
	{
		$total = TransKlinik::where('created_by', $user_id)
			->whereBetween(
				DB::raw('date(created_at)'),
				[$from, $to]
			)->count();
		return response()->json(['status' => true, 'data' => $total]);
	}

	public function summary(Request $request)
	{
		$this->validate($request, [
			'type' => 'required|string',
			'from' => 'required|date_format:Y-m-d',
			'to' => 'required|date_format:Y-m-d',
		]);

		$user_id = $request->user_id;
		$type = $request->type;
		$from = $request->from;
		$to = $request->to;

		#sum pasien
		if ($type === Constant::SUM_PASIEN) {
			return $this->pasienTotal($user_id, $from, $to);
		}

		#sum rawat jalan
		if ($type === Constant::SUM_RAWAT_JALAN) {
			return $this->rawatJalanTotal($user_id, $from, $to);
		}

		#last antrian
		if ($type === Constant::ANTREAN) {
			return $this->getLastAntrian($request);
		}

		#last total pendapatan
		if ($type === Constant::SUM_PENDAPATAN) {
			return $this->getPendapatan($request);
		}
	}
}
