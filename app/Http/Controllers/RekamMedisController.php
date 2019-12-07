<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\RekamMedis;
use App\TransKlinik;
use App\Pasien;
use App\Anamnesa;
use App\Constant;
use App\Diagnosa;
use App\PemeriksaanFisik;
use App\User;

class RekamMedisController extends Controller
{
    public function index(Request $request)
    {
        $user = User::find($request->user_id);
        if ($user->hasRole(Constant::SUPER_ADMIN)) {
            $rekam_medis = RekamMedis::paginate($request->limit);
            $data['rekam_medis'] = $rekam_medis;
			return response()->json([
				'success' => true,
				'message' => 'success',
				'data' => $data
			], 201);
        }

        if($request->pasien_id){
            $data = $this->getRekamMedisByPasien($request);
            return response()->json([
				'success' => true,
				'message' => 'success',
				'data' => $data
			], 201);
        }

        $rekam_medis = RekamMedis::where('created_by', $user->id)->paginate($request->limit);

        if(!$rekam_medis){
            return response()->json([
				'success' => false,
				'message' => 'failed, no data available...',
				'data' => $data
			], 201);
        }

        $data['rekam_medis'] = $rekam_medis;
        return response()->json([
            'success' => true,
            'message' => 'success',
            'data' => $data
        ], 201);


    }

    private function getRekamMedisByPasien($request){
        $rekam_medis = RekamMedis::whereHas('transKlinik', function($data) use ($request){
            $data->where('pasien_id', $request->pasien_id);
        })->paginate($request->limit);
        $data['rekam_medis'] = $rekam_medis;

        return $data;
    }

    public function store(Request $request)
    {
        $rules = [
            'kode_penyakit' => 'required|array',
            'next_konsultasi' => 'required|integer',
            'organ_id' => 'required|integer',
            'transklinik_id' => 'required|integer',
        ];

        if (!$request->pemeriksaan_is_draw) {
            $rules['pemeriksaan_text'] = 'required';
        }

        if (!$request->diagnosa_is_draw) {
            $rules['diagnosa_text'] = 'required';
        }

        $this->validate($request, $rules);

        #step:
        /**
         * 1. update transklinik (next konsultasi)
         * 2. insert to anamensa, pemeriksaan_fisik, diagnosa
         * 3. insert to rekam medis
         * 4. redirect
         */

        // return $request;

        #update transaksi klinik
        $trans_klinik = TransKlinik::find($request->transklinik_id);
        $trans_klinik->durasi_konsultasi = $request->next_konsultasi;
        $trans_klinik->save();

        #get data pasien, insert anamnesa
        $pasien = $trans_klinik->pasien;
        $anamnesa = new Anamnesa();
        $anamnesa->tensi_sistole = ($request->tensi_sistole) ? $request->tensi_sistole : $pasien->tensi_sistole;
        $anamnesa->tensi_diastole = ($request->tensi_diastole) ? $request->tensi_diastole : $pasien->tensi_diastole;
        $anamnesa->nadi = ($request->nadi) ? $request->nadi : $pasien->nadi;
        $anamnesa->suhu = ($request->suhu) ? $request->suhu : $pasien->suhu;
        $anamnesa->respirasi = ($request->respirasi) ? $request->respirasi : $pasien->respirasi;
        $anamnesa->tinggi_badan = ($request->tinggi_badan) ? $request->tinggi_badan : $pasien->tinggi_badan;
        $anamnesa->berat_badan = ($request->berat_badan) ? $request->berat_badan : $pasien->berat_badan;
        $anamnesa->notes = $request->anamnesa_text;
        $anamnesa->is_draw = $request->anamnesa_is_draw;
        $anamnesa->draw_path = \uploadToMinio('anamnesa',$request->anamnesa_draw);
        $anamnesa->created_by = $request->user_id;
        $anamnesa->save();

        #make array penyakit, insert diagnosa
        $arr_penyakit = [];
        foreach ($request->kode_penyakit as $item) {
            array_push($arr_penyakit, $item['id']);
        }
        $diagnosa = new Diagnosa();
        $diagnosa->kode_penyakit_id = json_encode($arr_penyakit);
        $diagnosa->notes = $request->diagnosa_text;
        $diagnosa->is_draw = $request->diagnosa_is_draw;
        $diagnosa->draw_path = \uploadToMinio('diagnosa',$request->diagnosa_draw);
        $diagnosa->created_by = $request->user_id;
        $diagnosa->save();


        #insert pemeriksaan_fisik
        $pemeriksaan_fisik = new PemeriksaanFisik();
        $pemeriksaan_fisik->organ_id = $request->organ_id;
        $pemeriksaan_fisik->notes = $request->pemeriksaan_text;
        $pemeriksaan_fisik->is_draw = $request->pemeriksaan_is_draw;
        $pemeriksaan_fisik->draw_path = \uploadToMinio('pemeriksaan',$request->pemeriksaan_draw);
        $pemeriksaan_fisik->created_by = $request->user_id;
        $pemeriksaan_fisik->save();

        #insert rekam_medis
        $rekam_medis = new RekamMedis();
        $rekam_medis->nomor_rekam_medis = $pasien->nomor_rekam_medis;
        $rekam_medis->anamnesa_id = $anamnesa->id;
        $rekam_medis->pemeriksaan_fisik_id = $pemeriksaan_fisik->id;
        $rekam_medis->diagnosa_id = $diagnosa->id;
        $rekam_medis->transklinik_id = $trans_klinik->id;
        $rekam_medis->created_by = $request->user_id;
        $status = $rekam_medis->save();

        $data['rekam_medis'] = $rekam_medis;

        if ($status) {
            return response()->json([
                'success' => true,
                'message' => 'success',
                'data' => $data
            ], 201);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'failed',
                'data' => ''
            ], 400);
        }
    }

    public function show(Request $request){
        $rekam_medis = RekamMedis::find($request->id);
		if (empty($rekam_medis)) {
			return response()->json([
				'status' => false,
				'message' => "rekam medis not found",
				'data' => ''
			]);
		} else {
            $data = array();
            $anamnesa = Anamnesa::find($rekam_medis->anamnesa_id);
            $pemeriksaan = PemeriksaanFisik::find($rekam_medis->pemeriksaan_fisik_id);
            $diagnosa = Diagnosa::find($rekam_medis->diagnosa_id);

            $data['rekam_medis'] = $rekam_medis;
            $data['anamnesa'] = $anamnesa;
            $data['pemeriksaan_fisik'] = $pemeriksaan;
            $data['diagnosa'] = $diagnosa;

			return response()->json([
				'status' => true,
				'data' => $rekam_medis,
			]);
		}
    }
}
