<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $fields = $request->validate([
            'name' => 'required|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:8|confirmed ',
            'role' => 'required|in:admin,doctor,patient', // Optional, based on schema
            'phone' => 'nullable|string|max:20|unique:users',           // Optional, based on schema
            'address' => 'nullable|string',                 // Optional, JSON in schema
        ]);

        $user = User::create([
            'name' => $fields['name'],
            'email' => $fields['email'],
            'password' => Hash::make($fields['password']), // Hash password manually if not using 'hashed' cast
            'role' => $fields['role'] ?? 'patient',        // Default to 'patient' if not provided
            'phone' => $fields['phone'] ?? null,
            'address' => $fields['address'] ?? null,
        ]);

        $token = $user->createToken($request->name)->plainTextToken;

        return response()->json([
            'user' => $user,
            'token' => $token,
        ], 201);
    }
    public function login(Request $request)
    {
        $fields = $request->validate([
            'email' => 'required|email|exists:users',
            'password' => 'required',
        ]);

        $user = User::where('email', $fields['email'])->first();

        if (!$user || !Hash::check($fields['password'], $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        $token = $user->createToken($user->name)->plainTextToken;
        return response()->json([
            'user' => $user,
            'token' => $token,
        ], 200);
    }
    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();
        return ['message' => 'Your are log out'];
    }
}
