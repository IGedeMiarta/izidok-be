<?php

namespace App\Http\Controllers;

use App\Paygate;
use App\Billing;
use App\PayflagLog;
use App\Constant;
use App\Paket;
use App\Addson;
use App\User;
use App\KlinikSubscribe;
use Illuminate\Support\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class PayFlagController extends Controller
{
    /**
     * Payment Flag - Payment Gateway Bayarind
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'channelId' => 'required|string|exists:paygate,channel_id',
                'currency' => 'required|in:IDR',
                'transactionNo' => 'required|exists:billing,no_invoice',
                'transactionAmount' => 'required',
                'channelType' => 'required',
                'transactionStatus' => 'required',
                'transactionMessage' => 'required',
                'customerAccount' => 'required',
                'flagType' => 'required',
                'insertId' => 'required',
                'authCode' => 'required',
            ]);

            if ($validator->fails()) {
                return response()->json($this->payFlagResponse($request, '03', 'Invalid Parameter'), 200);
            }

            $pg = Paygate::select('secretkey')
                ->where('channel_id', $request->channelId)
                ->first();

            $authCode = [
                $request->transactionNo,
                $request->transactionAmount,
                $request->channelId,
                $request->transactionStatus,
                $request->insertId,
                $pg->secretkey
            ];

            if($request->authCode != hash("sha256", implode('', $authCode))) {
                return response()->json($this->payFlagResponse($request, '03', 'Invalid Auth Code'), 200);
            }

            $billing = Billing::select('expired_pay', 'amount_disc', 'amount_pay','status')
                ->where('no_invoice', $request->transactionNo)
                ->first();

            // 0 = waiting
            // 1 = success
            // 2 = failed
            // 3 = cancel
            if($billing->status > 0) {
                switch($billing->status) {
                    case 1:
                        $payStatus = '02';
                        $payMessage = 'Transaction has been paid';
                    break;
                    case 2:
                        $payStatus = '01';
                        $payMessage = 'Transaction Failed';
                    break;
                    case 3:
                        $payStatus = '05';
                        $payMessage = 'Transaction Cancelled';
                    break;
                    default:
                        $payStatus = '01';
                        $payMessage = 'Invalid Billing Status';
                    break;
                }

                return response()->json($this->payFlagResponse($request, $payStatus, $payMessage), 200);
            }

            $payment_date = new Carbon($request->transactionDate);
            $payment_expired = $billing->expired_pay;

            if($payment_date > $payment_expired) {
                return response()->json($this->payFlagResponse($request, '04', 'Transaction Expired'), 200);
            }

            if($billing->amount_pay == $request->transactionAmount) {
                DB::beginTransaction();
                $updated = Billing::where('no_invoice', $request->transactionNo)
                    ->update([
                        'status' => 1,
                        'pay_date' => Carbon::now()
                    ]);
                if($updated === 1) {
                    $bill = Billing::where('no_invoice', $request->transactionNo)->first();
                    $cekPket = KlinikSubscribe::where('klinik_id',$bill->klinik_id)->where('status',1)->exists();
                    $pkg = Paket::find($bill->paket_id);
                    if (!$cekPket) {
                        $newPaket = new KlinikSubscribe();
                        $newPaket->billing_id = $bill->id;
                        $newPaket->klinik_id = $bill->klinik_id;
                        $newPaket->paket_id = $bill->paket_id;
                        $newPaket->addson_id = $bill->addson_id;
                        $newPaket->limit = strtolower($pkg->limit) != 'unlimited' ? $bill->paket_bln * $pkg->limit : '9999999999';
                        $newPaket->started_date = date('Y-m-d H:i:s');
                        $newPaket->expired_date = date('Y-m-d H:i:s', strtotime("+ ".$bill->paket_bln." month"));
                        $newPaket->status = '1';
                        $newPaket->created_by = $bill->created_by;
                        $newPaket->save();

                        $bill->used_status = 1;
                        $bill->update();
                    }
                    DB::commit();
                    return response()->json($this->payFlagResponse($request, '00', 'Success'), 200);
                }

                DB::rollBack();
                return response()->json($this->payFlagResponse($request, '01', 'Failed DB'), 200);
            }

            return response()->json($this->payFlagResponse($request, '01', 'Failed'), 200);
        }
        catch(\Exception $e) {
            report($e);
            return response()->json($this->payFlagResponse($request, '01', 'Failed with exception'), 200);
        }
    }

    /**
     * Generate Payment Flag Response.
     * And save to payflag_log table.
     *
     * @param \Illuminate\Http\Request $request
     * @param string $status
     * @param string $message
     * @return array
     */
    protected function payFlagResponse(Request $request, $status, $message)
    {
        PayflagLog::create(array_merge($request->all(), [
            'paymentStatus' => $status,
            'paymentMessage' => $message,
        ]));

        return [
            'channelId' => $request->channelId,
            'currency' => $request->currency,
            'paymentStatus' => $status,
            'paymentMessage' => $message,
            'flagType' => $request->flagType,
            'paymentReffId' => $request->paymentReffId
        ];
    }
}
