<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

use App\Models\Message;

class ContactMail extends Mailable
{
    use Queueable, SerializesModels;

    public $messageData;
    
    public function __construct(Message $message)
    {
        $this->messageData = $message;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Новое сообщение с сайта',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.contact',
            with: [
                'messageData' => $this->messageData
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
