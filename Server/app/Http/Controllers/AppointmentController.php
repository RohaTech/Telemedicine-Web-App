<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Exception;

class AppointmentController extends Controller
{
    // Get all appointments
    public function index()
    {
        try {
            $appointments = Appointment::all();
            return response()->json($appointments, 200);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to fetch appointments', 'message' => $e->getMessage()], 500);
        }
    }

    // Get a single appointment by ID
    public function show($id)
    {
        try {
            $appointment = Appointment::findOrFail($id);
            return response()->json($appointment, 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Appointment not found', 'message' => $e->getMessage()], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to fetch appointment', 'message' => $e->getMessage()], 500);
        }
    }

    // Create a new appointment
    public function store(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'patient_id' => 'required|exists:users,id',
                'doctor_id' => 'required|exists:users,id',
                'appointment_date' => 'required|date',
                'time' => 'required',
                'status' => 'required|string|in:pending,confirmed,cancelled',
            ]);

            $appointment = Appointment::create($validatedData);
            return response()->json($appointment, 201);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to create appointment', 'message' => $e->getMessage()], 500);
        }
    }

    // Update an existing appointment
    public function update(Request $request, $id)
    {
        try {
            $appointment = Appointment::findOrFail($id);

            $validatedData = $request->validate([
                'patient_id' => 'sometimes|exists:users,id',
                'doctor_id' => 'sometimes|exists:users,id',
                'appointment_date' => 'sometimes|date',
                'time' => 'sometimes|string',
                'status' => 'sometimes|string',
            ]);

            $appointment->update($validatedData);
            return response()->json($appointment, 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Appointment not found', 'message' => $e->getMessage()], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to update appointment', 'message' => $e->getMessage()], 500);
        }
    }

    // Delete an appointment
    public function destroy($id)
    {
        try {
            $appointment = Appointment::findOrFail($id);
            $appointment->delete();
            return response()->json(['message' => 'Appointment deleted successfully'], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Appointment not found', 'message' => $e->getMessage()], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to delete appointment', 'message' => $e->getMessage()], 500);
        }
    }

    public function getDoctorAppointments(Request $request)
    {
        try {
            $doctorId = auth()->id();
            if (!$doctorId) {
                return response()->json(['error' => 'Unauthenticated'], 401);
            }
    
            $appointments = Appointment::with('patient')
                ->where('doctor_id', $doctorId)
                ->orderBy('appointment_date', 'asc')
                ->get();
    
            if ($appointments->isEmpty()) {
                return response()->json(['message' => 'No appointments found for this doctor'], 200);
            }
    
            $formattedAppointments = $appointments->map(function ($appointment) {
                return [
                    'id' => $appointment->id,
                    'patientName' => $appointment->patient ? $appointment->patient->name : 'Unknown Patient',
                    'date' => $appointment->appointment_date->format('Y-m-d'), // e.g., "2025-04-10"
                    'time' => $appointment->time, // e.g., "14:00:00"
                    'status' => $appointment->status, // e.g., "pending"
                ];
            });
    
            return response()->json($formattedAppointments, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to fetch appointments', 'message' => $e->getMessage()], 500);
        }
    }

    public function getPatientsWithAppointments(Request $request)
{
    // dd("request");
    try {
        $doctorId = auth('sanctum')->id();
        // dd($doctorId);

        if (!$doctorId) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }

        // Fetch patients who have appointments with this doctor
        $patients = Appointment::where('doctor_id', $doctorId)
            ->with('patient')
            ->get()
            ->pluck('patient')
            ->filter() // Remove null values (if any)
            ->unique('id') // Ensure unique patients by ID
            ->values()
            ->map(function ($patient) {
                return [
                    'id' => $patient->id,
                    'name' => $patient->name,
                ];
            });

        if ($patients->isEmpty()) {
            return response()->json(['message' => 'No patients with prior appointments found'], 200);
        }

        return response()->json($patients, 200);
    } catch (\Exception $e) {
        return response()->json(['error' => 'Failed to fetch patients', 'message' => $e->getMessage()], 500);
    }
}

}