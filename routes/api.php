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

    Route::get('ranking', 'Api\GamesController@ranking');
    Route::get('locations', 'Api\LocationsController@all');
    Route::get('locations/{id}', 'Api\LocationsController@show');


    Route::middleware('auth.api')->group(function () {
        Route::post('game/start/single', 'Api\GamesController@startSingle');
        Route::post('game/start/team', 'Api\GamesController@startTeam');

        Route::get('ranking/personal', 'Api\GamesController@rankingPersonal');
        Route::post('logout', 'Api\Auth\LoginController@logout');
//        Route::get('posts', 'Api\PostController@index');
    });
});