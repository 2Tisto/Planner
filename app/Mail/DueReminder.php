<?php

declare(strict_types=1);

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class DueReminder extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $title,
        public string $when,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: (string) __('Planner — :title', ['title' => $this->title]),
        );
    }

    public function content(): Content
    {
        return new Content(
            text: 'mail.due-reminder',
        );
    }
}
