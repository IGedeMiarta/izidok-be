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

$router->get('/sendEmail', 'UserController@sendEmail');

$router->group(['prefix'=>'api/v1'], function() use($router){
    
    $router->post('/user','UserController@store');
    $router->post('/login','UserController@login');
    $router->post('/klinik', 'KlinikController@store');

    $router->get('/activate/{token}','UserController@activate');

    #reset password
    $router->post('/forgot','UserController@forgot');
    $router->post('/reset','UserController@reset');


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

    });


    
});
