<?php

namespace App\Http\Controllers;

use App\Promo;
use App\Billing;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Carbon\Carbon;

class PromoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $promo = Promo::all();

        foreach ($promo as $key => $p) {
            $pg[] = [
                'id' => $p->id,
                'kode' => $p->kode,
                'value' => $p->value,
                'satuan' => $p->satuan,
                'expired_at' => $p->expired_at,
            ];
        }

        $data['promo'] = $pg;

        return response()->json([
            'success' => true,
            'message' => 'success',
            'data' => $data,
        ], 200);
    }

    public function getPromo(Request $request){
        $promo = Promo::where('kode',$request->kode);
        $klinikId = Auth::user()->klinik_id;
        $data = '';

        if (!$promo->exists()) {
            $status = false;
            $message = 'Kode Promo tidak ditemukan.';
        } else {
            $promo = $promo->first();
            if (Carbon::now()->toDateTimeString() > $promo->expired_at) {
                $status = false;
                $message = 'Kode Promo sudah kadaluarsa.';
            } else {
                $bill = Billing::where('promo_id',$promo->id)
                    ->where('klinik_id',$klinikId)
                    ->where('status','!=',3)
                    ->exists();
                // special case untuk promo IZIDOK75
                // bisa di gunakan berkali-kali
                if (strtolower($request->kode) == 'izidok75') $bill = false;
                
                if ($bill) {
                    $status = false;
                    $message = 'Kode Promo sudah digunakan.';
                } else {
                    if ($promo->satuan == 'percent') {
                        $message = 'Selamat, Anda mendapatkan potongan '.$promo->value.'%.';
                    } else {
                        $message = 'Selamat, Anda mendapatkan potongan Rp'.number_format($promo->value,0,',','.').',-';
                    }
                    $status = true;
                    $data = [
                        'id' => $promo->id,
                        'kode' => $promo->kode,
                        'value' => $promo->value,
                        'satuan' => $promo->satuan,
                    ];
                }
            }
        }

        return response()->json([
            'success' => $status,
            'message' => $message,
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
