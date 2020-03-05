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
use App\Pembayaran;
use App\DetailPembayaran;
use App\KodePenyakit;
use App\Layanan;
use App\PemeriksaanFisik;
use App\PemeriksaanPenunjang;
use App\TataLaksana;
use App\User;
use Illuminate\Support\Facades\Auth;
use DB;

class RekamMedisController extends Controller
{
    public $user;

    public function __construct(){
        $this->user = Auth::user();
    }

    public function index(Request $request)
    {
        $user = $this->user;
        $rekam_medis = new RekamMedis();

        if (!$user->hasRole(Constant::SUPER_ADMIN)) {
			$rekam_medis = $rekam_medis->where('created_by', $user->id);
        }

        if(empty($request->column) && empty($request->order)) {
            $column = 'id';
            $order = 'asc';
        } else {
            $column = $request->column;
            $order = $request->order;
        }

        $man = "laki-laki";
        $women = "perempuan";
		$gender = ''; // jika karakter yg di search kosong atau ada di "perempuan" dan "laki-laki"

		if(!empty($request->jenis_kelamin)) {
			$male = $female = false;

			if (strpos($man, $request->jenis_kelamin) !== false) {
				$male = true;
			}
			if (strpos($women, $request->jenis_kelamin) !== false) {
				$female = true;
			}

			if(!$male) $gender = 0; // jika perempuan
			elseif(!$female) $gender = 1; // jika laki2
        }

        $rekam_medis = RekamMedis::select([
            'rekam_medis.id',
            'pasien.id as pasien_id',
            'rekam_medis.nomor_rekam_medis',
            DB::raw("CONCAT(pasien.nama,' (',DATE_FORMAT(pasien.tanggal_lahir, '%d-%m-%Y'),')') as nama"),
            'pasien.jenis_kelamin',
            'pasien.nomor_hp'
          ])
          ->leftJoin('trans_klinik', 'rekam_medis.transklinik_id', '=', 'trans_klinik.id')
          ->leftJoin('pasien', 'trans_klinik.pasien_id', '=', 'pasien.id')
          ->where('pasien.nomor_rekam_medis', 'like', "%{$request->nomor_rekam_medis}")
          ->where('pasien.nama', 'like', "%{$request->nama_pasien}%")
          ->where('pasien.jenis_kelamin', 'like', "%{$gender}%")
          ->where('pasien.nomor_hp', 'like', "%{$request->nomor_hp}%")
          ->where('rekam_medis.created_by', $user->id)
          ->groupBy('pasien.id')
          ->orderBy($column, $order)
          ->paginate($request->limit);

        if (!$rekam_medis) {
			return response()->json([
				'success' => false,
				'message' => 'failed, you dont have role to see this',
			], 201);
		}

		return response()->json([
			'success' => true,
			'message' => 'success',
			'data' => $rekam_medis
		], 201);
    }

