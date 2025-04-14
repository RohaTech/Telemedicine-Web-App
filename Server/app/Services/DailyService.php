<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class DailyService
{
    public function createMeetingRoom()
    {
        $response = Http::withToken(env('DAILY_API_KEY'))
            ->post('https://api.daily.co/v1/rooms', [
                'properties' => [
                    'enable_chat' => true,
                    'enable_screenshare' => true,
                    'start_video_off' => false,
                    'start_audio_off' => false,
                ]
            ]);

        return $response->json();
    }
}
