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
                'consultation_id' => 'nullable|exists:consultations,id',
                'amount' => 'required|numeric|min:0',
                'payment_method' => 'required|in:cash,credit_card,debit_card,card,mobile_money,paypal,online',
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
                'consultation_id' => 'sometimes|exists:consultations,id',
                'amount' => 'sometimes|numeric|min:0',
                'payment_method' => 'sometimes|in:cash,credit_card,debit_card,card,mobile_money,paypal',
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

    // Check payment status for a consultation
    public function checkConsultationPaymentStatus($consultationId)
    {
        try {
            // Get the consultation to verify it exists and get doctor/patient info
            $consultation = \App\Models\Consultation::with(['doctor', 'patient'])->findOrFail($consultationId);
            
            // Check if payment exists for this consultation
            $payment = Payment::where('consultation_id', $consultationId)
                             ->where('status', 'completed')
                             ->first();

            return response()->json([
                'success' => true,
                'data' => [
                    'consultation_id' => $consultationId,
                    'paid' => $payment ? true : false,
                    'payment' => $payment,
                    'consultation_fee' => $consultation->doctor->doctor->payment ?? 50
                ]
            ], 200);
        } catch (\App\Models\ModelNotFoundException $e) {
            return response()->json(['error' => 'Consultation not found'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to check payment status', 'message' => $e->getMessage()], 500);
        }
    }

    // Create payment intent (for future Stripe integration)
    public function createPaymentIntent(Request $request)
    {
        try {
            $fields = $request->validate([
                'consultation_id' => 'required|exists:consultations,id',
                'amount' => 'required|numeric|min:0',
            ]);

            // Here you would integrate with Stripe or other payment gateway
            // For now, we'll return a mock intent
            $paymentIntent = [
                'id' => 'pi_' . uniqid(),
                'client_secret' => 'pi_' . uniqid() . '_secret',
                'amount' => $fields['amount'] * 100, // Convert to cents for Stripe
                'currency' => 'usd',
                'status' => 'requires_payment_method'
            ];

            return response()->json([
                'success' => true,
                'data' => $paymentIntent
            ], 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['error' => 'Validation failed', 'messages' => $e->errors()], 422);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to create payment intent', 'message' => $e->getMessage()], 500);
        }
    }

    // Confirm payment (for future Stripe integration)
    public function confirmPayment(Request $request)
    {
        try {
            $fields = $request->validate([
                'consultation_id' => 'required|exists:consultations,id',
                'payment_intent_id' => 'required|string',
                'payment_method' => 'required|in:card,mobile_money,paypal',
                'amount' => 'required|numeric|min:0',
            ]);

            // Get consultation details
            $consultation = \App\Models\Consultation::with(['doctor', 'patient'])->findOrFail($fields['consultation_id']);

            // Create payment record
            $payment = Payment::create([
                'patient_id' => $consultation->patient_id,
                'doctor_id' => $consultation->doctor_id,
                'consultation_id' => $fields['consultation_id'],
                'amount' => $fields['amount'],
                'payment_method' => $fields['payment_method'],
                'status' => 'completed',
                'transaction_id' => $fields['payment_intent_id'],
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Payment confirmed successfully',
                'data' => $payment
            ], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['error' => 'Validation failed', 'messages' => $e->errors()], 422);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to confirm payment', 'message' => $e->getMessage()], 500);
        }
    }
}