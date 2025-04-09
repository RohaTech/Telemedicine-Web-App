<?php

namespace App\Http\Controllers;

use App\Models\Prescription;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Exception;

class PrescriptionController extends Controller
{
    // Get all prescriptions
    public function index()
    {
        try {
            $prescriptions = Prescription::with(['doctor', 'patient', 'consultation'])->get();
            return response()->json($prescriptions, 200);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to fetch prescriptions', 'message' => $e->getMessage()], 500);
        }
    }

    // Get a single prescription by ID
    public function show($id)
    {
        try {
            $prescription = Prescription::with(['doctor', 'patient', 'consultation'])->findOrFail($id);
            return response()->json($prescription, 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Prescription not found'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to fetch prescription', 'message' => $e->getMessage()], 500);
        }
    }

    // Create a new prescription
    public function store(Request $request)
    {
        try {
            $fields = $request->validate([
                'doctor_id' => 'required|exists:users,id',
                'patient_id' => 'required|exists:users,id',
                'consultation_id' => 'required|exists:consultations,id',
                'medicine_name' => 'required|string|max:255',
                'directions' => 'required|string',
            ]);

            $prescription = Prescription::create($fields);

            return response()->json(['message' => 'Prescription created successfully', 'prescription' => $prescription], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['error' => 'Validation failed', 'messages' => $e->errors()], 422);
        } catch (Exception $e) {
            return response()->json(['error' => 'Error creating prescription', 'message' => $e->getMessage()], 500);
        }
    }

    // Update an existing prescription
    public function update(Request $request, $id)
    {
        try {
            $prescription = Prescription::findOrFail($id);

            $fields = $request->validate([
                'doctor_id' => 'sometimes|exists:users,id',
                'patient_id' => 'sometimes|exists:users,id',
                'consultation_id' => 'sometimes|exists:consultations,id',
                'medicine_name' => 'sometimes|string|max:255',
                'directions' => 'sometimes|string',
            ]);

            $prescription->update($fields);

            return response()->json(['message' => 'Prescription updated successfully', 'prescription' => $prescription], 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['error' => 'Validation failed', 'messages' => $e->errors()], 422);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Prescription not found'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to update prescription', 'message' => $e->getMessage()], 500);
        }
    }

    // Delete a prescription
    public function destroy($id)
    {
        try {
            $prescription = Prescription::findOrFail($id);
            $prescription->delete();
            return response()->json(['message' => 'Prescription deleted successfully'], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Prescription not found'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to delete prescription', 'message' => $e->getMessage()], 500);
        }
    }
}