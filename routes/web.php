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

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', 'SubscribeController@index');
Route::get('/home1', 'SubscribeController@planSdk');
Route::post('/home', 'SubscribeController@subscribe');

Route::get('/plan', 'SubscribeController@planSdk');
Route::get('/subscribe', 'SubscribeController@indexSdk');
Route::post('/subscribe', 'SubscribeController@subscribeSdk');
