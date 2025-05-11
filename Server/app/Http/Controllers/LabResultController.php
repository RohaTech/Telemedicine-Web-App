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
    public function index()
    {
        try {
            $labResults = LabResult::with(['labRequest', 'laboratory'])->get();
            return response()->json($labResults, 200);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to fetch lab results', 'message' => $e->getMessage()], 500);
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
