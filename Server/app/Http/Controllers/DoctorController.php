<?php

namespace App\Http\Controllers;

use App\Models\Doctor;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
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

    public function getDoctorsByCategory($category)
    {
        // Convert slug to name
        $specialty = ucwords(str_replace('-', ' ', $category));
        try {
            $doctors = Doctor::with('user')
                ->where('specialty', $specialty)
                ->where('status', 'active')
                ->get();
            return response()->json($doctors, 200);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to fetch doctors by category', 'message' => $e->getMessage()], 500);
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

    public function getPatients(Request $request)
    {
        try {
            // Get the doctor ID from the authenticated user
            $doctorId = $request->user()->id;

            // Get all unique patients that have had consultations with this doctor
            $patients = User::whereHas('consultations', function ($query) use ($doctorId) {
                $query->where('doctor_id', $doctorId);
            })
            ->withCount([
                'consultations as consultation_count' => function ($query) use ($doctorId) {
                    $query->where('doctor_id', $doctorId);
                }
            ])
            ->with([
                'consultations' => function ($query) use ($doctorId) {
                    $query->where('doctor_id', $doctorId)
                          ->orderBy('created_at', 'desc')
                          ->take(1);
                }
            ])
            ->get()
            ->map(function ($patient) use ($doctorId) {
                // Get first and last consultation dates
                $consultations = $patient->consultations()
                    ->where('doctor_id', $doctorId)
                    ->orderBy('created_at')
                    ->get(['created_at']);

                $firstConsultation = $consultations->first();
                $lastConsultation = $consultations->last();

                // Get lab request count through consultations
                $labRequestCount = \DB::table('lab_requests')
                    ->join('consultations', 'lab_requests.consultation_id', '=', 'consultations.id')
                    ->where('consultations.patient_id', $patient->id)
                    ->where('consultations.doctor_id', $doctorId)
                    ->count();

                return [
                    'id' => $patient->id,
                    'name' => $patient->name,
                    'email' => $patient->email,
                    'phone' => $patient->phone,
                    'age' => $patient->age,
                    'gender' => $patient->gender,
                    'address' => $patient->address,
                    'profile_picture' => $patient->profile_picture,
                    'consultation_count' => $patient->consultation_count,
                    'lab_request_count' => $labRequestCount,
                    'first_consultation_date' => $firstConsultation ? $firstConsultation->created_at : null,
                    'last_consultation_date' => $lastConsultation ? $lastConsultation->created_at : null,
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $patients
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Failed to fetch patients',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function getDoctorPatients($doctorId)
    {
        try {
            // Get all unique patients that have had consultations with this doctor
            $patients = User::whereHas('consultations', function ($query) use ($doctorId) {
                $query->where('doctor_id', $doctorId);
            })
            ->withCount([
                'consultations as consultation_count' => function ($query) use ($doctorId) {
                    $query->where('doctor_id', $doctorId);
                }
            ])
            ->with([
                'consultations' => function ($query) use ($doctorId) {
                    $query->where('doctor_id', $doctorId)
                          ->orderBy('created_at', 'desc')
                          ->take(1);
                }
            ])
            ->get()
            ->map(function ($patient) use ($doctorId) {
                // Get first and last consultation dates
                $consultations = $patient->consultations()
                    ->where('doctor_id', $doctorId)
                    ->orderBy('created_at')
                    ->get(['created_at']);

                $firstConsultation = $consultations->first();
                $lastConsultation = $consultations->last();

                // Get lab request count through consultations
                $labRequestCount = \DB::table('lab_requests')
                    ->join('consultations', 'lab_requests.consultation_id', '=', 'consultations.id')
                    ->where('consultations.patient_id', $patient->id)
                    ->where('consultations.doctor_id', $doctorId)
                    ->count();

                return [
                    'id' => $patient->id,
                    'name' => $patient->name,
                    'email' => $patient->email,
                    'phone' => $patient->phone,
                    'age' => $patient->age,
                    'gender' => $patient->gender,
                    'address' => $patient->address,
                    'profile_picture' => $patient->profile_picture,
                    'consultation_count' => $patient->consultation_count,
                    'lab_request_count' => $labRequestCount,
                    'first_consultation_date' => $firstConsultation ? $firstConsultation->created_at : null,
                    'last_consultation_date' => $lastConsultation ? $lastConsultation->created_at : null,
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $patients
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Failed to fetch patients',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
