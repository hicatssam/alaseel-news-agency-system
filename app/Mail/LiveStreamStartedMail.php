<?php

namespace App\Mail;

use App\Models\LiveStream;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class LiveStreamStartedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public LiveStream $stream) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: __('messages.live_email_subject', ['title' => $this->stream->title]),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.live-stream-started',
        );
    }
}
