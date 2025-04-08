<?php

namespace App\Http\Controllers;

use App\Models\Laboratory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class LaboratoryAuthController extends Controller
{
    public function register(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:laboratories,email',
                'password' => 'required|confirmed|string|min:8',
                'phone' => 'nullable|string|max:20',
                'address' => 'nullable|string|max:255',
            ]);

            // Hash the password before storing
            $validatedData['password'] = bcrypt($validatedData['password']);

            $laboratory = Laboratory::create($validatedData);
            $token = $laboratory->createToken('laboratory-token')->plainTextToken;

            // Generate a token for the laboratory

            return response()->json([
                'message' => 'Laboratory registered successfully',
                'laboratory' => $laboratory,
                'token' => $token, // Return the token
            ], 201);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to register laboratory', 'message' => $e->getMessage()], 500);
        }
    }

    // Laboratory login
    public function login(Request $request)
    {
        $validatedData = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string|min:8',
        ]);

        $laboratory = Laboratory::where('email', $validatedData['email'])->first();

        if (!$laboratory || !Hash::check($validatedData['password'], $laboratory->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        // Generate a token for the laboratory
        $token = $laboratory->createToken('laboratory-token')->plainTextToken;

        return response()->json([
            'laboratory' => $laboratory,
            'token' => $token,
        ], 200);
    }

    // Laboratory logout
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Logged out successfully'], 200);
    }
}