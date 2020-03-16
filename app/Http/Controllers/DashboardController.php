<?php

namespace App\Http\Controllers;

use App\Constant;
use App\Klinik;
use App\KlinikSubscribe;
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

        $today_queue = TransKlinik::where('klinik_id', $user->klinik_id)
            ->whereDate('waktu_konsultasi', Carbon::today())
            ->where('status', '!=', Constant::TRX_BATAL)
            ->count();

        $new_patient = Pasien::where('klinik_id', $user->klinik_id)
            ->whereDate('created_at', Carbon::today())
            ->count();

        $last_queue = TransKlinik::where('klinik_id', $user->klinik_id)
            ->whereDate('waktu_konsultasi', Carbon::today())
            ->where('status', Constant::TRX_KONSULTASI)
            ->value('nomor_antrian');

        $cancel_queue = TransKlinik::where('klinik_id', $user->klinik_id)
            ->whereDate('waktu_konsultasi', Carbon::today())
            ->where('status', Constant::TRX_BATAL)
            ->count();

        $today_income = Pembayaran::where('klinik_id', $user->klinik_id)
            ->whereDate('updated_at', Carbon::today())
			->where('status', Constant::LUNAS)
            ->sum('total_net');

        $package = KlinikSubscribe::select([
            'klinik_subscribe.id',
            DB::raw("CONCAT(nama, ' (', paket_bln, '-bulan-berlangganan)') AS paket"),
            DB::raw("DATE_FORMAT(expired_date, '%d-%m-%Y, %H:%i:%s') AS habis_berlaku"),
            DB::raw("DATEDIFF(expired_date, NOW()) AS sisa_hari"),
            'klinik_subscribe.limit AS sisa_kouta',
        ])
        ->join('billing', 'klinik_subscribe.billing_id', '=', 'billing.id')
        ->join('paket', 'klinik_subscribe.paket_id', '=', 'paket.id')
        ->where('klinik_subscribe.klinik_id', $user->klinik_id)
        ->where('klinik_subscribe.status', Constant::PACKAGE_ACTIVE)
        ->where('expired_date', '>', Carbon::now())
        ->first();

        $data['pasien_hari_ini'] = $today_queue;
        $data['pasien_baru_hari_ini'] = $new_patient;
        $data['nomor_antrian_saat_ini'] = $last_queue;
        $data['pasien_batal_hari_ini'] = $cancel_queue;
        $data['total_pendapatan_hari_ini'] = 'Rp. '.number_format($today_income, 0, ',', '.').',-';
        $data['paket_berlangganan'] = $package;

		return response()->json([
			'success' => true,
			'message' => 'success',
			'data' => $data
		], 200);
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
