<?php

namespace App\Jobs;

use App\Mail\LiveStreamNotification;
use App\Models\LiveStream;
use App\Models\NewsletterSubscriber;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Mail;

class NotifyLiveStreamSubscribers implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public LiveStream $stream) {}

    /**
     * Send a live-stream-started email to every active newsletter subscriber.
     * Each email carries a per-subscriber signed unsubscribe link.
     * Processed in chunks of 50 to keep memory usage low.
     */
    public function handle(): void
    {
        $locale = App::getLocale();

        NewsletterSubscriber::active()
            ->select('email')
            ->chunk(50, function ($subscribers) use ($locale) {
                foreach ($subscribers as $subscriber) {
                    Mail::to($subscriber->email)->send(
                        new LiveStreamNotification($this->stream, $subscriber->email, $locale)
                    );
                }
            });
    }
}
