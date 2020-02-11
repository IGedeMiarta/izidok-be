<?php 

namespace App\Http\Controllers;

use App\Constant;
use App\DetailPembayaran;
use App\Pembayaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PembayaranController extends Controller 
{

  public $user;

	public function __construct(){
		$this->user = Auth::user();
  }
  
  public function index(Request $request)
  {
    $user = $this->user;

    $this->validate($request, [
      'from' => 'required|date_format:Y-m-d',
      'to' => 'required|date_format:Y-m-d',
    ]);

    $from = $request->from;
    $to = $request->to;

    $pembayaran = new Pembayaran;

		if (!$user->hasRole(Constant::SUPER_ADMIN)) {
      $pembayaran = $pembayaran->where('klinik_id', $user->klinik_id);
    } 
    
    #filter by date
		$pembayaran = $pembayaran->whereBetween('created_at',  [$from, $to])
              ->orWhereDate('created_at', $from);
    
    #filter by nama pasien & no. rekam medis
    if (!empty($request->nama_pasien)) {
			$pembayaran = Pembayaran::whereHas('transklinik', function($transklinik) use ($request){
        $transklinik->whereHas('pasien', function($pasien) use ($request){
          $pasien->where('nama', 'LIKE', "%{$request->nama_pasien}%");
        });
      });
    }
    
		if (!empty($request->no_rekam_medis)) {
      $pembayaran = Pembayaran::whereHas('transklinik', function($transklinik) use ($request){
        $transklinik->whereHas('pasien', function($pasien) use ($request){
          $pasien->where('nomor_rekam_medis', 'LIKE', "%{$request->no_rekam_medis}%");
        });
      });
		}

    #get data pasien and dokter
    $pembayaran = $pembayaran->with(['transklinik.pasien', 'createdBy', 'detail']);
    if($request->sort){
      $this->validate($request, [
        'sort' => 'in:asc,desc',
      ]);
      $pembayaran = $pembayaran->orderBy('status', $request->sort);
    }

    $pembayaran = $pembayaran->paginate($request->limit);
		$data['pembayaran'] = $pembayaran;

		return response()->json([
			'success' => true,
			'message' => 'success',
			'data' => $data
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
    $pembayaran = Pembayaran::with(['detail', 'transklinik.pasien'])->find($request->id);
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

		foreach ($detail_pembayaran as $item) {
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
    
    $data['detail'] = $result;

    return response()->json([
      'status' => true,
      'message' => 'items have been added to pembayaran ID '. $request->pembayaran_id,
      'data' => $data
    ], 201);

  }
  
}
