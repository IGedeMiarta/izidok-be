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
$router->get('/key', function() {
    return str_random(32);
});

$router->group(['prefix'=>'api/v1'], function() use($router){

    $router->get('/email/verify','UserController@verifyEmail');
    $router->get('/username/verify','UserController@verifyUsername');
   
    #registration
    $router->post('/user','UserController@store');
    $router->post('/klinik', 'KlinikController@store');
    $router->get('/activate/{token}','UserController@activate');
    $router->get('/email/resend/{user_id}', 'UserController@sendEmail');
    $router->post('/login','UserController@login');

    #reset password
    $router->post('/forgot','UserController@forgot');
    $router->get('/forgot_password/{token}','UserController@forgot_password');
    $router->post('/reset','UserController@reset');

    #operator
    $router->get('/operator/check/{token}','OperatorController@check_activation');
    $router->post('/operator/activate','OperatorController@activation');


    $router->group(['middleware' => 'auth'], function() use($router){
        #user
        $router->get('/user','UserController@index');
        $router->get('/user/{id}','UserController@show');
        $router->put('/user/{id}','UserController@update');
        $router->delete('/user/{id}','UserController@delete');
        $router->post('/logout','UserController@logout');

        #klinik
        $router->get('/klinik', 'KlinikController@index');
        $router->get('/klinik/{id}', 'KlinikController@show');
        $router->put('/klinik/{id}', 'KlinikController@update');
        $router->delete('/klinik/{id}', 'KlinikController@delete');

        #role
        $router->get('/role', 'RoleController@index');
        $router->post('/role', 'RoleController@store');
        $router->get('/role/{id}', 'RoleController@show');
        $router->put('/role/{id}', 'RoleController@update');
        $router->delete('/role/{id}', 'RoleController@delete');

        #operator
        $router->get('/operator', 'OperatorController@index');
        $router->post('/operator', 'OperatorController@store');

        $router->get('/operator/{id}', 'OperatorController@show');
        $router->put('/operator/{id}', 'OperatorController@update');
        $router->delete('/operator/{id}', 'OperatorController@delete');

        #dokter
        $router->get('/dokter', 'DokterController@index');
        $router->post('/dokter', 'DokterController@store');
        $router->get('/dokter/{id}', 'DokterController@show');
        $router->put('/dokter/{id}', 'DokterController@update');
        $router->delete('/dokter/{id}', 'DokterController@delete');

        #kode penyakit
        $router->get('/kode_penyakit', 'KodePenyakitController@index');
        $router->post('/kode_penyakit', 'KodePenyakitController@store');
        $router->post('/kode_penyakit/excel', 'KodePenyakitController@store_excel');
        $router->get('/kode_penyakit/{id}', 'KodePenyakitController@show');
        $router->put('/kode_penyakit/{id}', 'KodePenyakitController@update');
        $router->delete('/kode_penyakit/{id}', 'KodePenyakitController@delete');

        #layanan
        $router->get('/layanan', 'LayananController@index');
        $router->post('/layanan', 'LayananController@store');
        $router->get('/layanan/{id}', 'LayananController@show');
        $router->put('/layanan/{id}', 'LayananController@update');
        $router->delete('/layanan/{id}', 'LayananController@delete');

        #pasien
        $router->get('/pasien', 'PasienController@index');
        $router->post('/pasien', 'PasienController@store');
        $router->get('/pasien/{id}', 'PasienController@show');
        $router->put('/pasien/{id}', 'PasienController@update');
        $router->delete('/pasien/{id}', 'PasienController@delete');
        $router->post('/pasien/ocr', 'PasienController@getText');

        #transaksi klinik
        $router->get('/transaksi','TransKlinikController@index');
        $router->post('/transaksi', 'TransKlinikController@store');
        $router->get('/transaksi/{id}','TransKlinikController@show');
        $router->put('/transaksi/{id}','TransKlinikController@update');
        $router->delete('/transaksi/{id}','TransKlinikController@delete');

        #transaksi klinik
        $router->get('/transaksi','TransKlinikController@index');
        $router->post('/transaksi', 'TransKlinikController@store');
        $router->get('/transaksi/{id}','TransKlinikController@show');
        $router->put('/transaksi/{id}','TransKlinikController@update');
        $router->delete('/transaksi/{id}','TransKlinikController@delete');

        #rekam medis
        $router->get('/rekam_medis/{pasien_id}','RekamMedisController@index');

    });
    
    $router->post('/test', 'OperatorController@test');
});
