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

Route::group([
    "namespace" => 'Auth',
    "prefix"    => "auth"
], function () {
    Route::post('register', 'RegistrationController');
    Route::post('login', 'LoginController');
    Route::post('logout', 'LogoutController');
});

Route::group([
    "namespace" => 'Attendance',
    "prefix"    => 'attendance'
], function () {
    Route::get("/", 'AttendanceController@index');
    Route::post("/come", 'AttendanceController@comes');
    Route::put("/gohome", 'AttendanceController@gohome');
    Route::get("/show", 'AttendanceController@show');
});
