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

Route::post('login-with-facebook',[\App\Http\Controllers\Api\AuthController::class,'loginFacebook']);
Route::post('login-with-email',[\App\Http\Controllers\Api\AuthController::class,'loginWithEmail']);
Route::post('register-with-email',[\App\Http\Controllers\Api\AuthController::class,'registerWithEmail']);
Route::get('/setup-app',[\App\Http\Controllers\Api\SetupAppController::class,'index']);
Route::group([
    'middleware' => 'auth.api'
],function (){
    Route::post("/attachments",[\App\Http\Controllers\Api\AttachmentController::class,'store']);
    Route::post('/favorites',[\App\Http\Controllers\Api\UserController::class,'storeFavorite']);
    Route::get('/me',[\App\Http\Controllers\Api\UserController::class,'infoMe']);
    Route::patch('/me',[\App\Http\Controllers\Api\UserController::class,'updateInfo']);
    Route::patch('/me/lat-long',[\App\Http\Controllers\Api\UserController::class,'updateLatLong']);
    Route::get("/images/{user_oid}",[\App\Http\Controllers\Api\UserController::class,'listImage']);
    Route::post('/images',[\App\Http\Controllers\Api\UserController::class,'storeImage']);
    Route::patch('/images',[\App\Http\Controllers\Api\UserController::class,'updateImage']);
    Route::post('/follows',[\App\Http\Controllers\Api\FollowController::class,'store']);
    Route::get("/users/{user_oid}",[\App\Http\Controllers\Api\UserController::class,'infoUser']);
    Route::get('/matching',[\App\Http\Controllers\Api\MatchingController::class,'index']);
    Route::get('/messages/room',[\App\Http\Controllers\Api\ChatController::class,'room']);
    Route::get('/messages/{user_oid}',[\App\Http\Controllers\Api\ChatController::class,'message']);
});

Route::get('/',function (){
   return response()->setStatusCode(404)->json(json_encode([
       'status' => 404,
       'content' => 'Page not found!',
       'data' => []
   ]));
});

