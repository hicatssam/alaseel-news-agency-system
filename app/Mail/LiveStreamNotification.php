<?php

namespace App\Mail;

use App\Models\LiveStream;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\URL;

class LiveStreamNotification extends Mailable
{
    use Queueable, SerializesModels;

    public string $emailLocale;

    public function __construct(
        public LiveStream $stream,
        public string $subscriberEmail,
        ?string $emailLocale = null
    ) {
        $this->emailLocale = $emailLocale ?? App::getLocale();
    }

    public function envelope(): Envelope
    {
        App::setLocale($this->emailLocale);

        return new Envelope(
            subject: __('admin.email_live_stream_subject', ['title' => $this->stream->title]),
        );
    }

    public function content(): Content
    {
        App::setLocale($this->emailLocale);

        // Generate a signed unsubscribe URL valid for 30 days, tied to this specific subscriber
        $unsubscribeUrl = URL::signedRoute(
            'newsletter.unsubscribe',
            ['email' => $this->subscriberEmail],
            now()->addDays(30)
        );

        return new Content(
            view: 'emails.live-stream-notification',
            with: [
                'stream'         => $this->stream,
                'emailLocale'    => $this->emailLocale,
                'isRtl'          => $this->emailLocale === 'ar',
                'unsubscribeUrl' => $unsubscribeUrl,
            ],
        );
    }
}
