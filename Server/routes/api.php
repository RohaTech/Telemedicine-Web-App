<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\ConsultationController;
use App\Http\Controllers\DoctorController;
use App\Http\Controllers\LaboratoryController;
use App\Http\Controllers\LaboratoryAuthController;
use App\Http\Controllers\LabRequestController;
use App\Http\Controllers\LabResultController;
use App\Http\Controllers\PrescriptionController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\NotificationController;
use App\Http\Middleware\AdminMiddleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
Route::get('/doctor-status', function (Request $request) {
    $doctor = $request->user()->doctor;
    return response()->json([
        'status' => $doctor->status,
    ], 200);
})->middleware('auth:sanctum');
// Route::middleware('auth:sanctum')->group(function () {
//     Route::apiResource('/users', UserController::class);
// });

Route::get('/users/patient', [UserController::class, 'getAllCustomers'])->middleware(AdminMiddleware::class);;
Route::post('/register-doctor', [AuthController::class, 'registerDoctor']);

Route::apiResource('/appointments', AppointmentController::class);
Route::apiResource('/consultations', ConsultationController::class);
Route::apiResource('/doctors', DoctorController::class);



Route::apiResource('/laboratories', LaboratoryController::class);
Route::get('/laboratories/pending', [LaboratoryController::class, 'getPendingLaboratories']);



Route::apiResource('/lab-requests', LabRequestController::class);
Route::apiResource('/lab-results', LabResultController::class);
Route::apiResource('/prescriptions', PrescriptionController::class);
Route::apiResource('/payments', PaymentController::class);
Route::apiResource('/notifications', NotificationController::class);




Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');

Route::post('/laboratories/login', [LaboratoryAuthController::class, 'login']);
Route::post('/laboratories/register', [LaboratoryAuthController::class, 'register']);
Route::post('/laboratories/logout', [LaboratoryAuthController::class, 'logout'])->middleware('auth:sanctum');
