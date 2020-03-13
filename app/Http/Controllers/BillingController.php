<?php

namespace App\Http\Controllers;

use DB;
use App\Constant;
use App\Billing;
use Carbon\Carbon;
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
            'nama AS produk',
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
        ->where(function ($query) use ($request) {
            $query->whereDate('pay_date', 'like', "%{$request->tanggal_bayar}%")
                ->orWhereNull('pay_date');
        })
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

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function packageList(Request $request)
    {
        $user = $this->user;

        $package = Billing::select([
            'billing.id',
            DB::raw("DATE_FORMAT(pay_date, '%d %M %Y, %H:%i:%S') AS waktu_pembelian"),
            'nama AS paket',
            'limit AS jumlah_kouta',
        ])
        ->join('paket', 'billing.paket_id', '=', 'paket.id')
        ->where('status', Constant::BILLING_SUCCESS)
        ->where('used_status', Constant::BILLING_UNUSED)
        ->where('billing.klinik_id', $user->klinik_id)
        ->paginate(5);

        if (!$package) {
			return response()->json([
				'success' => false,
				'message' => 'failed, you dont have role to see this',
			], 403);
		}
		return response()->json([
			'success' => true,
			'message' => 'success',
			'data' => $package
		], 200);
    }

     /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function packageListExpired(Request $request)
    {
        $user = $this->user;

        $package = Billing::select([
            'billing.id',
            DB::raw("DATE_FORMAT(pay_date, '%d %M %Y, %H:%i:%S ') AS waktu_pembelian"),
            'nama AS paket',
            DB::raw("DATE_FORMAT(started_date, '%d %M %Y') AS mulai_berlaku"),
            DB::raw("DATE_FORMAT(expired_date, '%d %M %Y') AS habis_berlaku")
        ])
        ->join('paket', 'billing.paket_id', '=', 'paket.id')
        ->join('klinik_subscribe', 'klinik_subscribe.billing_id', '=', 'billing.id' )
        ->where('billing.klinik_id', $user->klinik_id)
        ->where('expired_date', '<', Carbon::now())
        ->paginate(5);

        if (!$package) {
			return response()->json([
				'success' => false,
				'message' => 'failed, you dont have role to see this',
			], 403);
		}
		return response()->json([
			'success' => true,
			'message' => 'success',
			'data' => $package
		], 200);
    }

    /**
     * Display the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function packageDetails(Request $request)
    {
        $user = $this->user;

        $package_detail = Billing::select([
            'billing.id',
            'nama AS paket',
            DB::raw("CONCAT(paket_bln, ' Bulan') AS durasi"),
            'desc AS fitur',
        ])
        ->join('paket', 'billing.paket_id', '=', 'paket.id')
        ->where('billing.klinik_id', $user->klinik_id)
        ->find($request->id);

        if (empty($package_detail)) {
            return response()->json([
                'status' => false,
                'message' => "package not found",
            ]);
        } else {
            return response()->json([
                'status' => true,
                'message' => 'success',
                'data' => $package_detail
            ]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function packageActive(Request $request)
    {
        $user = $this->user;

        $package_active = Billing::select([
            'billing.id',
            'nama AS paket',
            'klinik_subscribe.status',
            'klinik_subscribe.limit AS sisa_kouta',
            'paket.limit AS paket_kouta',
            DB::raw("DATE_FORMAT(pay_date, '%d %M %Y %H:%i:%s') AS pembelian"),
            DB::raw("DATE_FORMAT(started_date, '%d %M %Y %H:%i:%s') AS mulai_berlaku"),
            DB::raw("DATE_FORMAT(expired_date, '%d %M %Y %H:%i:%s') AS habis_berlaku"),
        ])
        ->join('paket', 'billing.paket_id', '=', 'paket.id')
        ->join('klinik_subscribe', 'klinik_subscribe.billing_id', '=', 'billing.id' )
        ->where('klinik_subscribe.status', Constant::PACKAGE_ACTIVE)
        ->where('expired_date', '<', Carbon::now())
        ->where('billing.klinik_id', $user->klinik_id)
        ->first();

        if (empty($package_active)) {
            return response()->json([
                'status' => false,
                'message' => "package not found",
            ]);
        } else {
            return response()->json([
                'status' => true,
                'message' => 'success',
                'data' => $package_active
            ]);
        }
    }
}
