<?php

namespace App\Http\Controllers;

use App\Paket;
use App\Addson;
use App\Paygate;
use App\PaygateLog;
use App\PaygateTutorial;
use App\Billing;
use App\Dokter;
use App\Operator;
use App\User;
use App\KlinikSubscribe;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use DB;

class PaketController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $klinikId = Auth::user()->klinik_id;
        $exist = KlinikSubscribe::where('klinik_id',$klinikId)->exists();
        if ($exist) {
            $paket = Paket::where('id','!=',1)->get();
        } else {
            $paket = Paket::all();
        }

        foreach ($paket as $p) {
            $arrDesc = [];
            $desc = explode(';', $p['desc']);

            for ($i=0; $i < count($desc); $i++) {
                $arrDesc[$i+1] = $desc[$i];
            }

            $data['paket'][] = [
                'id' => $p['id'],
                'nama' => $p['nama'],
                'limit' => $p['limit'],
                'harga' => $p['harga'],
                'harga_tahunan' => $p['harga'] * 10,
                'desc' => $arrDesc
            ];
        }

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

    public function getAddson()
    {
        $data['addson'] = Addson::select('id','nama','limit','harga')->get();

        return response()->json([
            'success' => true,
            'message' => 'success',
            'data' => $data,
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $user = Auth::user();
        $data = Paket::where('id',$id)->first();

        if ($id == 1) {
            $checkBill = Billing::where('klinik_id',$user->klinik_id)->where('paket_id',1)->exists();
            if ($checkBill) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak dapat menggunakan Paket ini kembali.',
                    'data' => null,
                ], 200);
            }

            $noInvoice = substr('IZD'.date('ymdHis').rand(), 0,18);
            $now = date('Y-m-d H:i:s');
            $expPay = date('Y-m-d H:i:s', strtotime($now."+1 days"));
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
                'transactionNo' => $noInvoice,
                'transactionAmount' => '0',
                'transactionDate' => $now,
                'transactionExpire' => $expPay,
                'description' => 'Pembelian Paket Free Trial',
                'customerAccount' => $custAcc,
                'customerName' => $user->nama,
                'authCode' => hash("sha256",$noInvoice.'0'.$pg->channel_id.$pg->secretkey),
                'rc' => '00',
                'created_by' => $user->id
            ];

            PaygateLog::create($req);

            $bill = new Billing();
            $bill->klinik_id = $user->klinik_id;
            $bill->pg_id = 2;
            $bill->paket_id = 1;
            $bill->paket_bln = 1;
            $bill->no_invoice = $noInvoice;
            $bill->expired_pay = $expPay;
            $bill->amount_disc = 0;
            $bill->amount_real = 0;
            $bill->status = 1;
            $bill->created_by = $user->id;
            $bill->created_at = $now;
            $bill->save();

            $klinikSub = new KlinikSubscribe();
            $klinikSub->billing_id = $bill->id;
            $klinikSub->klinik_id = $user->klinik_id;
            $klinikSub->paket_id = $id;
            $klinikSub->limit = $data->limit;
            $klinikSub->started_date = date('Y-m-d H:i:s');
            $klinikSub->expired_date = date('Y-m-d H:i:s', strtotime("+1 month"));
            $klinikSub->status = 1;
            $klinikSub->created_by = $user->id;
            $klinikSub->save();
        }

        $arrDesc = [];
        $desc = explode(';', $data->desc);

        for ($i=0; $i < count($desc); $i++) {
            $arrDesc[$i+1] = $desc[$i];
        }

        $paket = [
            'id' => $data->id,
            'nama' => $data->nama,
            'limit' => $data->limit,
            'harga' => $data->harga,
            'harga_tahunan' => $data->harga * 10,
            'desc' => $arrDesc
        ];

        return response()->json([
            'success' => true,
            'message' => 'success',
            'data' => $paket,
        ], 200);
    }

    public function showAddson($id)
    {
        $data = Addson::where('id',$id)->first();
        $arrDesc = [];
        $desc = explode(';', $data->desc);

        $paket = [
            'id' => $data->id,
            'nama' => $data->nama,
            'limit' => $data->limit,
            'harga' => $data->harga,
        ];

        return response()->json([
            'success' => true,
            'message' => 'success',
            'data' => $paket,
        ], 200);
    }

    public function detailPembayaran($id)
    {
        $data['detail'] = DB::table('billing as b')
            ->select('pl.id','b.id AS billing_id','pl.channelId','pl.serviceCode','pl.currency','pl.transactionNo','pl.transactionAmount','pl.transactionDate','pl.transactionExpire','pl.description','pl.customerAccount','pl.customerName',
                DB::raw('case when b.status = 0 then "MENUNGGU PEMBAYARAN" else "LUNAS" end as status_billing'),
                'b.pg_id','p.nama as paket','p.harga as harga_paket','a.nama as addson','a.harga as harga_addson','b.paket_bln as durasi_paket','b.amount_real','b.amount_disc','pr.value as diskon','pr.satuan as satuan_promo')
            ->join('paygate_log as pl','b.no_invoice','=','pl.transactionNo')
            ->leftJoin('paket as p','b.paket_id','=','p.id')
            ->leftJoin('addson as a','b.addson_id','=','a.id')
            ->leftJoin('promo as pr','b.promo_id','=','pr.id')
            ->where('b.id',$id)->first();

        if (!is_null($data['detail'])) {
            $pgTutor = PaygateTutorial::where('pg_id',$data['detail']->pg_id)->get();

            foreach ($pgTutor as $p) {
                $arrDesc = [];
                $desc = explode(';', $p['description']);

                for ($i=0; $i < count($desc); $i++) {
                    $arrDesc[$i+1] = $desc[$i];
                }
                $data['tutorial'][] = [
                    'id' => $p['id'],
                    'tipe' => $p['tipe'],
                    'desc' => $arrDesc,
                ];
            }

            $pg = Paygate::find($data['detail']->pg_id);
            $data['paygate'] = [
                'id' => $pg->id,
                'nama' => $pg->nama,
                'biaya_admin' => $pg->biaya_admin,
                'logo' => url('/paygate/'.$pg->logo),
            ];

            $user = Auth::user();
            $dktr = Dokter::where('user_id', $user->id)->first();

            if (!is_null($dktr)) {
                $dokter = User::find($dktr->created_by);
            } else {
                $dokterId = Operator::where('user_id',$user->id)->first();
                $dokter = User::find($dokterId->created_by);
            }

            $data['dokter'] = [
                'nama' => $dokter->nama,
                'nomor_telp' => $dokter->nomor_telp,
                'email' => $dokter->email,
            ];
            $data['detail']['amount_pay'] = $data['detail']['amount_disc'] + $pg->biaya_admin;

            return response()->json([
                'success' => true,
                'message' => 'success',
                'data' => $data,
            ], 200);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'data not found',
                'data' => null,
            ], 200);
        }
    }

    /**
     * generate pdf from html
     *
     * @param array $data
     * @param string $type
     */
    public function generatePdfInvoice(Request $request)
    {
        $dtlPmbyrn = $this->detailPembayaran($request->id)->getData();

        $pdf = app()->make('dompdf.wrapper');
        $html = view('invoice', ['data' => (array) $dtlPmbyrn->data])->render();
        $pdf->setWatermarkImage(asset('upload/images/lunas.png'));

        $pdf->loadHTML($html);

        return $pdf->download('invoice.pdf');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
    }

    public function checkPackage(){
        $klinikId = Auth::user()->klinik_id;
        $paket = KlinikSubscribe::where('klinik_id',$klinikId)->where('status',1);
        $billing = Billing::where('klinik_id',$klinikId)->where('status',1)->where('used_status',0);

        if ($paket->exists()) {
            $pkt = $paket->first();

            if ($pkt->limit <= 0 && $pkt->expired_date <= date('Y-m-d H:i:s') && !$billing->exists()) {
                //Kuota habis, masa berlaku habis, belum beli paket
                return response()->json([
                    'success' => true,
                    'message' => 'Paket Anda telah berakhir, silahkan lakukan pembelian Paket untuk dapat melakukan aktivitas ini.',
                    'data' => $pkt,
                ], 200);
            }elseif ($pkt->limit > 0 && $pkt->expired_date <= date('Y-m-d H:i:s') && !$billing->exists()) {
                //Kuota masih ada, masa berlaku habis (kuota hangus), belum beli paket.
                return response()->json([
                    'success' => true,
                    'message' => 'Masa Berlaku Paket Anda telah berakhir, silahkan lakukan pembelian untuk dapat melakukan aktivitas ini.',
                    'data' => $pkt,
                ], 200);
            }elseif (($pkt->limit <= 0 && $pkt->expired_date < date('Y-m-d H:i:s') && $billing->exists()) || ($pkt->limit == 0 && $pkt->expired_date > date('Y-m-d H:i:s') && $billing->exists())) {
                //Kuota habis, masa berlaku habis, sudah beli paket ATAU Kuota habis, masa berlaku masih ada, namun sudah beli paket
                $bill = $billing->first();
                $bill->used_status = 1;
                $bill->update();

                $pkt->status = 0;
                $pkt->update();

                $pkg = Paket::find($bill->paket_id);

                $newPaket = new KlinikSubscribe();
                $newPaket->billing_id = $bill->id;
                $newPaket->klinik_id = $klinikId;
                $newPaket->paket_id = $bill->paket_id;
                $newPaket->addson_id = $bill->addson_id;
                $newPaket->limit = strtolower($pkg->limit) != 'unlimited' ? $bill->paket_bln * $pkg->limit : '9999999999';
                $newPaket->started_date = date('Y-m-d H:i:s');
                $newPaket->expired_date = date('Y-m-d H:i:s', strtotime("+ ".$bill->paket_bln." month"));
                $newPaket->status = '1';
                $newPaket->created_by = Auth::user()->id;
                $newPaket->save();

                return response()->json([
                    'success' => true,
                    'message' => 'Paket Anda '.$pkg->nama.' telah OTOMATIS Aktif mulai dari tanggal '.$newPaket->started_date.' hingga '.$newPaket->expired_date.'!',
                    'data' => $newPaket,
                ], 200);
            }elseif ($pkt->limit <= 0 && $pkt->expired_date > date('Y-m-d H:i:s') && !$billing->exists()) {
                //Kuota habis, masa berlaku masih ada, belum beli paket.
                return response()->json([
                    'success' => true,
                    'message' => 'Kuota Anda telah habis, silahkan lakukan pembelian Paket untuk dapat melakukan aktivitas ini.',
                    'data' => $pkt,
                ], 200);
            }
        }else{
            $billWaiting = Billing::
                where('klinik_id',$klinikId)
                ->where('status',0)
                ->where(
                    DB::raw('expired_pay','>',date('Y-m-d H:i:s'))
                );
            if ($billWaiting->exists()) {
                //Saat inisiasi, memilih ‘beli paket’, lalu ketika statusnya sedang menunggu pembayaran, dia ingin akses menu lain.
                return response()->json([
                    'success' => false,
                    'message' => 'Silahkan selesaikan proses Pembayaran Paket Anda untuk dapat melakukan aktivitas ini.',
                    'data' => null,
                ], 200);
            }else{
                return response()->json([
                    'success' => false,
                    'message' => 'Anda belum melakukan pembelian paket apapun',
                    'data' => null,
                ], 200);
            }
        }
    }
}
