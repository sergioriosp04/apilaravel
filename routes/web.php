<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// rutas de prueba

Route::get('/', function () {
    return view('welcome');
});

Route::get('/test-orm', 'PruebasController@testOrm');


//RUTAS DE LA API
    //RUTAS DE PRUEBAS
    Route::get('/usuariopruebas', 'UserController@pruebas');
    Route::get('/categoriapruebas', 'CategoryController@pruebas');
    Route::get('/postpruebas', 'PostController@pruebas');

    //RUTAS DEL CONTROLADOR DE USUARIO
    Route::post('/api/register', 'UserController@register');
    Route::post('/api/login', 'UserController@login');
