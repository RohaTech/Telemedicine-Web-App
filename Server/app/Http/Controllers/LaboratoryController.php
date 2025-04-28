<?php

namespace App\Http\Controllers;

use App\Models\Laboratory;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Hash;

use Exception;

class LaboratoryController extends Controller

{
    // Get all laboratories
    public function index()
    {
        try {
            $laboratories = Laboratory::all();
            return response()->json($laboratories, 200);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to fetch laboratories', 'message' => $e->getMessage()], 500);
        }
    }

    public function getPendingLaboratories()
    {

        try {
            $laboratories = Laboratory::where('status', 'pending')->get();
            return response()->json($laboratories, 200);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to fetch laboratories', 'message' => $e->getMessage()], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:laboratories,email',
                'password' => 'required|string|min:6',
                'phone' => 'nullable|string|max:20',
                'license' => 'nullable|string',
                'region' => 'nullable|string',
                'city' => 'nullable|string',
                'location' => 'nullable|array',  // Changed to array
                'tests' => 'required|array',
            ]);

            $laboratory = Laboratory::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' =>  Hash::make($validated['password']),
                'phone' => $validated['phone'],
                'license' => $validated['license'],
                'region' => $validated['region'],
                'city' => $validated['city'],
                'location' => json_encode($validated['location']), // Encoding to JSON here
                'status' => "pending",
                'tests' => json_encode($validated['tests']),
            ]);

            return response()->json(['message' => 'Laboratory registered successfully', 'laboratory' => $laboratory], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['error' => 'Validation failed', 'messages' => $e->errors()], 422);
        } catch (Exception $e) {
            return response()->json(['error' => 'Laboratory registration failed', 'message' => $e->getMessage()], 500);
        }
    }

    public function show($id)
    {
        try {
            $laboratory = Laboratory::findOrFail($id);
            $laboratory->tests = json_decode($laboratory->tests);
            $laboratory->location = json_decode($laboratory->location);
            return response()->json($laboratory, 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Laboratory not found', 'message' => $e->getMessage()], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to fetch laboratory', 'message' => $e->getMessage()], 500);
        }
    }

    // Update an existing laboratory
    public function update(Request $request, Laboratory $laboratory)
    {
        try {
            $validatedData = $request->validate([
                'name' => 'sometimes|string|max:255',
                'email' => 'sometimes|email|unique:laboratories,email,' . $laboratory->id,
                'password' => 'sometimes|string|min:8',
                'phone' => 'sometimes|string|max:20|unique:laboratories,phone,' . $laboratory->id,
                'license' => 'sometimes|string|nullable',
                'tests' => 'sometimes|array|nullable',
                'status' => 'sometimes|string|in:active,pending,suspended,expired',
                'region' => 'sometimes|string|nullable',
                'city' => 'sometimes|string|nullable',
                'location' => 'sometimes|array|nullable',
            ]);

            // Hash the password if provided
            if (isset($validatedData['password'])) {
                $validatedData['password'] = Hash::make($validatedData['password']);
            }

            // Convert arrays to JSON before updating
            if (isset($validatedData['tests'])) {
                $validatedData['tests'] = json_encode($validatedData['tests']);
            }

            if (isset($validatedData['location'])) {
                $validatedData['location'] = json_encode($validatedData['location']);
            }

            $laboratory->update($validatedData);

            // Decode JSON fields for the response
            $laboratory->refresh();
            $laboratory->tests = json_decode($laboratory->tests);
            $laboratory->location = json_decode($laboratory->location);

            return response()->json([
                'message' => 'Laboratory updated successfully',
                'laboratory' => $laboratory
            ], 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['error' => 'Validation failed', 'messages' => $e->errors()], 422);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to update laboratory', 'message' => $e->getMessage()], 500);
        }
    }

    public function updateLaboratoryStatus(Request $request, Laboratory $laboratory)
    {
        try {
            $validatedData = $request->validate([
                'status' => 'required|string|in:active,pending,suspended,expired',
            ]);

            $laboratory->status = $validatedData['status'];
            $laboratory->save();

            // Decode JSON fields for the response
            $laboratory->tests = json_decode($laboratory->tests);
            $laboratory->location = json_decode($laboratory->location);

            return response()->json([
                'message' => 'Laboratory status updated successfully',
                'laboratory' => $laboratory
            ], 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['error' => 'Validation failed', 'messages' => $e->errors()], 422);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to update laboratory status', 'message' => $e->getMessage()], 500);
        }
    }

    // Delete a laboratory
    public function destroy($id)
    {
        try {
            $laboratory = Laboratory::findOrFail($id);
            $laboratory->delete();
            return response()->json(['message' => 'Laboratory deleted successfully'], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Laboratory not found', 'message' => $e->getMessage()], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to delete laboratory', 'message' => $e->getMessage()], 500);
        }
    }
}
