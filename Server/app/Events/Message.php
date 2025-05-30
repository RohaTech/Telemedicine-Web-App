<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class Message implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;


    public $sender;
    public $timestamp;
    public $consultation_id;
    public $text;
    /**
     * Create a new event instance.
     */
    public function __construct($text, $sender, $timestamp, $consultation_id)
    {
        $this->text = $text;
        $this->sender = $sender;
        $this->timestamp = $timestamp;
        $this->consultation_id = $consultation_id;
    }


    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return ['Chat'];
    }

    public function broadcastAs(): string
    {
        return 'message';
    }
}
