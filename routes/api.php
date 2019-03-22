<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::group(["prefix" => "/time-travellers/api/v1/app"], function () {

    Route::post('login', 'Api\Auth\LoginController@login');

    Route::get('locations', 'Api\LocationsController@all');


    Route::middleware('auth.api')->group(function () {
        Route::post('logout', 'Api\Auth\LoginController@logout');
//        Route::get('posts', 'Api\PostController@index');
    });
});