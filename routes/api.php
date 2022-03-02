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
//Route::get('/list', [\App\Http\Controllers\Api\UserController::class, 'getUsers'])->name('getUsers');
Route::get('/getTopPosts', [\App\Http\Controllers\Api\PostController::class, 'getTopPosts'])->name('getTopPosts');
Route::get('/getComment', [\App\Http\Controllers\Api\PostController::class, 'serachIntoComments'])->name('serachIntoComments');
Route::get('/commentedPosts', [\App\Http\Controllers\Api\PostController::class, 'CommentedPosts'])->name('CommentedPosts');
Route::get('/postsByNumberOfComments', [\App\Http\Controllers\Api\PostController::class, 'postsByNumberOfComments'])->name('postsByNumberOfComments');
Route::get('/serachIntoComments', [\App\Http\Controllers\Api\PostController::class, 'serachIntoComments'])->name('serachIntoComments');
Route::get('/getmails', [\App\Http\Controllers\Api\UserController::class, 'mapUsersWithPosts'])->name('mapUsersWithPosts');
Route::get('/maps', [\App\Http\Controllers\Api\UserController::class, 'UserCommentOn'])->name('UserCommentOn');
Route::get('/getTopPostsSorted', [\App\Http\Controllers\Api\PostController::class, 'getTopPostsSorted'])->name('getTopPostsSorted');



Route::middleware(['auth', 'role:admin'])->group(function () {
    // User is authentication and has admin role
    Route::get('/list', [\App\Http\Controllers\Api\UserController::class, 'getUsers'])->name('getUsers');

});

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});


Route::group(['middleware' => ['auth:api']], static function () {

        Route::post('/logout', [\App\Http\Controllers\Api\Auth\LoginController::class, 'logout'])->name('logout');
        Route::get('/me', [\App\Http\Controllers\Api\Auth\LoginController::class, 'getMyProfile'])->name('getMyProfile');
        Route::post('/updateProfile', [\App\Http\Controllers\Api\Auth\LoginController::class, 'updateProfile'])->name('updateProfile');
        Route::get('/getTopUsers', [\App\Http\Controllers\Api\UserController::class, 'getTheMoreActifUsers'])->name('getTheMoreActifUsers');





           Route::group(['prefix' => 'posts/'], function (){
           Route::post('/create', [\App\Http\Controllers\Api\PostController::class, 'store'])->name('store');
           Route::post('/{post_id}/comment/', [\App\Http\Controllers\Api\CommentController::class, 'comment'])->name('comment');
           Route::get('/{post_id}', [\App\Http\Controllers\Api\PostController::class, 'show'])->name('show');
           Route::get('/comment/{comment_id}', [\App\Http\Controllers\Api\CommentController::class, 'show'])->name('show');
           Route::delete('/', [\App\Http\Controllers\Api\PostController::class, 'destroy'])->name('destroy');
           Route::delete('/comment', [\App\Http\Controllers\Api\CommentController::class, 'deleteComment'])->name('deleteComment');
           Route::post('/update', [\App\Http\Controllers\Api\PostController::class, 'updatePost'])->name('apdate');
           Route::get('/comments/{id}', [\App\Http\Controllers\Api\PostController::class, 'postGetAllComments'])->name('postGetAllComments');
    });





});
