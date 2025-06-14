<?php

namespace App\Http\Controllers;

use App\Models\LabRequest;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Exception;

class LabRequestController extends Controller
{
    // Get all lab requests
    public function index()
    {
        try {
            $labRequests = LabRequest::with(['consultation', 'consultation.patient', 'consultation.doctor'])->get();
            return response()->json($labRequests, 200);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to fetch lab requests', 'message' => $e->getMessage()], 500);
        }
    }

    // Get a single lab request by ID
    public function show($id)
    {
        try {
            $labRequest = LabRequest::with(['consultation', 'consultation.patient', 'consultation.doctor'])->findOrFail($id);
            return response()->json($labRequest, 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Lab request not found', 'message' => $e->getMessage()], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to fetch lab request', 'message' => $e->getMessage()], 500);
        }
    }

    // Create a new lab request
    public function store(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'consultation_id' => 'required|exists:consultations,id',
                'doctor_id' => 'nullable|exists:users,id',
                'patient_id' => 'nullable|exists:users,id',
                'laboratory_id' => 'nullable|exists:laboratories,id',
                'test_type' => 'required|string|max:255',
                'status' => 'nullable|in:pending,completed,cancelled',
            ]);

            // Set default status if not provided
            if (!isset($validatedData['status'])) {
                $validatedData['status'] = 'pending';
            }

            $labRequest = LabRequest::create($validatedData);
            
            // Load relationships for response
            $labRequest->load(['consultation', 'consultation.patient', 'consultation.doctor', 'laboratory']);
            
            return response()->json($labRequest, 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'error' => 'Validation failed',
                'message' => 'The given data was invalid.',
                'errors' => $e->errors()
            ], 422);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to create lab request', 'message' => $e->getMessage()], 500);
        }
    }

    // Update an existing lab request
    public function update(Request $request, $id)
    {
        try {
            $labRequest = LabRequest::findOrFail($id);

            $validatedData = $request->validate([
                'consultation_id' => 'sometimes|exists:consultations,id',
                'laboratory_id' => 'sometimes|exists:laboratories,id',
                'test_type' => 'sometimes|string|max:255',
                'status' => 'sometimes|in:pending,completed,cancelled',
            ]);

            $labRequest->update($validatedData);
            return response()->json($labRequest, 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Lab request not found', 'message' => $e->getMessage()], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to update lab request', 'message' => $e->getMessage()], 500);
        }
    }

    // Delete a lab request
    public function destroy($id)
    {
        try {
            $labRequest = LabRequest::findOrFail($id);
            $labRequest->delete();
            return response()->json(['message' => 'Lab request deleted successfully'], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Lab request not found', 'message' => $e->getMessage()], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to delete lab request', 'message' => $e->getMessage()], 500);
        }
    }
}
