<?php

namespace App\Http\Controllers;

use App\Paket;
use App\Addson;
use App\Subscribe;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

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
                'desc' => $arrDesc
            ];
        }

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
