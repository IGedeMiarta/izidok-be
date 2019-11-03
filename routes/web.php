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

$router->post('/register','AuthController@register');
$router->post('/login','AuthController@login');
$router->post('/logout','AuthController@logout');

$router->get('/klinik', 'KlinikController@getKlinik');
$router->get('/klinik/{id}', 'KlinikController@getKlinikById');
$router->post('/klinik', 'KlinikController@saveKlinik');
$router->put('/klinik/{id}', 'KlinikController@updateKlinik');
$router->delete('/klinik/{id}', 'KlinikController@deleteKlinik');
