<?php

namespace App\Http\Controllers;

use DB;
use App\Billing;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class BillingController extends Controller
{
    public $user;

    public function __construct()
    {
		$this->user = Auth::user();
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $user = $this->user;

        if (empty($request->column) && empty($request->order)) {
            $column = 'id';
            $order = 'asc';
        } else {
            $column = $request->column;
            $order = $request->order;
        }

        $billing = Billing::select([
            'billing.id',
            'no_invoice AS nomor_tagihan',
            'paket.nama AS produk',
            DB::raw("CONCAT(paket_bln, ' bulan') AS periode_berlaku"),
            DB::raw("CONCAT('Rp. ', FORMAT(amount_disc, 0, 'id_ID'), ',-') AS total_pembayaran"),
            DB::raw("DATE_FORMAT(pay_date, '%d/%m/%Y') AS tanggal_bayar"),
            'status'
        ])
        ->join('paket', 'billing.paket_id', '=', 'paket.id')
        ->where('no_invoice', 'like', "%{$request->nomor_tagihan}%")
        ->where('paket.nama', 'like', "%{$request->produk}%")
        ->where('paket_bln', 'like', "%{$request->periode_berlaku}%")
        ->where('amount_disc', 'like', "%{$request->total_pembayaran}%")
        ->whereDate('pay_date', 'like', "%{$request->tanggal_bayar}%")
        ->orWhereNull('pay_date')
        ->where('billing.klinik_id', $user->klinik_id)
        ->orderBy($column, $order)
        ->paginate($request->limit);

        if (!$billing) {
			return response()->json([
				'success' => false,
				'message' => 'failed, you dont have role to see this',
			], 403);
		}
		return response()->json([
			'success' => true,
			'message' => 'success',
			'data' => $billing
		], 200);
    }
}
