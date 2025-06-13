<?php

use Illuminate\Support\Facades\Route;
use App\Models\Consultation;
use App\Models\User;

// Test route to verify consultation data
Route::get('/test-consultations/{patientId}', function ($patientId) {
    try {
        $consultations = Consultation::where('patient_id', $patientId)
            ->with(['doctor.doctor', 'prescription', 'appointment'])
            ->orderBy('consultation_date', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'patient_id' => $patientId,
            'consultations_count' => $consultations->count(),
            'consultations' => $consultations,
            'raw_data' => $consultations->toArray()
        ]);
    } catch (Exception $e) {
        return response()->json([
            'success' => false,
            'error' => $e->getMessage(),
            'patient_id' => $patientId
        ]);
    }
});

// Test route to verify patient exists
Route::get('/test-patients', function () {
    $patients = User::where('role', 'patient')->get(['id', 'name', 'email']);
    return response()->json([
        'success' => true,
        'patients' => $patients
    ]);
});
