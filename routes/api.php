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
Route::post('/logout', [\App\Http\Controllers\Api\Auth\LoginController::class, 'logout'])->name('logout');


Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});


Route::group(['middleware' => ['auth:api']], static function () {
    
        Route::post('/logout', [\App\Http\Controllers\Api\Auth\LoginController::class, 'logout'])->name('logout');
        Route::get('/me', [\App\Http\Controllers\Api\Auth\LoginController::class, 'getMyProfile'])->name('getMyProfile');
        Route::post('/updateProfile', [\App\Http\Controllers\Api\Auth\LoginController::class, 'updateProfile'])->name('updateProfile');

        Route::group(['prefix' => 'posts/'], function (){
        Route::post('/create', [\App\Http\Controllers\Api\PostController::class, 'store'])->name('store');
        Route::get('/{post_id}', [\App\Http\Controllers\Api\PostController::class, 'show'])->name('show');
        Route::delete('/', [\App\Http\Controllers\Api\PostController::class, 'destroy'])->name('destroy');
        Route::post('/update', [\App\Http\Controllers\Api\PostController::class, 'updatePost'])->name('apdate');
    });
       



});
