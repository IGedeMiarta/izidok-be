<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Izicrypt\Facade\Izicrypt;
use DB;
use App\Pasien;

class ExampleController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    public function encrypt(Request $request)
    {
        // dd(Izicrypt::encrypt('test'));

        // Izicrypt::requestEncrypt($request);
        // Izicrypt::requestEncrypt($request, ['umur']);
        // Izicrypt::requestEncrypt($request, ['umur'], 'except');

        // dd($request);
    }

    public function decrypt(Request $request)
    {
        // dd(Izicrypt::decrypt('d1b1620f93170eda49b7f75d20748917PbMKFPDQ48UoC/OEGVLOaw=='));

        // $data = DB::table('pasien')->get()->decrypt(['nik', 'klinik_id', 'rt', 'rw'], 'only');
        // $data = DB::table('pasien')->get()->decrypt(['nik', 'klinik_id', 'rt', 'rw'], 'except');

        // $data = Pasien::where('klinik_id', $request->user()->klinik_id)->first()->decrypt();
        // $data = Pasien::where('klinik_id', $request->user()->klinik_id)->get()->decrypt();
        // $data = Pasien::where('klinik_id', $request->user()->klinik_id)->paginate()->decrypt();

        // dd($data);
    }
}
