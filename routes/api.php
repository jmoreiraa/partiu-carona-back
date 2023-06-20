<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/


Route::controller(UserController::class)->prefix('/user')->group(function () {
    Route::get('/caronas', 'index');
    Route::put('/motorista/visible', 'visible');
    Route::put('/', 'update');
    Route::post('/motorista', 'motorista');
    Route::post('/', 'usuario');
    Route::post('/login', 'login');
});
