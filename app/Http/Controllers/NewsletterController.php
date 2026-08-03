<?php

namespace App\Http\Controllers;

use App\Models\NewsletterSubscriber;
use Illuminate\Http\Request;

class NewsletterController extends Controller
{
    /** Subscribe from the live-stream page (or any page with the form). */
    public function subscribe(Request $request)
    {
        $request->validate(['email' => 'required|email|max:255']);

        $email = strtolower(trim($request->email));

        $existing = NewsletterSubscriber::where('email', $email)->first();

        if ($existing) {
            if ($existing->status !== 'active') {
                $existing->update(['status' => 'active', 'subscribed_at' => now(), 'unsubscribed_at' => null]);
                return back()->with('subscribe_success', __('messages.live_notify_success'));
            }
            return back()->with('subscribe_info', __('messages.live_notify_already'));
        }

        NewsletterSubscriber::create([
            'email'         => $email,
            'status'        => 'active',
            'subscribed_at' => now(),
        ]);

        return back()->with('subscribe_success', __('messages.live_notify_success'));
    }

    /** One-click unsubscribe from email links. */
    public function unsubscribe(Request $request)
    {
        $email = strtolower(trim($request->query('email', '')));

        if ($email) {
            NewsletterSubscriber::where('email', $email)
                ->update(['status' => 'unsubscribed', 'unsubscribed_at' => now()]);
        }

        return redirect('/')->with('subscribe_info', __('messages.live_notify_unsub_done'));
    }
}
