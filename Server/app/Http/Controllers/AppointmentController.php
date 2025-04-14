<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Exception;
use App\Services\DailyService;

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
    public function confirmAppointmentWithMeeting($id)
    {
        try {
            $appointment = Appointment::findOrFail($id);

            if ($appointment->status !== 'confirmed') {
                return response()->json(['error' => 'Appointment must be confirmed first.'], 400);
            }

            $dailyService = new DailyService();
            $meeting = $dailyService->createMeetingRoom();

            $appointment->meeting_url = $meeting['url'] ?? null;
            $appointment->save();

            return response()->json([
                'message' => 'Meeting room created successfully.',
                'meeting_url' => $appointment->meeting_url
            ], 200);

        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Appointment not found', 'message' => $e->getMessage()], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to create meeting room', 'message' => $e->getMessage()], 500);
        }
    }
}