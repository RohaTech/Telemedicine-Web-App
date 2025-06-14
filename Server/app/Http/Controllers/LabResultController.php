<?php

namespace App\Http\Controllers;

use App\Models\LabResult;
use App\Models\LabRequest; // Import the LabRequest model
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Storage;
use Exception;

class LabResultController extends Controller
{
    // Get all lab results
    public function index(Request $request)
    {
        try {
            $query = LabResult::with(['labRequest.patient', 'labRequest.doctor', 'laboratory']);

            // Filter by doctor if doctor_id is provided
            if ($request->has('doctor_id')) {
                $query->whereHas('labRequest', function($q) use ($request) {
                    $q->where('doctor_id', $request->doctor_id);
                });
            }

            // Filter by patient if patient_id is provided
            if ($request->has('patient_id')) {
                $query->whereHas('labRequest', function($q) use ($request) {
                    $q->where('patient_id', $request->patient_id);
                });
            }

            // Filter by status if provided
            if ($request->has('status')) {
                $query->whereHas('labRequest', function($q) use ($request) {
                    $q->where('status', $request->status);
                });
            }

            $labResults = $query->orderBy('created_at', 'desc')->get();

            // Transform the data to include patient information
            $labResults = $labResults->map(function ($result) {
                return [
                    'id' => $result->id,
                    'lab_request_id' => $result->lab_request_id,
                    'laboratory_id' => $result->laboratory_id,
                    'result_details' => $result->result_details,
                    'attachment' => $result->attachment,
                    'created_at' => $result->created_at,
                    'updated_at' => $result->updated_at,
                    'status' => $result->labRequest->status ?? 'unknown',
                    'lab_request' => $result->labRequest ? [
                        'id' => $result->labRequest->id,
                        'test_type' => $result->labRequest->test_type,
                        'status' => $result->labRequest->status,
                        'created_at' => $result->labRequest->created_at,
                    ] : null,
                    'patient' => $result->labRequest->patient ?? null,
                    'doctor' => $result->labRequest->doctor ?? null,
                    'laboratory' => $result->laboratory ?? null,
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $labResults
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Failed to fetch lab results',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    // Get a single lab result by ID
    public function show($id)
    {
        try {
            $labResult = LabResult::with(['labRequest', 'laboratory'])->findOrFail($id);
            return response()->json($labResult, 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Lab result not found', 'message' => $e->getMessage()], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to fetch lab result', 'message' => $e->getMessage()], 500);
        }
    }

    // Create a new lab result
    public function store(Request $request)
    {

        try {
            $fields = $request->validate([
                'lab_request_id' => 'required|exists:lab_requests,id',
                'laboratory_id' => 'required|exists:laboratories,id',
                'result_details' => 'required|string',
                'attachment' => 'required | string',
            ]);

            $labResult = LabResult::create([
                'lab_request_id' => $fields['lab_request_id'],
                'laboratory_id' => $fields['laboratory_id'],
                'result_details' => $fields['result_details'],
                'attachment' => $fields['attachment'],
            ]);

            // Update lab request status to completed
            $labRequest = LabRequest::findOrFail($fields['lab_request_id']);
            $labRequest->status = 'completed';
            $labRequest->save();

            return response()->json(['message' => 'Lab result created successfully', 'lab_result' => $labResult], 201);
        } catch (Exception $e) {
            return response()->json(['message' => 'Error creating lab result', 'error' => $e->getMessage()], 500);
        }
    }

    // Update an existing lab result
    public function update(Request $request, $id)
    {
        try {
            $labResult = LabResult::findOrFail($id);

            $fields = $request->validate([
                'lab_request_id' => 'sometimes|exists:lab_requests,id',
                'laboratory_id' => 'sometimes|exists:laboratories,id',
                'result_details' => 'sometimes|string',
                'attachment' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            ]);

            if ($request->hasFile('attachment')) {
                // Delete old attachment if exists
                if ($labResult->attachment && Storage::disk('public')->exists($labResult->attachment)) {
                    Storage::disk('public')->delete($labResult->attachment);
                }
                $fields['attachment'] = $request->file('attachment')->store('lab_results', 'public');
            }

            $labResult->update($fields);
            return response()->json(['message' => 'Lab result updated successfully', 'lab_result' => $labResult], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Lab result not found', 'message' => $e->getMessage()], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to update lab result', 'message' => $e->getMessage()], 500);
        }
    }

    // Delete a lab result
    public function destroy($id)
    {
        try {
            $labResult = LabResult::findOrFail($id);
            if ($labResult->attachment && Storage::disk('public')->exists($labResult->attachment)) {
                Storage::disk('public')->delete($labResult->attachment);
            }
            $labResult->delete();
            return response()->json(['message' => 'Lab result deleted successfully'], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Lab result not found', 'message' => $e->getMessage()], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to delete lab result', 'message' => $e->getMessage()], 500);
        }
    }
}
