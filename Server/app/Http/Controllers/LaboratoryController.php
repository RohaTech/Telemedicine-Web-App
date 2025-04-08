<?php

namespace App\Http\Controllers;

use App\Models\Laboratory;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;
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