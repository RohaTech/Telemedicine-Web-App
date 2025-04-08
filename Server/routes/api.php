<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController; 
use App\Http\Controllers\AppointmentController; 
use App\Http\Controllers\ConsultationController; 
use App\Http\Controllers\DoctorController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('/users', UserController::class);
});
Route::apiResource('/appointments', AppointmentController::class); 
Route::apiResource('/consultations', ConsultationController::class);
Route::apiResource('/doctors', DoctorController::class);


Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
