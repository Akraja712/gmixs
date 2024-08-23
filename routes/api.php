<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

Route::post('login', [AuthController::class, 'login']);


Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
