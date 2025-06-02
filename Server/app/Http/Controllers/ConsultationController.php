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
            $consultation = Consultation::findOrFail($id);
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
}
