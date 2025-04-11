<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Exception;

class NotificationController extends Controller
{
    // Get all notifications
    public function index()
    {
        try {
            $notifications = Notification::with('user')->get();
            return response()->json($notifications, 200);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to fetch notifications', 'message' => $e->getMessage()], 500);
        }
    }

    // Get a single notification by ID
    public function show($id)
    {
        try {
            $notification = Notification::with('user')->findOrFail($id);
            return response()->json($notification, 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Notification not found'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to fetch notification', 'message' => $e->getMessage()], 500);
        }
    }

    // Create a new notification
    public function store(Request $request)
    {
        try {
            $fields = $request->validate([
                'user_id' => 'required|exists:users,id',
                'message' => 'required|string|max:255',
                'status' => 'required|in:unread,read', // Assuming status can be 'unread' or 'read'
            ]);

            $notification = Notification::create($fields);

            return response()->json(['message' => 'Notification created successfully', 'notification' => $notification], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['error' => 'Validation failed', 'messages' => $e->errors()], 422);
        } catch (Exception $e) {
            return response()->json(['error' => 'Error creating notification', 'message' => $e->getMessage()], 500);
        }
    }

    // Update an existing notification
    public function update(Request $request, $id)
    {
        try {
            $notification = Notification::findOrFail($id);

            $fields = $request->validate([
                'user_id' => 'sometimes|exists:users,id',
                'message' => 'sometimes|string|max:255',
                'status' => 'sometimes|in:unread,read',
            ]);

            $notification->update($fields);

            return response()->json(['message' => 'Notification updated successfully', 'notification' => $notification], 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['error' => 'Validation failed', 'messages' => $e->errors()], 422);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Notification not found'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to update notification', 'message' => $e->getMessage()], 500);
        }
    }

    // Delete a notification
    public function destroy($id)
    {
        try {
            $notification = Notification::findOrFail($id);
            $notification->delete();
            return response()->json(['message' => 'Notification deleted successfully'], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Notification not found'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to delete notification', 'message' => $e->getMessage()], 500);
        }
    }
}