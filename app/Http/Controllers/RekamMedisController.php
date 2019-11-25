<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\RekamMedis;
use App\TransKlinik;
use App\Pasien;
use App\Anamnesa;
use App\Diagnosa;
use App\PemeriksaanFisik;

class RekamMedisController extends Controller
{
    public function index(Request $request)
  	{
  		 $all_klinik = TransKlinik::where('pasien_id',$request->pasien_id)->get();
       echo json_encode($all_klinik);
  	}

  	public function store(Request $request)
  	{
      $this->validate($request, [
            'pasien_id' => 'required|integer',
            'dokter_id' => 'required|integer',
            'operator_id' => 'required|integer',
            'klinik_id' => 'required|integer',
            'nomor_antrian' => 'required|integer',
            'anamnesa_notes' => 'required|string',
            'kode_penyakit_id' => 'required|integer',
            'nomor_rekam_medis' => 'required|string',
            'durasi_konsultasi' => 'required|integer',
            'organ_id' => 'required|integer',
        ]);

  		$trans_klinik = new TransKlinik();
      $trans_klinik->pasien_id = $request->pasien_id;
      $trans_klinik->operator_id = $request->operator_id;
      $trans_klinik->dokter_id = $request->dokter_id;
      $trans_klinik->klinik_id = $request->klinik_id;
      $trans_klinik->nomor_antrian = $request->nomor_antrian;
      $trans_klinik->durasi_konsultasi = $request->durasi_konsultasi;
      $trans_klinik->status = $request->status;
      $trans_klinik->save();

      $anamnesa = new Anamnesa();
      $anamnesa->tensi = $request->tensi;
      $anamnesa->nadi = $request->nadi;
      $anamnesa->suhu = $request->suhu;
      $anamnesa->respirasi = $request->respirasi;
      $anamnesa->tinggi_badan = $request->tinggi_badan;
      $anamnesa->notes = $request->anamnesa_notes;
      $anamnesa->berat_badan = $request->berat_badan;
      $anamnesa->save();

      $diagnosa = new Diagnosa();
      $diagnosa->kode_penyakit_id = $request->kode_penyakit_id;
      $diagnosa->notes = $request->diagnosa_notes;
      $diagnosa->is_draw = $request->diagnosa_is_draw;
      $diagnosa->draw_path = $request->diagnosa_draw_path;
      $diagnosa->save();

      $pemeriksaan_fisik = new PemeriksaanFisik();
      $pemeriksaan_fisik->organ_id = $request->organ_id;
      $pemeriksaan_fisik->notes = $request->pemeriksaan_fisik_notes;
      $pemeriksaan_fisik->is_draw = $request->pemeriksaan_is_draw;
      $pemeriksaan_fisik->draw_path = $request->pemeriksaan_draw_path;
      $pemeriksaan_fisik->save();

      $rekam_medis = new RekamMedis();
      $rekam_medis->nomor_rekam_medis = $request->nomor_rekam_medis;
      $rekam_medis->anamnesa_id = $anamnesa->id;
      $rekam_medis->pemeriksaan_fisik_id = $pemeriksaan_fisik->id;
      $rekam_medis->diagnosa_id = $diagnosa->id;
      $rekam_medis->transklinik_id = $trans_klinik->id;
      $status = $rekam_medis->save();

      $data['rekam_medis'] = $rekam_medis;
      $data['anamnesa'] = $anamnesa;
      $data['trans_klinik'] = $trans_klinik;
      $data['pemeriksaan_fisik'] = $pemeriksaan_fisik;
      $data['diagnosa'] = $diagnosa;



	   	if($status)
	   	{
	   		return response()->json([
	    			'success' => true,
	    			'message' => 'success',
	    			'data' => $data
	    		],201);
	   	}
	   	else
	   	{
	   		return response()->json([
	    			'success' => false,
	    			'message' => 'failed',
	    			'data' => ''
	    		],400);	
	   	}

  	}
}
