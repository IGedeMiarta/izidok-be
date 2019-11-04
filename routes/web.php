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
    #user
    $router->get('/user','AuthController@index');
    $router->post('/user','AuthController@store');
    $router->get('/user/{id}','AuthController@show');
    $router->put('/user/{id}','AuthController@update');
    $router->delete('/user/{id}','AuthController@delete');
    $router->post('/login','AuthController@login');
    $router->post('/logout','AuthController@logout');

    #klinik
    $router->get('/klinik', 'KlinikController@index');
    $router->post('/klinik', 'KlinikController@store');
    $router->get('/klinik/{id}', 'KlinikController@show');
    $router->put('/klinik/{id}', 'KlinikController@update');
    $router->delete('/klinik/{id}', 'KlinikController@delete');
});
