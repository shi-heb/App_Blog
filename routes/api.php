<?php

use Illuminate\Http\Request;
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


Route::post('/login', [\App\Http\Controllers\Api\Auth\LoginController::class, 'login'])->name('login');
Route::post('/register', [\App\Http\Controllers\Api\Auth\RegisterController::class, 'register'])->name('register');
Route::post('/password/email', [\App\Http\Controllers\Api\Auth\ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
Route::post('/password/reset', [\App\Http\Controllers\Api\Auth\ResetPasswordController::class, 'reset'])->name('password.reset');
//Route::get('/Api/register','Api\Auth\RegisterController@register');


Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
