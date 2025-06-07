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
use App\Http\Controllers\ChatController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\VideoCallController;
use App\Http\Middleware\AdminMiddleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Services\TwilioService;

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
Route::get('/users', [UserController::class, 'index']); // For patient details
Route::get('/users/{id}', [UserController::class, 'show']); // For patient details
Route::get('/users/patient', [UserController::class, 'getAllCustomers'])->middleware(AdminMiddleware::class);
Route::post('/register-doctor', [AuthController::class, 'registerDoctor']);
Route::get('/appointments/status/{status}', [AppointmentController::class, 'getByStatus'])->middleware('auth:sanctum');
Route::get('/doctor/patients', [AppointmentController::class, 'getPatientsWithAppointments'])->middleware('auth:sanctum');
Route::get('/doctors/status-active', [DoctorController::class, 'getActiveDoctors']);
Route::get('/appointments/user', [AppointmentController::class, 'getUserAppointments'])->middleware('auth:sanctum');
Route::apiResource('/appointments', AppointmentController::class);
Route::get('/doctor/appointments', [AppointmentController::class, 'getDoctorAppointments'])->middleware('auth:sanctum');
Route::apiResource('/consultations', ConsultationController::class);
Route::get('/doctors/categories/{category}', [DoctorController::class, 'getDoctorsByCategory']);
Route::apiResource('/doctors', DoctorController::class);
Route::put('/doctors/update-status/{doctor}', [DoctorController::class, 'updateStatus'])->middleware(AdminMiddleware::class);
Route::get('/doctor-dashboard', [DoctorController::class, 'dashboard'])->middleware('auth:sanctum');

Route::apiResource('/laboratories', LaboratoryController::class);
Route::get('/laboratories/status-pending', [LaboratoryController::class, 'getPendingLaboratories']);
Route::put('/laboratories/update-status/{laboratory}', [LaboratoryController::class, 'updateLaboratoryStatus']);
Route::get('/laboratories/login', [LaboratoryController::class, 'login']);
Route::get('/laboratories/register', [LaboratoryController::class, 'store']);
Route::get('/laboratories/logout', [LaboratoryController::class, 'logout']);


Route::apiResource('/lab-requests', LabRequestController::class);
Route::apiResource('/lab-results', LabResultController::class);
Route::apiResource('/prescriptions', PrescriptionController::class);
Route::apiResource('/payments', PaymentController::class);
Route::apiResource('/notifications', NotificationController::class);


Route::get('/patient/consultations', [ConsultationController::class, 'getUserConsultations'])->middleware('auth:sanctum');
Route::apiResource('/consultations', ConsultationController::class)->middleware('auth:sanctum');
Route::post('/messages', [ChatController::class, 'message']);
Route::apiResource('/chats', ChatController::class);
Route::get('/chats/consultation/{consultation_id}', [ChatController::class, 'getByConsultation']);



Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');

Route::post('/laboratories/login', [LaboratoryAuthController::class, 'login']);
Route::post('/laboratories/register', [LaboratoryAuthController::class, 'register']);
Route::post('/laboratories/logout', [LaboratoryAuthController::class, 'logout'])->middleware('auth:sanctum');




Route::middleware('auth:sanctum')->group(function () {
    Route::post('/video-call/request/{user}', [VideoCallController::class, 'requestVideoCall']);
    Route::post('/video-call/request/status/{user}', [VideoCallController::class, 'requestVideoCallStatus']);
});


Route::middleware('auth:sanctum')->post('/send-sms', function (Request $request) {
    $request->validate([
        'to' => 'required|string',
        'message' => 'required|string',
    ]);

    try {
        $twilio = new TwilioService();
        $result = $twilio->sendSMS($request->to, $request->message);
        return response()->json($result);
    } catch (Exception $e) {
        return response()->json([
            'success' => false,
            'error' => 'Twilio service error: ' . $e->getMessage()
        ], 500);
    }
});
