<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Doctor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\DB;


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
        if ($user->role === 'doctor') {
            $doctor = $user->doctor;
            if ($doctor->approval_status === 'pending') {
                return response()->json([
                    'error' => 'Your account is under review. Please wait for admin approval.',
                ], 403);
            }
            if ($doctor->approval_status === 'rejected') {
                return response()->json([
                    'error' => 'Your account was rejected. Please contact support.',
                ], 403);
            }
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

    public function registerDoctor(Request $request)
    {
        try {
            // Validate request data
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|min:8|confirmed',
                'phone' => 'required|string|max:20|unique:users',
                'medical_license_number' => 'required|string|unique:doctors',
                'specialty' => 'required|string',
                'qualification' => 'required|string',
                'experience_years' => 'required|integer|min:0',
                'university_attended' => 'required|string',
                'license_issue_date' => 'required|date',
                'license_expiry_date' => 'required|date|after:license_issue_date',
                'status' => 'nullable|in:pending,active,suspended,expired',
                'payment' => 'required|numeric|min:0',
                'location' => 'nullable|json',
                'certificate_path' => 'required|url', // Already required
            ]);

            // Use a transaction to ensure atomicity
            return DB::transaction(function () use ($validated) {
                // Create User
                $user = User::create([
                    'name' => $validated['name'],
                    'email' => $validated['email'],
                    'password' => Hash::make($validated['password']),
                    'phone' => $validated['phone'],
                    'role' => 'doctor',
                ]);

                // Create Doctor
                $doctor = Doctor::create([
                    'user_id' => $user->id,
                    'medical_license_number' => $validated['medical_license_number'],
                    'specialty' => $validated['specialty'],
                    'qualification' => $validated['qualification'],
                    'experience_years' => $validated['experience_years'],
                    'university_attended' => $validated['university_attended'],
                    'license_issue_date' => $validated['license_issue_date'],
                    'license_expiry_date' => $validated['license_expiry_date'],
                    'status' => $validated['status'],
                    'payment' => $validated['payment'],
                    'location' => $validated['location'],
                    'certificate_path' => $validated['certificate_path'],
                ]);

                // Optionally, generate an API token for the user
                $token = $user->createToken('auth_token')->plainTextToken;

                return response()->json([
                    'user' => $user,
                    'doctor' => $doctor,
                    'token' => $token,
                ], 201);
            });
        } catch (ValidationException $e) {
            return response()->json([
                'error' => 'Validation failed',
                'messages' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to register doctor',
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