    private function getRekamMedisByTanggalLahir(Request $request)
    {
        $rekam_medis = RekamMedis::whereHas('pasien', function ($data) use ($request) {
            $data->where('tanggal_lahir', $request->tanggal_lahir);
        })
            ->with(['transKlinik.pasien', 'transKlinik.examinationBy'])
            ->where('created_by', $this->user->id)->paginate($request->limit);

        $data['rekam_medis'] = $rekam_medis;
        if (count($rekam_medis) > 0) {
            return response()->json([
                'success' => true,
                'message' => 'success',
                'data' => $data
            ], 200);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'not found'
            ], 404);
        }
    }

    public function getRekamMedisByPasien(Request $request)
    {
        $rekam_medis = RekamMedis::whereHas('transKlinik', function ($data) use ($request) {
            $data->where('pasien_id', $request->pasien_id);
        })
        ->where('created_by', $this->user->id)
        ->with('diagnosa')
        //->with(['transKlinik.pasien', 'transKlinik.examinationBy'])
        ->limit(4)
        ->get();

        foreach ($rekam_medis as $rm) {
            $item['id'] = $rm->diagnosa->id;
            $item['kode_penyakit'] = KodePenyakit::select('id', 'kode', 'description')->find(substr($rm->diagnosa->kode_penyakit_id, 1, 1));
            $item['notes'] = $rm->diagnosa->notes;
            $item['is_draw'] = $rm->diagnosa->is_draw;
            $item['draw_path'] = $rm->diagnosa->draw_path;
            $item['created_by'] =$rm->diagnosa->created_by;
            $rm->diagnosa_result = (object) $item;
        }

        $data['rekam_medis'] = $rekam_medis;

        if (count($rekam_medis) > 0) {
            return response()->json([
                'success' => true,
                'message' => 'success',
                'data' => $data
            ], 200);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Data not found'
            ], 404);
        }
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
        $anamnesa->draw_path = $request->anamnesa_is_draw ? \uploadToCloud('anamnesa', $request->anamnesa_draw)['url'] : null;
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
        $diagnosa->draw_path = $request->diagnosa_is_draw ? \uploadToCloud('diagnosa', $request->diagnosa_draw)['url'] : null;
        $diagnosa->created_by = $request->user_id;
        $diagnosa->save();

        #insert pemeriksaan_fisik
        $pemeriksaan_fisik = new PemeriksaanFisik();
        $pemeriksaan_fisik->organ_id = $request->organ_id;
        $pemeriksaan_fisik->notes = $request->pemeriksaan_text;
        $pemeriksaan_fisik->is_draw = $request->pemeriksaan_is_draw;
        $pemeriksaan_fisik->draw_path = $request->pemeriksaan_is_draw ? \uploadToCloud('pemeriksaan', $request->pemeriksaan_draw)['url'] : null;
        $pemeriksaan_fisik->created_by = $request->user_id;
        $pemeriksaan_fisik->save();

        #insert tata_laksana
        $tata_laksana = new TataLaksana();
        $tata_laksana->notes = $request->tatalaksana_text;
        $tata_laksana->is_draw = $request->tatalaksana_is_draw;
        $tata_laksana->draw_path = $request->tatalaksana_is_draw ? \uploadToCloud('tatalaksana', $request->tatalaksana_draw)['url'] : null;
        $tata_laksana->created_by = $request->user_id;
        $tata_laksana->save();

        #insert pemeriksaan_penunjang
        $p_penunjang = new PemeriksaanPenunjang();
        $p_penunjang->notes = $request->pemeriksaan_penunjang_text;
        $p_penunjang->is_draw = $request->pemeriksaan_penunjang_is_draw;
        $p_penunjang->draw_path = $request->pemeriksaan_penunjang_is_draw ? \uploadToCloud('pemeriksaan_penunjang', $request->pemeriksaan_penunjang_draw)['url'] : null;
        $p_penunjang->files = json_encode($request->pemeriksaan_penunjang);
        $p_penunjang->created_by = $request->user_id;
        $p_penunjang->save();

        #insert rekam_medis
        $rekam_medis = new RekamMedis();
        $rekam_medis->nomor_rekam_medis = $pasien->nomor_rekam_medis;
        $rekam_medis->anamnesa_id = $anamnesa->id;
        $rekam_medis->pemeriksaan_fisik_id = $pemeriksaan_fisik->id;
        $rekam_medis->diagnosa_id = $diagnosa->id;
        $rekam_medis->tata_laksana_id = $tata_laksana->id;
        $rekam_medis->pemeriksaan_penunjang_id = $p_penunjang->id;
        $rekam_medis->transklinik_id = $trans_klinik->id;
        $rekam_medis->created_by = $request->user_id;
        $status = $rekam_medis->save();

        #insert pembayaran
        $pembayaran = new Pembayaran();
        $pembayaran->transklinik_id = $request->transklinik_id;
        $pembayaran->klinik_id = $trans_klinik->klinik_id;
        $pembayaran->status = Constant::BELUM_LUNAS;
        $pembayaran->created_by = $request->user_id;
        $pembayaran->save();

        $layanan = Layanan::select('kode_layanan', 'nama_layanan', 'tarif')
            ->where('created_by', $request->user_id)
            ->limit(2)
            ->get();

        foreach ($layanan as $item) {
            $detail_pembayaran = new DetailPembayaran();
            $detail_pembayaran->pembayaran_id = $pembayaran->id;
            $detail_pembayaran->kode_layanan = $item->kode_layanan;
            $detail_pembayaran->nama_layanan = $item->nama_layanan;
            $detail_pembayaran->tarif = $item->tarif;
            $detail_pembayaran->quantity = 1;
            $detail_pembayaran->created_by = $request->user_id;
			$detail_pembayaran->save();
        }

        $data['rekam_medis'] = $rekam_medis;

        if ($status) {
            return response()->json([
                'status' => true,
                'message' => 'success',
                'data' => $data
            ], 201);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'failed',
                'data' => ''
            ], 400);
        }
    }

    public function show(Request $request)
    {
        $rekam_medis = RekamMedis::where('id', $request->id)
            ->with(['anamnesa', 'diagnosa', 'pemeriksaan_fisik', 'pemeriksaan_penunjang', 'tatalaksana'])
            ->first();
        if (empty($rekam_medis)) {
            return response()->json([
                'status' => false,
                'message' => "rekam medis not found",
                'data' => ''
            ]);
        } else {
            return response()->json([
                'status' => true,
                'data' => $rekam_medis,
            ]);
        }
    }

    public function uploadFile(Request $request)
    {
        $extension = ['png', 'jpg', 'jpeg', 'pdf'];

        $data = [];
        foreach ($request->file() as $key => $item) {

            if (!in_array(strtolower($item->extension()), $extension)) {
                return response()->json([
                    'status' => false,
                    'original_name' => $key,
                    'message' => 'file not supported, please upload PNG, JPG or PDF file...',
                ]);
            }

            $res = uploadToCloud('pemeriksaan_penunjang', $item);

            $data['name'] = $key;
            $data['url'] = $res['url'];
            $data['uploaded_name'] = $res['uploaded_name'];
        }

        $data['status'] = true;
        return $data;
    }

    public function deleteUploadedFile(Request $request)
    {
        $filenames = [];
        foreach ($request->filenames as $item) {
            array_push($filenames, 'pemeriksaan_penunjang/' . $item);
        }

        $res = deleteFromCloud($filenames);
        if ($res) {
            $data['deleted_files'] = $filenames;

            return response()->json([
                'status' => true,
                'data' => $data,
            ]);
        }
    }
}
