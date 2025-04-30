<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class DoctorStatusUpdated extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $user;
    public $name;
    public $status;

    /**
     * Create a new message instance.
     */
    public function __construct($user, $status)
    {
        $this->user = $user;
        $this->name = $user->name;
        $this->status = $status;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Your Tenadam Doctor Status Has Been Updated',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.doctor_status_updated',
            with: [
                'name' => $this->name,
                'status' => $this->status,
            ],
        );
    }

    /**
     * Get the attachments for the message.
     */
    public function attachments(): array
    {
        return [];
    }
}