<?php

namespace App\Http\Controllers;

use App\Paket;
use App\Addson;
use App\Subscribe;
use App\Paygate;
use App\PaygateTutorial;
use App\Billing;
use App\Dokter;
use App\Operator;
use App\User;
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
        $exist = Subscribe::where('klinik_id',$klinikId)->exists();
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

        $paygate = Paygate::all();

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
        $data = Paket::where('id',$id)->first();
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
            ->select('pl.id','pl.channelId','pl.serviceCode','pl.currency','pl.transactionNo','pl.transactionAmount','pl.transactionDate','pl.transactionExpire','pl.description','pl.customerAccount','pl.customerName',
                DB::raw('case when b.status = 0 then "MENUNGGU PEMBAYARAN" else "LUNAS" end as status_billing'),
                'b.pg_id','p.nama as paket','a.nama as addson','b.amount_real','b.amount_disc')
            ->join('paygate_log as pl','b.no_invoice','=','pl.transactionNo')
            ->leftJoin('paket as p','b.paket_id','=','p.id')
            ->leftJoin('addson as a','b.addson_id','=','a.id')
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
}
