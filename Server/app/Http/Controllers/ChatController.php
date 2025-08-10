<?php

namespace App\Http\Controllers;

use App\Models\Chat;
use Illuminate\Http\Request;
use App\Events\Message;
class ChatController extends Controller
{

    public function message(Request $request)
    {
        $validated = $request->validate([
            'consultation_id' => 'required|exists:consultations,id',
            'sender' => 'required|string',
            'text' => 'required|string',
            'timestamp' => 'required|date',
        ]);

        $chat = Chat::create($validated);
        // dd($chat);
        event(new Message($chat->text, $chat->sender, $chat->timestamp, $chat->consultation_id));
        return response()->json($chat, 201);

    }

    public function index(Request $request)
    {
        $consultationId = $request->query('consultation_id');
        if ($consultationId) {
            return response()->json(Chat::where('consultation_id', $consultationId)->get());
        }
        return response()->json(Chat::all());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'consultation_id' => 'required|exists:consultations,id',
            'sender' => 'required|string',
            'text' => 'required|string',
            'timestamp' => 'required|date',
        ]);

        $chat = Chat::create($validated);
        return response()->json($chat, 201);
    }

    public function show($id)
    {
        $chat = Chat::find($id);
        if (!$chat) {
            return response()->json(['message' => 'Chat not found'], 404);
        }
        return response()->json($chat);
    }

    public function destroy($id)
    {
        $chat = Chat::find($id);
        if (!$chat) {
            return response()->json(['message' => 'Chat not found'], 404);
        }
        $chat->delete();
        return response()->json(['message' => 'Chat deleted successfully']);
    }

    public function getByConsultation($consultation_id)
    {
        try {
            $chats = Chat::where('consultation_id', $consultation_id)
                        ->orderBy('timestamp', 'asc')
                        ->get();

            return response()->json($chats);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to fetch chat messages',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}