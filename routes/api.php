<?php

use App\Http\Controllers\API\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


// Route untuk logout
Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Route untuk registrasi
Route::post('/register', [AuthController::class, 'register']);

// Route untuk login
Route::post('/login', [AuthController::class, 'login']);

// Route untuk mengambil semua pengguna
Route::get('/users', [AuthController::class, 'index']);

// Route untuk mengambil data pengguna berdasarkan ID
Route::get('/user/{id}', [AuthController::class, 'getUserData']);
