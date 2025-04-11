<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Exception;

class PaymentController extends Controller
{
    // Get all payments
    public function index()
    {
        try {
            $payments = Payment::with(['patient', 'doctor'])->get();
            return response()->json($payments, 200);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to fetch payments', 'message' => $e->getMessage()], 500);
        }
    }

    // Get a single payment by ID
    public function show($id)
    {
        try {
            $payment = Payment::with(['patient', 'doctor'])->findOrFail($id);
            return response()->json($payment, 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Payment not found'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to fetch payment', 'message' => $e->getMessage()], 500);
        }
    }

    // Create a new payment
    public function store(Request $request)
    {
        try {
            $fields = $request->validate([
                'patient_id' => 'required|exists:users,id',
                'doctor_id' => 'required|exists:users,id',
                'amount' => 'required|numeric|min:0',
                'payment_method' => 'required|in:cash,credit_card,debit_card,online',
                'status' => 'required|in:pending,completed,failed',
                'transaction_id' => 'nullable|string|max:255|unique:payments,transaction_id',
            ]);

            $payment = Payment::create($fields);

            return response()->json(['message' => 'Payment created successfully', 'payment' => $payment], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['error' => 'Validation failed', 'messages' => $e->errors()], 422);
        } catch (Exception $e) {
            return response()->json(['error' => 'Error creating payment', 'message' => $e->getMessage()], 500);
        }
    }

    // Update an existing payment
    public function update(Request $request, $id)
    {
        try {
            $payment = Payment::findOrFail($id);

            $fields = $request->validate([
                'patient_id' => 'sometimes|exists:users,id',
                'doctor_id' => 'sometimes|exists:users,id',
                'amount' => 'sometimes|numeric|min:0',
                'payment_method' => 'sometimes|in:cash,credit_card,mobile_money',
                'status' => 'sometimes|in:pending,completed,failed',
                'transaction_id' => 'sometimes|nullable|string|max:255|unique:payments,transaction_id,' . $id,
            ]);

            $payment->update($fields);

            return response()->json(['message' => 'Payment updated successfully', 'payment' => $payment], 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['error' => 'Validation failed', 'messages' => $e->errors()], 422);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Payment not found'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to update payment', 'message' => $e->getMessage()], 500);
        }
    }

    // Delete a payment
    public function destroy($id)
    {
        try {
            $payment = Payment::findOrFail($id);
            $payment->delete();
            return response()->json(['message' => 'Payment deleted successfully'], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Payment not found'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to delete payment', 'message' => $e->getMessage()], 500);
        }
    }
}