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

    public function store(Request $request)
    {


        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:laboratories,email',
                'password' => 'required|string|min:6',
                'phone' => 'nullable|string|max:20',
                'address' => 'nullable|string',
                'tests' => 'required|array',
            ]);

            $laboratory = Laboratory::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' =>  Hash::make($validated['password']),
                'phone' => $validated['phone'],
                'address' => $validated['address'],
                'tests' => json_encode($validated['tests']),
            ]);

            return response()->json(['message' => 'Laboratory registered successfully', 'laboratory' => $laboratory], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['error' => 'Validation failed', 'messages' => $e->errors()], 422);
        } catch (Exception $e) {
            return response()->json(['error' => 'Laboratory registration failed', 'message' => $e->getMessage()], 500);
        }
    }

    // Get a single laboratory by ID
    public function show($id)
    {
        try {
            $laboratory = Laboratory::findOrFail($id);
            return response()->json($laboratory, 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Laboratory not found', 'message' => $e->getMessage()], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to fetch laboratory', 'message' => $e->getMessage()], 500);
        }
    }

    // Update an existing laboratory
    public function update(Request $request, $id)
    {
        try {
            $laboratory = Laboratory::findOrFail($id);

            $validatedData = $request->validate([
                'name' => 'sometimes|string|max:255',
                'email' => 'sometimes|email|unique:laboratories,email,' . $id,
                'password' => 'sometimes|string|min:8',
                'phone' => 'sometimes|string|max:20',
                'address' => 'sometimes|string|max:255',
            ]);

            // Hash the password if provided
            if (isset($validatedData['password'])) {
                $validatedData['password'] = bcrypt($validatedData['password']);
            }

            $laboratory->update($validatedData);
            return response()->json($laboratory, 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Laboratory not found', 'message' => $e->getMessage()], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to update laboratory', 'message' => $e->getMessage()], 500);
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
