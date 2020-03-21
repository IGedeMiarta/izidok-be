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
            $order = 'desc';
        } else {
            $column = $request->column;
            $order = $request->order;
        }

        $billing = Billing::select([
            'billing.id',
            'no_invoice AS nomor_tagihan',
            'paket.nama AS produk',
            DB::raw("CONCAT(paket_bln, ' bulan') AS periode_berlaku"),
            DB::raw("CONCAT('Rp. ', FORMAT(amount_disc + paygate.biaya_admin, 0, 'id_ID'), ',-') AS total_pembayaran"),
            DB::raw("DATE_FORMAT(pay_date, '%d/%m/%Y') AS tanggal_bayar"),
            'billing.status'
        ])
        ->join('paket', 'billing.paket_id', '=', 'paket.id')
        ->join('paygate', 'billing.pg_id', '=', 'paygate.id')
        ->where('klinik_id', $user->klinik_id)
        ->where('no_invoice', 'like', "%{$request->nomor_tagihan}%")
        ->where('paket.nama', 'like', "%{$request->produk}%")
        ->where('paket_bln', 'like', "%{$request->periode_berlaku}%")
        ->where('amount_disc', 'like', "%{$request->total_pembayaran}%")
        ->where('billing.status', 'like', "%{$request->status}%")
        ->orderBy($column, $order);

        if(!empty($request->tanggal_bayar)) {
            $billing = $billing->whereDate('pay_date', 'like', "%{$request->tanggal_bayar}%");
        }

        $billing = $billing->paginate($request->limit);

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
            DB::raw("CONCAT(nama, '-' ,paket_bln) AS nama"),
            DB::raw("case when paket.limit = 'Unlimited' then 'Unlimited' else paket.limit * paket_bln end as jumlah_kouta")
        ])
        ->join('paket', 'billing.paket_id', '=', 'paket.id')
        ->where('billing.klinik_id', $user->klinik_id)
        ->where('status', Constant::BILLING_SUCCESS)
        ->where('used_status', Constant::BILLING_UNUSED)
        ->orderBy('billing.id', 'desc')
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
            DB::raw("CONCAT(nama, '-' ,paket_bln) AS nama"),
            DB::raw("DATE_FORMAT(started_date, '%d %M %Y') AS mulai_berlaku"),
            DB::raw("DATE_FORMAT(expired_date, '%d %M %Y') AS habis_berlaku")
        ])
        ->join('paket', 'billing.paket_id', '=', 'paket.id')
        ->join('klinik_subscribe', 'klinik_subscribe.billing_id', '=', 'billing.id' )
        ->where('billing.klinik_id', $user->klinik_id)
        ->where('expired_date', '<', Carbon::now())
        ->orderBy('billing.id', 'desc')
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
            DB::raw("CONCAT(nama, '-' ,paket_bln) AS nama"),
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
            DB::raw("case when paket.limit = 'Unlimited' then 'Unlimited' else paket.limit * paket_bln end as jumlah_kouta"),
            DB::raw("DATE_FORMAT(pay_date, '%d %M %Y %H:%i:%s') AS pembelian"),
            DB::raw("DATE_FORMAT(started_date, '%d %M %Y %H:%i:%s') AS mulai_berlaku"),
            DB::raw("DATE_FORMAT(expired_date, '%d %M %Y %H:%i:%s') AS habis_berlaku"),
        ])
        ->join('paket', 'billing.paket_id', '=', 'paket.id')
        ->join('klinik_subscribe', 'klinik_subscribe.billing_id', '=', 'billing.id' )
        ->where('billing.klinik_id', $user->klinik_id)
        ->where('klinik_subscribe.status', Constant::PACKAGE_ACTIVE)
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

     /**
     * Display the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function packageUnpaid(Request $request)
    {
        $user = $this->user;

        $package_unpaid = Billing::where('klinik_id', $user->klinik_id)
                    ->where('status', Constant::BILLING_UNPAID)
                    ->exists();

        if ($package_unpaid) {
            return response()->json([
                'status' => false,
                'message' => "There are unpaid transactions",
            ]);
        } else {
            return response()->json([
                'status' => true,
                'message' => 'There are no unpaid transactions',
            ]);
        }
    }
}
