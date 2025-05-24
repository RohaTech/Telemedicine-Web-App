<?php

namespace App\Http\Controllers;

use App\Models\Doctor;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Mail\DoctorStatusUpdated;
use App\Mail\DoctorRegistrationPending;
use Illuminate\Support\Facades\Auth;
use App\Models\Appointment;
use App\Models\LabRequest;
use App\Models\Notification;
use App\Models\User;
use App\Models\Laboratory;
use App\Models\LaboratoryAuth;

class DoctorController extends Controller
{
    public function index()
    {
        try {
            $doctors = Doctor::with('user')->get();
            return response()->json($doctors, 200);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to fetch doctors', 'message' => $e->getMessage()], 500);
        }
    }

    public function getActiveDoctors()
    {
        try {
            $doctors = Doctor::with('user')->where('status', 'active')->get();
            return response()->json($doctors, 200);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to fetch active doctors', 'message' => $e->getMessage()], 500);
        }
    }

    public function show($id)
    {
        try {
            $doctor = Doctor::with('user')->findOrFail($id);
            return response()->json($doctor, 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Doctor not found', 'message' => $e->getMessage()], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to fetch doctor', 'message' => $e->getMessage()], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'user_id' => 'required|exists:users,id|unique:doctors,user_id',
                'medical_license_number' => 'required|unique:doctors',
                'specialty' => 'required|string',
                'qualification' => 'required|string',
                'experience_years' => 'required|integer|min:0',
                'university_attended' => 'required|string',
                'license_issue_date' => 'required|date',
                'license_expiry_date' => 'required|date|after:license_issue_date',
                'status' => 'required|in:pending,active,suspended,expired',
                'payment' => 'required|numeric|min:0',
                'location' => 'nullable|json',
                'certificate_path' => 'required|url',
            ]);


            $doctor = Doctor::create($validatedData);
            return response()->json($doctor, 201);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to create doctor', 'message' => $e->getMessage()], 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $doctor = Doctor::findOrFail($id);

            $validatedData = $request->validate([
                'medical_license_number' => 'sometimes|string|unique:doctors,medical_license_number,' . $id,
                'specialty' => 'sometimes|string',
                'qualification' => 'sometimes|string',
                'experience_years' => 'sometimes|integer|min:0',
                'university_attended' => 'sometimes|string',
                'license_issue_date' => 'sometimes|date',
                'license_expiry_date' => 'sometimes|date|after:license_issue_date',
                'status' => 'sometimes|in:pending,active,suspended,expired',
                'payment' => 'sometimes|required|numeric|min:0',
                'location' => 'sometimes|nullable|json',
                'certificate_path' => 'required|url',
            ]);

            $doctor->update($validatedData);
            return response()->json($doctor, 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Doctor not found', 'message' => $e->getMessage()], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to update doctor', 'message' => $e->getMessage()], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $doctor = Doctor::findOrFail($id);
            $doctor->delete();
            return response()->json(['message' => 'Doctor deleted successfully'], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Doctor not found', 'message' => $e->getMessage()], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to delete doctor', 'message' => $e->getMessage()], 500);
        }
    }

    public function updateStatus(Request $request, $id)
    {
        try {
            $doctor = Doctor::findOrFail($id);

            $validatedData = $request->validate([
                'status' => 'required|in:pending,active,suspended,expired',
            ]);

            $doctor->update($validatedData);
            // dd($doctor->user->email);
            try {
                Mail::to($doctor->user->email)->send(new DoctorStatusUpdated($doctor->user, $validatedData['status']));
                Log::info('Email sent successfully', ['user_id' => $doctor->id]);
            } catch (\Exception $e) {
                Log::error('Failed to send email', ['error' => $e->getMessage()]);
                return response()->json([
                    'error' => 'Failed to send email',
                    'message' => $e->getMessage(),
                ], 500);
            }
            return response()->json($doctor, 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Doctor not found', 'message' => $e->getMessage()], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to update doctor status', 'message' => $e->getMessage()], 500);
        }
    }

    public function dashboard(Request $request)
    {
        try {
            // Fetch the doctor record for the authenticated user
            $doctor = Doctor::where('user_id', Auth::id())->firstOrFail();

            // Gather dashboard data
            $dashboardData = [
                'upcomingAppointments' => Appointment::where('doctor_id', $doctor->user->id)
                    ->where('date', '>=', now())
                    ->count(),
                'pendingLabRequests' => LabRequest::where('doctor_id', $doctor->user->id)
                    ->where('status', 'pending')
                    ->count(),
                'unreadNotifications' => Notification::where('user_id', Auth::id())
                    ->where('status', 'unread')
                    ->count(),
            ];

            return response()->json($dashboardData, 200);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to fetch dashboard data', 'message' => $e->getMessage()], 500);
        }
    }
}
