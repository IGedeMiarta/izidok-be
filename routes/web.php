<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

$router->get('/key', function () {
    return str_random(32);
});

$router->group(['prefix' => 'api/v1'], function () use ($router) {

    $router->get('/email/verify', 'UserController@verifyEmail');
    $router->get('/username/verify', 'UserController@verifyUsername');

    #registration
    // $router->post('/user', 'UserController@store');
    $router->post('/klinik', 'KlinikController@store');
    $router->get('/activate/{token}', 'UserController@activate');
    $router->get('/email/resend/{user_id}', 'UserController@sendEmail');
    $router->post('/login', 'UserController@login');

    #reset password
    $router->post('/forgot', 'UserController@forgot');
    $router->get('/check_forgot/{token}', 'UserController@check_forgot');
    $router->post('/reset', 'UserController@reset');

    #operator
    $router->get('/operator/check/{token}', 'OperatorController@check_activation');
    $router->post('/operator/activation', 'OperatorController@activation');

    $router->post('/logout', 'UserController@logout');

    $router->group(['middleware' => 'auth'], function () use ($router) {
        #user
        $router->put('/user/{id}', 'UserController@update');
        $router->post('/user/upload-foto/{id}', 'UserController@uploadFotoProfile');
        $router->group(['middleware' => ['role:super_admin|admin_klinik']], function () use ($router) {
            $router->get('/user', 'UserController@index');
            $router->get('/user/{id}', 'UserController@show');
            $router->delete('/user/{id}', 'UserController@delete');
        });
        

        #klinik
        $router->get('/klinik', ['middleware' => 'permission:read-klinik', 'uses' => 'KlinikController@index']);
        $router->get('/klinik/{id}', ['middleware' => 'permission:read-klinik', 'uses' => 'KlinikController@show']);
        $router->put('/klinik/{id}', ['middleware' => 'permission:update-klinik', 'uses' => 'KlinikController@update']);
        $router->delete('/klinik/{id}', ['middleware' => 'permission:delete-klinik', 'uses' => 'KlinikController@delete']);

        #role
        $router->group(['middleware' => 'role:super_admin'], function () use ($router) {
            $router->get('/role', 'RoleController@index');
            $router->post('/role', 'RoleController@store');
            $router->get('/role/{id}', 'RoleController@show');
            // $router->put('/role/{id}', 'RoleController@update');
            $router->delete('/role/{id}', 'RoleController@delete');
        });

        #operator
        $router->get('/operator', ['middleware' => 'permission:read-operator', 'uses' => 'OperatorController@index']);
        $router->post('/operator', ['middleware' => 'permission:create-operator', 'uses' => 'OperatorController@store']);
        $router->get('/operator/{id}', ['middleware' => 'permission:read-operator', 'uses' => 'OperatorController@show']);
        $router->put('/operator/{id}', ['middleware' => 'permission:update-operator', 'uses' => 'OperatorController@update']);
        $router->delete('/operator/{id}', ['middleware' => 'permission:delete-operator', 'uses' => 'OperatorController@delete']);

        #dokter
        $router->get('/dokter', ['uses' => 'DokterController@index']);
        $router->post('/dokter', ['uses' => 'DokterController@store']);
        $router->get('/dokter/{id}', ['uses' => 'DokterController@show']);
        $router->put('/dokter/{id}', ['uses' => 'DokterController@update']);
        $router->delete('/dokter/{id}', ['uses' => 'DokterController@delete']);

        #kode penyakit
        $router->get('/kode_penyakit/name', ['middleware' => 'permission:read-rekam-medis', 'uses' => 'KodePenyakitController@getByName']); // get by name
        $router->get('/kode_penyakit', ['middleware' => 'permission:read-rekam-medis', 'uses' => 'KodePenyakitController@index']);
        $router->get('/kode_penyakit/{id}', ['middleware' => 'permission:read-rekam-medis', 'uses' => 'KodePenyakitController@show']);
        $router->group(['middleware' => 'role:super_admin'], function () use ($router) {            
            $router->post('/kode_penyakit', 'KodePenyakitController@store');
            $router->post('/kode_penyakit/excel', 'KodePenyakitController@store_excel');
            $router->put('/kode_penyakit/{id}', 'KodePenyakitController@update');
            $router->delete('/kode_penyakit/{id}', 'KodePenyakitController@delete');
        });

        #layanan
        $router->group(['middleware' => ['role:dokter_praktek|admin_klinik|super_admin|operator']], function () use ($router) {
            $router->get('/layanan', 'LayananController@index');
            $router->post('/layanan', 'LayananController@store');
            $router->get('/layanan/{id}', 'LayananController@show');
            $router->put('/layanan/{id}', 'LayananController@update');
            $router->delete('/layanan/{id}', 'LayananController@delete');
            $router->get('/layanan/{kode}/kode', 'LayananController@getByKode');
        });

        #pasien
        $router->get('/pasien', ['middleware' => 'permission:read-pasien', 'uses' => 'PasienController@index']);
        $router->get('/pasien/{date}/date', ['middleware' => 'permission:read-pasien', 'uses' => 'PasienController@getByDate']);
        $router->post('/pasien', ['middleware' => 'permission:create-pasien', 'uses' => 'PasienController@store']);
        $router->get('/pasien/{id}', ['middleware' => 'permission:read-pasien', 'uses' => 'PasienController@show']);
        $router->put('/pasien/{id}', ['middleware' => 'permission:update-pasien', 'uses' => 'PasienController@update']);
        $router->delete('/pasien/{id}', ['middleware' => 'permission:delete-pasien', 'uses' => 'PasienController@delete']);
        $router->post('/pasien/ocr', ['middleware' => 'permission:create-pasien', 'uses' => 'PasienController@getText']);
        $router->get('/pasien/verify', ['middleware' => 'permission:create-pasien', 'uses' => 'PasienController@verifyPasien']);

        #transaksi klinik
        $router->get('/transaksi', ['middleware' => 'permission:read-transklinik', 'uses' => 'TransKlinikController@index']);
        $router->post('/transaksi', ['middleware' => 'permission:create-transklinik', 'uses' => 'TransKlinikController@store']);
        $router->get('/transaksi/{id}', ['middleware' => 'permission:read-transklinik', 'uses' => 'TransKlinikController@show']);
        $router->put('/transaksi/{id}', ['middleware' => 'permission:update-transklinik', 'uses' => 'TransKlinikController@update']);
        $router->delete('/transaksi/{id}', ['middleware' => 'permission:delete-transklinik', 'uses' => 'TransKlinikController@delete']);

        #rekam medis
        $router->group(['middleware' => ['role:dokter_praktek|dokter_klinik']], function () use ($router) {
            $router->get('/rekam_medis', 'RekamMedisController@index');
            $router->post('/rekam_medis', 'RekamMedisController@store');
            $router->get('/rekam_medis/{id}', 'RekamMedisController@show');
        });

        $router->get('/rekam_medis/date/{tanggal_lahir}', 'RekamMedisController@getRekamMedisByTanggalLahir');

        #organ
        $router->get('/organ/name', ['middleware' => 'permission:read-rekam-medis', 'uses' => 'OrganController@getByName']); // get by name
        $router->get('/organ', ['middleware' => 'permission:read-rekam-medis', 'uses' => 'OrganController@index']);
        $router->get('/organ/{id}', ['middleware' => 'permission:read-rekam-medis', 'uses' => 'OrganController@show']);
        $router->group(['middleware' => 'role:super_admin'], function () use ($router) {
            $router->post('/organ', 'OrganController@store');
            $router->put('/organ/{id}', 'OrganController@update');
            $router->delete('/organ/{id}', 'OrganController@delete');
        });

        #dashboard
        $router->get('/dash-pasien', 'DashboardController@getPasien');
        $router->get('/dash-rawat-jalan', 'DashboardController@getPasienRawatJalan');
        $router->get('/dash-antrian', 'DashboardController@getLastAntrian');

        $router->get('/pekerjaan', 'PekerjaanController@index');
        $router->get('/pekerjaan/{pekerjaan}', 'PekerjaanController@search');

        #pembayaran
        $router->get('/pembayaran', ['uses' => 'PembayaranController@index']);
        $router->post('/pembayaran', ['uses' => 'PembayaranController@store']);
        $router->post('/pembayaran/detail', ['uses' => 'PembayaranController@addDetail']);
        $router->get('/pembayaran/{id}', ['uses' => 'PembayaranController@show']);
        $router->put('/pembayaran/{id}', ['uses' => 'PembayaranController@update']);
        $router->delete('/pembayaran/{id}', ['uses' => 'PembayaranController@delete']);



    });

    $router->get('/image', function (Request $request) {
        $file = Storage::disk('minio')->get($request->path);
        return response($file, 200)->header('Content-Type', 'image/jpeg');
    });

    $router->post('/test-upload', 'RekamMedisController@uploadFile');
});
