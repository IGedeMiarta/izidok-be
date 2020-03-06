<?php

namespace App\Http\Controllers;

use App\Constant;
use App\DetailPembayaran;
use App\Pembayaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use DB;

class PembayaranController extends Controller
{
    public $user;

    public function __construct()
    {
		$this->user = Auth::user();
    }

    public function index(Request $request)
    {
        $user = $this->user;
        $pembayaran = new Pembayaran();

        if (!$user->hasRole(Constant::SUPER_ADMIN)) {
			$pembayaran = $pembayaran->where('klinik_id', $user->klinik_id);
		}

        if(empty($request->column) && empty($request->order)) {
            $column = 'status';
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

        //$status = [Constant::BELUM_LUNAS, Constant::LUNAS];
        $pembayaran = Pembayaran::select([
            'pembayaran.id',
            'pasien.nomor_rekam_medis',
            DB::raw("CONCAT(pasien.nama,' (',DATE_FORMAT(pasien.tanggal_lahir, '%d-%m-%Y'),')') as nama"),
            'pasien.jenis_kelamin',
            'pembayaran.status',
          ])
          ->leftJoin('trans_klinik', 'pembayaran.transklinik_id', '=', 'trans_klinik.id')
          ->leftJoin('pasien', 'trans_klinik.pasien_id', '=', 'pasien.id')
          ->where('pasien.nomor_rekam_medis', 'like', "%{$request->nomor_rekam_medis}")
          ->where('pasien.nama', 'like', "%{$request->nama_pasien}%")
          ->where('pasien.jenis_kelamin', 'like', "%{$gender}%")
          ->where('pembayaran.status', 'like', "{$request->status_pembayaran}%")
          ->where('pembayaran.klinik_id', $user->klinik_id)
          ->orderBy($column, $order)
          ->paginate($request->limit);

		if (!$pembayaran) {
			return response()->json([
				'success' => false,
				'message' => 'failed, you dont have role to see this',
			], 201);
		}

		return response()->json([
			'success' => true,
			'message' => 'success',
			'data' => $pembayaran
		], 201);
    }

  public function store(Request $request)
  {
    $this->validate($request, [
      'transklinik_id' => 'required|integer',
      'klinik_id' => 'required|integer',
      'jaminan' => 'required|in:UMUM,ASURANSI',
      'potongan' => 'between:0,99.99',
      'status' => 'required|string',
      'detail_pembayaran' => 'required|array',
      'detail_pembayaran[*].kode_layanan' => 'required|string',
      'detail_pembayaran[*].nama_layanan' => 'required|string',
      'detail_pembayaran[*].tarif' => 'required|integer',
      'detail_pembayaran[*].quantity' => 'required|integer'
    ]);


    $pembayaran = new Pembayaran();
    $pembayaran->transklinik_id = $request->transklinik_id;
    $pembayaran->klinik_id = $request->klinik_id;
    $pembayaran->jaminan = $request->jaminan;
    $pembayaran->potongan = $request->potongan;
    $pembayaran->status = $request->status;
    $pembayaran->created_by = $request->user_id;
    $pembayaran->save();

    $detail_pembayaran = $request->detail_pembayaran;
		$result = array();

		foreach ($detail_pembayaran as $item) {
      $detail = new DetailPembayaran();
      $detail->pembayaran_id = $pembayaran->id;
			$detail->kode_layanan = $item['kode_layanan'];
			$detail->nama_layanan = $item['nama_layanan'];
			$detail->tarif = $item['tarif'];
			$detail->quantity = $item['quantity'];
			$detail->created_by = $request->user_id;
			$detail->save();
			array_push($result, $detail);
    }

    $data['pembayaran'] = $pembayaran;
    $data['detail'] = $result;

    return response()->json([
      'status' => true,
      'message' => 'success',
      'data' => $data
    ], 201);

  }

  public function show(Request $request)
  {
    $pembayaran = Pembayaran::with(['createdBy:id,nama','detail', 'transklinik.pasien'])->find($request->id);
    if (empty($pembayaran)) {
      return response()->json([
        'status' => false,
        'message' => "Pembayaran not found",
      ]);
    } else {
      return response()->json([
        'status' => true,
        'data' => $pembayaran,
        'message' => 'success'
      ]);
    }
  }

  public function update(Request $request)
  {
    $this->validate($request, [
      'status' => 'required|in:DRAFT,BELUM LUNAS,LUNAS',
    ]);

    $pembayaran = Pembayaran::find($request->id);

      if(empty($pembayaran)){
        return response()->json([
          'status' => false,
          'message' => 'Pembayaran not found...'
        ]);
      }

    $pembayaran->status = $request->status;
    $pembayaran->save();

    return response()->json([
      'status' => true,
      'message' => 'Pembayaran has updated successfully...',
      'data' => $pembayaran
    ]);
  }

  public function delete($id)
  {
    $pembayaran = Pembayaran::find($id);

    if (empty($pembayaran)) {
      return response()->json([
        'status' => false,
        'message' => 'pembayaran not found'
      ]);
    }

    $pembayaran->delete();
    return response()->json([
      'status' => true,
      'message' => 'Pembayaran with ID \'' . $pembayaran->id . '\' has been deleted'
    ]);
  }

  public function addDetail(Request $request){
    $this->validate($request, [
      'pembayaran_id' => 'required|integer|exists:pembayaran,id',
      'detail_pembayaran' => 'required|array',
      'detail_pembayaran[*].kode_layanan' => 'required|string',
      'detail_pembayaran[*].nama_layanan' => 'required|string',
      'detail_pembayaran[*].tarif' => 'required|integer',
      'detail_pembayaran[*].quantity' => 'required|integer'
    ]);

    $detail_pembayaran = $request->detail_pembayaran;
		$result = array();

    $oldDetail = DetailPembayaran::where('pembayaran_id',$request->pembayaran_id)->get();

    if (count($oldDetail) > count($detail_pembayaran)) {
      foreach ($oldDetail as $key => $od) {
        if (!empty($detail_pembayaran[$key])) {
            $dtlUpdate = DetailPembayaran::where('id',$oldDetail[$key]->id)->first();
            $dtlUpdate->kode_layanan = $detail_pembayaran[$key]['kode_layanan'];
            $dtlUpdate->nama_layanan = $detail_pembayaran[$key]['nama_layanan'];
            $dtlUpdate->tarif = $detail_pembayaran[$key]['tarif'];
            $dtlUpdate->quantity = $detail_pembayaran[$key]['quantity'];
            $dtlUpdate->subtotal_tarif = intval($detail_pembayaran[$key]['tarif'])*intval($detail_pembayaran[$key]['quantity']);
            $dtlUpdate->updated_by = $request->user_id;
            $dtlUpdate->updated_at = date('Y-m-d H:i:s');
            $dtlUpdate->update();
        }else{
            DetailPembayaran::where('id',$od->id)->delete();
        }
      }
    }else{
  		foreach ($detail_pembayaran as $key => $item) {
        if (!empty($oldDetail[$key])) {
          $oldDtl = DetailPembayaran::where('id',$oldDetail[$key]->id)->first();
          $oldDtl->kode_layanan = $item['kode_layanan'];
          $oldDtl->nama_layanan = $item['nama_layanan'];
          $oldDtl->tarif = $item['tarif'];
          $oldDtl->quantity = $item['quantity'];
          $oldDtl->subtotal_tarif = intval($item['tarif'])*intval($item['quantity']);
          $oldDtl->updated_by = $request->user_id;
          $oldDtl->updated_at = date('Y-m-d H:i:s');
          $oldDtl->update();
          array_push($result, $oldDtl);
        }else{
          $detail = new DetailPembayaran();
          $detail->pembayaran_id = $request->pembayaran_id;
    			$detail->kode_layanan = $item['kode_layanan'];
    			$detail->nama_layanan = $item['nama_layanan'];
    			$detail->tarif = $item['tarif'];
    			$detail->quantity = $item['quantity'];
    			$detail->created_by = $request->user_id;
    			$detail->save();
    			array_push($result, $detail);
        }
      }
    }

    $data['detail'] = $result;

    $pembayaran = Pembayaran::find($request->pembayaran_id);
    $pembayaran->total = $request->total;
    $pembayaran->total_net = $request->total_nett;
    $pembayaran->potongan = $request->potongan;
    $pembayaran->updated_at = date('Y-m-d H:i:s');
    $pembayaran->update();

    return response()->json([
      'status' => true,
      'message' => 'items have been added to pembayaran ID '. $request->pembayaran_id,
      'data' => $data
    ], 201);

  }

    public function receipt(Request $request)
    {
        $user = $this->user;

        $pembayaran = Pembayaran::select([
            'pembayaran.id',
            'nomor_ijin AS no_sip',
            'klinik.nomor_telp',
            'nomor_rekam_medis',
            'pasien.nama AS nama_pasien',
            'jaminan',
            'users.nama AS nama_dokter',
            'pembayaran.created_by',
            'pembayaran.updated_at AS created_time',
            //'trans_klinik.updated_at AS admission_time',
            //'rekam_medis.created_at AS discharge'
            'total',
            'potongan',
            'total_net',
        ])
        ->leftJoin('trans_klinik', 'pembayaran.transklinik_id', '=', 'trans_klinik.id')
        ->leftJoin('klinik', 'trans_klinik.klinik_id', '=', 'klinik.id')
        ->leftJoin('pasien', 'trans_klinik.pasien_id', '=', 'pasien.id')
        ->leftJoin('users', 'trans_klinik.examination_by', '=', 'users.id')
        ->where('pembayaran.id', $request->id)
        ->where('pembayaran.klinik_id', $user->klinik_id)
        ->get();

        if(count($pembayaran) > 0) {
            $detail_pembayaran = DetailPembayaran::select([
                'kode_layanan',
                'nama_layanan',
                'tarif',
                'quantity',
                'subtotal_tarif'
            ])
            ->leftJoin('pembayaran', 'detail_pembayaran.pembayaran_id', '=', 'pembayaran.id')
            ->where('pembayaran.id', $request->id)
            ->get();
        } else {
            return response()->json([
                'status' => false,
                'message' => 'pembayaran not found'
            ]);
        }

        $data['pembayaran'] = $pembayaran;
        $data['detail_pembayaran'] = $detail_pembayaran;
        //return $data;

        return view('receipt', [
            'pembayaran'=>$pembayaran,
            'detail_pembayaran' => $detail_pembayaran
        ]);
    }

}
