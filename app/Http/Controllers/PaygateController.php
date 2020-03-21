<?php

namespace App\Http\Controllers;

use App\Paygate;
use App\Billing;
use App\PaygateLog;
use App\Constant;
use App\Paket;
use App\Addson;
use App\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Http\Controllers\PaketController;

class PaygateController extends Controller
{
    public function __construct(){
        $this->url_ins = 'https://simpg.sprintasia.net/PaymentRegister';
        $this->url_void = 'https://simpg.sprintasia.net/PostAuth';
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $paygate = Paygate::where('status',1)->get();

        foreach ($paygate as $key => $p) {
            $pg[] = [
                'id' => $p->id,
                'nama' => $p->nama,
                'biaya_admin' => $p->biaya_admin,
                'logo' => url('/paygate/'.$p->logo),
            ];
        }

        $data['paygate'] = $pg;

        return response()->json([
            'success' => true,
            'message' => 'success',
            'data' => $data,
        ], 200);
    }

    public function show($id)
    {
        $data = Paygate::find($id);
        $paygate = [
            'id' => $data->id,
            'nama' => $data->nama,
            'biaya_admin' => $data->biaya_admin,
            'logo' => url('/paygate/'.$data->logo),
        ];

        return response()->json([
            'success' => true,
            'message' => 'success',
            'data' => $paygate,
        ], 200);
    }

