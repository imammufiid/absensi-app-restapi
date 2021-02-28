<?php

use Illuminate\Support\Facades\Route;

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

Route::namespace('Auth')->prefix('auth')->group(function () {
    Route::post('register', 'RegistrationController');
    Route::post('login', 'LoginController');
    Route::post('logout', 'LogoutController');
});

Route::group([
    "namespace" => 'Task',
    "prefix"    => 'task'
], function() {
    Route::get('/', 'TaskController@index');
    Route::get('/show', 'TaskController@show');
    Route::put('/mark', 'TaskController@markComplete');
    Route::post('/store', 'TaskController@store');
});
