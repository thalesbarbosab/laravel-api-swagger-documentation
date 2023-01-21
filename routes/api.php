<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\UserController;

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

Route::post('/sanctum/token', [UserController::class,'authenticate']);
Route::post('/user', [UserController::class,'store']);

Route::middleware('auth:sanctum')->group(function(){
    Route::get('/me', [UserController::class,'me']);
    Route::patch('/user/change-email',  [UserController::class,'updateEmail']);
    Route::delete('/user/logout',  [UserController::class,'logout']);
});


