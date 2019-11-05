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
    
    $router->post('/user','AuthController@store');
    $router->post('/login','AuthController@login');

    #reset password
    $router->post('/forgot','AuthController@forgot');
    $router->post('/reset','AuthController@reset');

    $router->group(['middleware' => 'auth'], function() use($router){
        #user
        $router->get('/user','AuthController@index');
        $router->get('/user/{id}','AuthController@show');
        $router->put('/user/{id}','AuthController@update');
        $router->delete('/user/{id}','AuthController@delete');
        $router->post('/logout','AuthController@logout');



        #klinik
        $router->get('/klinik', 'KlinikController@index');
        $router->post('/klinik', 'KlinikController@store');
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
        $router->get('/kode_penyakit/{id}', 'KodePenyakitController@show');
        $router->put('/kode_penyakit/{id}', 'KodePenyakitController@update');
        $router->delete('/kode_penyakit/{id}', 'KodePenyakitController@delete');

    });


    
});
