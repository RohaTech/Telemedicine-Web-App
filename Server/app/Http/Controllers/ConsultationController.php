<?php

namespace App\Http\Controllers;

use App\Models\Consultation;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Exception;

class ConsultationController extends Controller
{
    // Get all consultations
    public function index(Request $request)
    {
        try {

            $user = $request->user();
            $consultations = $user->consultations()->with(['doctor', 'appointment'])->get();
            return response()->json($consultations, 200);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to fetch consultations', 'message' => $e->getMessage()], 500);
        }
    }

    public function getUserConsultations(Request $request)
    {
        $user = $request->user();
        if (!$user) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }

        $consultations = $user->consultations()->with('doctor')->get();

        return response()->json($consultations, 200);
    }

    // Get a single consultation by ID
    public function show($id)
    {
        try {
            $consultation = Consultation::findOrFail($id)->with(['doctor.doctor', 'prescription', 'appointment'])->first();
            return response()->json($consultation, 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Consultation not found', 'message' => $e->getMessage()], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to fetch consultation', 'message' => $e->getMessage()], 500);
        }
    }

    // Create a new consultation
    public function store(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'patient_id' => 'required|exists:users,id',
                'doctor_id' => 'required|exists:users,id',
                'prescription_id' => 'nullable|exists:prescriptions,id',
                'consultation_date' => 'required|date',
                'notes' => 'nullable|string',
                'appointment_id' => 'required|exists:appointments,id',
            ]);

            $consultation = Consultation::create($validatedData);
            return response()->json($consultation, 201);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to create consultation', 'message' => $e->getMessage()], 500);
        }
    }

    // Update an existing consultation
    public function update(Request $request, $id)
    {
        try {
            $consultation = Consultation::findOrFail($id);

            $validatedData = $request->validate([
                'patient_id' => 'sometimes|exists:users,id',
                'doctor_id' => 'sometimes|exists:users,id',
                'prescription_id' => 'nullable|exists:prescriptions,id',
                'consultation_date' => 'sometimes|date',
                'notes' => 'nullable|string',
                'appointment_id' => 'sometimes|exists:appointments,id',
            ]);

            $consultation->update($validatedData);
            return response()->json($consultation, 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Consultation not found', 'message' => $e->getMessage()], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to update consultation', 'message' => $e->getMessage()], 500);
        }
    }

    // Delete a consultation
    public function destroy($id)
    {
        try {
            $consultation = Consultation::findOrFail($id);
            $consultation->delete();
            return response()->json(['message' => 'Consultation deleted successfully'], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Consultation not found', 'message' => $e->getMessage()], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to delete consultation', 'message' => $e->getMessage()], 500);
        }
    }

    // Get consultations for a specific patient
    public function getPatientConsultations($patientId)
    {
        try {
            $consultations = Consultation::where('patient_id', $patientId)
                ->with(['doctor.doctor', 'prescription', 'appointment'])
                ->orderBy('consultation_date', 'desc')
                ->get();

            // Transform the data to include doctor specialty and other details
            $transformedConsultations = $consultations->map(function ($consultation) {
                return [
                    'id' => $consultation->id,
                    'date' => $consultation->consultation_date,
                    'notes' => $consultation->notes,
                    'doctor_name' => $consultation->doctor->name ?? 'Unknown Doctor',
                    'doctor_specialty' => optional($consultation->doctor->doctor)->specialty ?? 'General Practice',
                    'appointment_type' => optional($consultation->appointment)->appointment_type ?? 'General Consultation',
                    'prescription' => $consultation->prescription ? [
                        'medications' => $consultation->prescription->medications ?? [],
                        'instructions' => $consultation->prescription->instructions ?? ''
                    ] : null,
                    'created_at' => $consultation->created_at,
                    'updated_at' => $consultation->updated_at
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $transformedConsultations,
                'count' => $transformedConsultations->count()
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Failed to fetch patient consultations', 
                'message' => $e->getMessage()
            ], 500);
        }
    }

    // Get consultation by appointment ID
    public function getByAppointmentId($appointmentId)
    {
        try {
            $consultation = Consultation::where('appointment_id', $appointmentId)
                ->with(['doctor.doctor', 'prescription', 'appointment'])
                ->first();

            if (!$consultation) {
                return response()->json([
                    'success' => false,
                    'error' => 'Consultation not found for this appointment'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $consultation
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Failed to fetch consultation',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    // Create consultation for appointment
    public function createForAppointment(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'appointment_id' => 'required|exists:appointments,id',
                'patient_id' => 'required|exists:users,id',
                'doctor_id' => 'required|exists:users,id',
                'notes' => 'nullable|string',
            ]);

            // Check if consultation already exists for this appointment
            $existingConsultation = Consultation::where('appointment_id', $validatedData['appointment_id'])->first();
            if ($existingConsultation) {
                return response()->json([
                    'success' => true,
                    'data' => $existingConsultation,
                    'message' => 'Consultation already exists for this appointment'
                ], 200);
            }

            // Set consultation date to current time
            $validatedData['consultation_date'] = now();

            $consultation = Consultation::create($validatedData);
            $consultation->load(['doctor.doctor', 'prescription', 'appointment']);

            return response()->json([
                'success' => true,
                'data' => $consultation
            ], 201);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Failed to create consultation',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