    public function store(Request $request){
        $user = Auth::user();
        $klinikId = $user->klinik_id;
        $noInvoice = substr('IZD'.date('ymdHis').rand(), 0,18);
        $now = date('Y-m-d H:i:s');
        $expPay = date('Y-m-d H:i:s', strtotime($now."+1 days"));

        $dataPg = [
            'no_invoice' => $noInvoice,
            'amount' => $request->amount_disc,
            'trans_date' => $now,
            'expired_pay' => $expPay,
            'desc' => 'Pembelian Paket '.$request->paket_id.($request->addson_id ? ' dan Paket Adds-on '.$request->addson_id : ''),
        ];

        if ($request->pg_id == 1) {
            return false;
        } elseif ($request->pg_id == 2) {
            $res = $this->bcaVa($dataPg);
        } else {
            $res = $this->permataVa($dataPg);
        }

        if ($res['insertStatus'] == '00') {
            try {
                $bill = new Billing();
                $bill->klinik_id = $klinikId;
                $bill->pg_id = $request->pg_id;
                $bill->promo_id = $request->promo_id;
                $bill->paket_id = $request->paket_id;
                $bill->paket_bln = $request->paket_bln;
                $bill->addson_id = $request->addson_id;
                $bill->no_invoice = $noInvoice;
                $bill->expired_pay = $expPay;
                $bill->amount_disc = $request->amount_disc;
                $bill->amount_real = $request->amount_real;
                $bill->created_by = $user->id;
                $bill->created_at = $now;
                $bill->save();

                $pktCtrl = new PaketController();
                $dtlPmbyrn = $pktCtrl->detailPembayaran($bill->id)->getData();

                $email_data = [
                    'subject' => 'Pembayaran izidok',
                    'to' => [$user->email],
                    'from' => env('MAIL_USERNAME'),
                    'data' => (array) $dtlPmbyrn->data
                ];

                if (\sendEmail($email_data, Constant::PAYMENT_CONFIRMATION)) {
                    return response()->json([
                        'status' => true,
                        'message' => 'email konfirmasi pembayaran sudah dibuat',
                        'data' => $user->email,
                        'billing_id' => $bill->id
                    ]);
                }
            } catch (\Exception $e) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage()
                ], 200);
            }

            return response()->json([
                'success' => true,
                'message' => 'success',
                'data' => ['billing_id' => $bill->id],
            ], 200);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'failed',
                'data' => $res,
            ], 200);
        }
    }

    public function bcaVa($data){
        $user = Auth::user();
        $pg = Paygate::find(2);

        if (strlen($user->nomor_telp) > 11) {
            $ca = substr($user->nomor_telp, -11);
        } else {
            $ca = $user->nomor_telp;
            for ($i=strlen($user->nomor_telp); $i < 11; $i++) {
                $ca = '0'.$ca;
            }
        }

        $custAcc = $pg->company_code.$ca;

        $req = [
            'channelId' => $pg->channel_id,
            'serviceCode' => '1021',
            'currency' => 'IDR',
            'transactionNo' => $data['no_invoice'],
            'transactionAmount' => $data['amount'],
            'transactionDate' => $data['trans_date'],
            'transactionExpire' => $data['expired_pay'],
            'description' => $data['desc'],
            'customerAccount' => $custAcc,
            'customerName' => $user->nama,
            'authCode' => hash("sha256",$data['no_invoice'].$data['amount'].$pg->channel_id.$pg->secretkey)
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->url_ins);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($req));
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/x-www-form-urlencoded'));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_POST,1);
        $response = json_decode(curl_exec($ch), true);
        curl_close($ch);

        $req['rc'] = $response['insertStatus'];
        $req['created_by'] = $user->id;
        PaygateLog::create($req);

        return $response;
    }

    public function permataVa($data){
        $user = Auth::user();
        $pg = Paygate::find(3);

        if (strlen($user->nomor_telp) > 10) {
            $ca = substr($user->nomor_telp, -10);
        } else {
            $ca = $user->nomor_telp;
            for ($i=strlen($user->nomor_telp); $i < 10; $i++) {
                $ca = '0'.$ca;
            }
        }

        $custAcc = $pg->company_code.$ca;

        $req = [
            'channelId' => $pg->channel_id,
            'serviceCode' => '1011',
            'currency' => 'IDR',
            'transactionNo' => $data['no_invoice'],
            'transactionAmount' => $data['amount'],
            'transactionDate' => $data['trans_date'],
            'transactionExpire' => $data['expired_pay'],
            'description' => $data['desc'],
            'customerAccount' => $custAcc,
            'customerName' => $user->nama,
            'authCode' => hash("sha256",$data['no_invoice'].$data['amount'].$pg->channel_id.$pg->secretkey)
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->url_ins);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($req));
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/x-www-form-urlencoded'));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_POST,1);
        $response = json_decode(curl_exec($ch), true);
        curl_close($ch);

        $req['rc'] = $response['insertStatus'];
        $req['created_by'] = $user->id;
        PaygateLog::create($req);

        return $response;
    }

    public function cancel($id){
        $pl = PaygateLog::find($id);
        $user = User::find($pl->created_by);
        $bil = Billing::where('no_invoice',$pl->transactionNo)->first();
        $pg = Paygate::find($bil->pg_id);
        $pkt = Paket::find($bil->paket_id);
        $adds = !is_null($bil->addson_id) ? Addson::find($bil->addson_id) : null;

        $req = [
            'channelId' => $pl->channelId,
            'serviceCode' => $pl->serviceCode,
            'transactionNo' => $pl->transactionNo,
            'transactionAmount' => $pl->transactionAmount,
            'transactionType' => 'VOID INSERT',
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->url_void);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($req));
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/x-www-form-urlencoded'));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_POST,1);
        $response = json_decode(curl_exec($ch), true);
        curl_close($ch);

        if ($response['transactionStatus'] == '00') {
            $pl->cancel_by = is_null(Auth::user()) ? 0 : Auth::user()->id;
            $pl->cancel_date = date('Y-m-d H:i:s');
            $pl->cancel_response = json_encode($response);
            $pl->cancel_rc = $response['transactionStatus'];
            $pl->update();

            $bil->status = 3;
            $bil->cancel_by = is_null(Auth::user()) ? 0 : Auth::user()->id;
            $bil->cancel_date = date('Y-m-d H:i:s');
            $bil->update();

            $data = [
                'bill' => $bil,
                'pg' => $pg,
                'pl' => $pl,
                'paket' => $pkt,
                'addson' => is_null($adds) ? null : $adds,
                'user' => is_null(Auth::user()) ? 'sistem' : 'pengguna',
                'now' => date('Y-m-d H:i:s'),
            ];

            $email_data = [
                'subject' => 'Pembatalan Pembayaran izidok',
                'to' => [$user->email],
                'from' => env('MAIL_USERNAME'),
                'data' => $data
            ];

            if (\sendEmail($email_data, Constant::CANCEL_SUBSCRIBE)) {
                return response()->json([
                    'success' => true,
                    'message' => 'success',
                    'data' => $response,
                ], 200);
            }else{
                return response()->json([
                    'success' => false,
                    'message' => 'email not sent',
                    'data' => $response,
                ], 200);
            }
        } else {
            $pl->cancel_by = is_null(Auth::user()) ? 0 : Auth::user()->id;
            $pl->cancel_date = date('Y-m-d H:i:s');
            $pl->cancel_response = json_encode($response);
            $pl->cancel_rc = $response['transactionStatus'];
            $pl->update();

            $bil->status = 3;
            $bil->cancel_by = is_null(Auth::user()) ? 0 : Auth::user()->id;
            $bil->cancel_date = date('Y-m-d H:i:s');
            $bil->update();

            return response()->json([
                'success' => false,
                'message' => 'failed',
                'data' => $response,
            ], 200);
        }
    }

    public function cronCancel(){
        $data = Billing::where('status',0)
            ->where('pay_date',null)
            ->where('expired_pay','<=',date('Y-m-d H:i:s'))
            ->where('cancel_date',null)
            ->get();
        if (count($data) > 0) {
            foreach ($data as $key => $d) {
                $pgl = PaygateLog::where('transactionNo',$d['no_invoice'])->first();
                $cancel = $this->cancel($pgl['id']);
            }
        }
    }
}
