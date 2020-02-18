<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Klinik;
use App\User;
use App\Constant;
use App\Dokter;
use App\Activation;
use App\Reference;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Input;

class KlinikController extends Controller
{
    public function __construct()
    {
        //nothing
    }

    public function index(Request $request)
    {
        $user = User::find($request->user_id);

        if ($user->hasRole(Constant::SUPER_ADMIN)) {
            $klinik = Klinik::all()->paginate($request->limit);
            $data['klinik'] = $klinik;
            return response()->json(['status' => true, 'data' => $data]);
        }

        $data['klinik'] = $user->klinik;
        return response()->json(['status' => true, 'data' => $data]);
    }

    public function show($id = null, Request $request)
    {
        $klinik = Klinik::find($id);

        if (!$klinik) {
            return response()->json(['status' => false, 'message' => 'Klinik not found...'], 422);
        }

        if ($klinik->user->id !== $request->user_id) {
            return response()->json(['status' => false, 'message' => 'you have no access to this klinik...'], 422);
        }

        return response()->json(['status' => true, 'data' => $klinik]);
    }

    public function store(Request $request)
    {
        $rules = [
            //'tipe_faskes' => 'required|min:1:max:2',
            'nama_klinik' => 'required|string',
            'nomor_telp' => 'required|regex:/^([0-9\s\-\+\(\)]*)$/|min:8|max:15',
            'email' => 'required|email',
            //'username' => 'required|string',
            'password' => 'required|confirmed|min:6|regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+$/',
            //'alamat' => 'string',
            //'foto_profile' => 'file|max:5000',
        ];

        #cek email
        $em = User::where('email', $request->email)->get();
        if ($this->isUserExist($em)) {
            return response()->json(['status' => false, 'message' => 'email is already in used!']);
        }

        #cek nomor_telp
        $username = User::where('nomor_telp', $request->nomor_telp)->get();
        if ($this->isUserExist($username)) {
            return response()->json(['status' => false, 'message' => 'no handphone is already in used!']);
        }

        $activation_url = Reference::where('key', Constant::VERIFY_EMAIL)->first();

        $isKlinik = false;
        if ($request->tipe_faskes == Constant::TIPE_KLINIK) {
            $rules['nama_pic'] = 'required|string';
            $isKlinik = true;
        }
        $this->validate($request, $rules);

        if ($isKlinik) {
            $nama_pic = $request->nama_pic;
            #fitur registrasi klinik belum tersedia
            return response()->json(['status' => false, 'message' => 'register klinik unavailable...'], 422);
        } else {
            $nama_pic = $request->nama_klinik;
            $nama_dokter = $request->nama_klinik;
        }

        #data klinik
        $klinik = Klinik::create([
            'tipe_faskes' => 2,
            'nama_klinik' => $request->nama_klinik,
            'nama_pic' => $nama_pic,
            //'nomor_ijin' => $request->nomor_ijin,
            'nomor_telp' => $request->nomor_telp
        ]);

        $klinik->kode_faskes = sprintf('%06d', $klinik->id);
        $klinik->save();

        #data user
        //$random_password = str_random(8);
        $user = User::create([
            "username" => $request->email,
            "email" => $request->email,
            //'password' => Hash::make($random_password),
            'password' => Hash::make($request->password),
            "nama" => $nama_pic,
            "nomor_telp" => $request->nomor_telp,
            "klinik_id" => $klinik->id,
            //"alamat" => $request->alamat,
            //"foto_profile" => \uploadToCloud('foto_profile', $request->foto_profile)
        ]);
        $user->assignRole(Constant::DOKTER_PRAKTEK);

        if (!$isKlinik) {
            #data dokter
            Dokter::create([
                'nama' => $nama_dokter,
                'user_id' => $user->id,
                'created_by' => $user->id,
            ]);
        }

        #activation token
        $activation = new Activation();
        $activation->token = base64_encode(str_random(10));
        $activation->user_id = $user->id;
        $activation->expired_at = date('Y-m-d H:i:s', strtotime('+10 minutes'));
        $activation->save();

        $data['klinik_id'] = $klinik->id;
        $data['user_id'] = $user->id;
        //$data['password'] = $random_password;
        $data['activation_url'] = url(env('APP_PREFIX', 'api/v1') . $activation_url->value . '/' . $activation->token);

        $email_data = [
            'subject' => 'Konfirmasi Akun izidok',
            'from' => 'izidok.dev@gmail.com',
            'to' => [$user->email],
            'activation_url' => $data['activation_url'],
            'name' => $user->nama,
            'phone' => $user->nomor_telp,
            'email' => $user->email,
            //'password' => $request->password,
        ];

        \sendEmail($email_data, Constant::ACTIVATION_EMAIL_TEMPLATE);

        if (!$data['klinik_id']) {
            return response()->json(['status' => false], 422);
        }

        return response()->json(['status' => true, 'data' => $data]);
    }

    public function update(Request $request)
    {
        $klinik = Klinik::find($request->id);
        if (!$klinik) {
            return response()->json(['status' => false, 'message' => 'klinik not found...']);
        } else {
            Klinik::find($request->id)->fill(Input::all())->save();
            $klinik = Klinik::find($request->id);
            return response()->json(['status' => true, 'data' => $klinik]);
        }
    }

    public function delete($id = null)
    {
        $klinik = Klinik::find($id);

        if (!$klinik) {
            return response()->json(['status' => false]);
        } else {
            $nama = $klinik->nama_klinik;
            $klinik->delete();
            return response()->json(['status' => true, 'message' => 'Klinik \'' . $nama . '\' has been deleted']);
        }
    }

    public function isUserExist($users)
    {
        if ($users) {
            foreach ($users as $item) {
                if ($item->activation->status == 1 || $item->activation->expired_at < date('Y-m-d H:i:s')) {
                    return true;
                }
            }
        }

        return false;
    }
}
