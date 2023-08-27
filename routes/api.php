<?php

use App\Http\Controllers\Auth\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::post('register',[AuthController::class,'register']);
Route::post('login',[AuthController::class,'login']);



//IF USER ARE REGISTERED AND TOKEN IS AVAILABLE THEN THEY CAN ACCESS IT
Route::middleware('auth:api')->group(function () {
    // protected routes go here
    Route::get('test',[AuthController::class,'test']);
});