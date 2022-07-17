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
Route::get('/setup-app',[\App\Http\Controllers\Api\SetupAppController::class,'index']);
Route::group([
    'middleware' => 'auth.api'
],function (){
    Route::post("/attachments",[\App\Http\Controllers\Api\AttachmentController::class,'store']);
    Route::post('/favorites',[\App\Http\Controllers\Api\UserController::class,'storeFavorite']);
    Route::get('/me',[\App\Http\Controllers\Api\UserController::class,'infoMe']);
    Route::patch('/me',[\App\Http\Controllers\Api\UserController::class,'updateInfo']);
    Route::patch('/me/lat-long',[\App\Http\Controllers\Api\UserController::class,'updateLatLong']);
    Route::post('/images',[\App\Http\Controllers\Api\UserController::class,'storeImage']);
    Route::patch('/images',[\App\Http\Controllers\Api\UserController::class,'updateImage']);

    Route::post('/follows',[\App\Http\Controllers\Api\FollowController::class,'store']);
});

