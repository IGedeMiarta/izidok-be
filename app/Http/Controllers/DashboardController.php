<?php

namespace App\Http\Controllers;

use App\Constant;
use App\Operator;
use Illuminate\Http\Request;
use App\Organ;
use App\Pasien;
use App\TransKlinik;
use App\User;

class DashboardController extends Controller
{
	public function getPasien(Request $request)
	{

		#...validate here

		$user_id = $request->user_id;
		$type = $request->type;
		$from = null;
		$to = null;

		if ($type === Constant::DATE_RANGE) {
			$from = $request->from;
			$to = $request->to;

			$result = $this->pasienRangeDate($user_id, $from, $to);
			return response()->json(['status' => true, 'data' => $result]);
		}

		if ($type === Constant::MIGGUAN) {
			$result =  $this->pasienWeekly($user_id);
			return response()->json(['status' => true, 'data' => $result]);
		}

		if ($type === Constant::BULANAN) {
			$result = $this->pasienMonthly($user_id);
			return response()->json(['status' => true, 'data' => $result]);
		}

		if ($type === Constant::TAHUNAN) {
			$result = $this->pasienYearly($user_id);
			return response()->json(['status' => true, 'data' => $result]);
		}

		return response()->json(['status' => false, 'message' => 'something went wrong...'], 422);
	}

	private function pasienRangeDate($user_id, $from, $to)
	{
		$pasien_baru = Pasien::where('user_id', $user_id)
			->whereBetween('created_at', [$from, $to])
			->get();
		return $pasien_baru;
	}

	private function pasienWeekly($user_id)
	{
		$start_week = Carbon::now()->startOfWeek();
		$end_week = Carbon::now()->endOfWeek();

		$pasien_baru = Pasien::where('user_id', $user_id)
			->whereBetween('created_at', [$start_week, $end_week])
			->get();
		return $pasien_baru;
	}

	private function pasienMonthly($user_id)
	{
		$pasien_baru = Pasien::where('user_id', $user_id)
			->groupBy(function ($date) {
				return Carbon::parse($date->created_at)->format('m');
			})
			->get();
		return $pasien_baru;
	}

	private function pasienYearly($user_id)
	{
		$pasien_baru = Pasien::where('user_id', $user_id)
			->groupBy(function ($date) {
				return Carbon::parse($date->created_at)->format('Y');
			})
			->get();
		return $pasien_baru;
	}

	public function getPasienRawatJalan(Request $request)
	{
		$operator = Operator::where('user_id', $request->user_id);
		$klinik = $operator->kliniks[0]; #one operator only has one klinik
		$from = $request->from;
		$to = $request->to;

		$trans_klinik = TransKlinik::where('klinik_id', $klinik->id)
			->whereBetweem('created_at', [$from, $to])
			->get();

		if ($trans_klinik) {
			return response()->json(['status' => true, 'data' => $trans_klinik]);
		} else {
			return response()->json(['status' => false, 'message' => 'data is unavailable...'], 422);
		}
	}

	public function getLastAntrian(Request $request)
	{
		$operator = Operator::where('user_id', $request->user_id);
		$klinik = $operator->kliniks[0]; #one operator only has one klinik

		$trans_klinik = TransKlinik::select('nomor_antrian')
			->where('created_at', date('Y-m-d'))
			->orderBy('nomor_antrian', 'desc')
			->first();

		if ($trans_klinik) {
			return response()->json(['status' => true, 'data' => $trans_klinik]);
		} else {
			return response()->json(['status' => false, 'message' => 'data is unavailable...'], 422);
		}
	}

	public function getPendapatan(Request $request)
	{ }
}
