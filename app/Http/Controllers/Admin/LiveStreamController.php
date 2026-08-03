<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Jobs\NotifyLiveStreamSubscribers;
use App\Models\LiveStream;
use App\Models\NewsletterSubscriber;
use Illuminate\Http\Request;

class LiveStreamController extends Controller
{
    public function index()
    {
        $streams = LiveStream::latest()->get();
        return view('admin.live-stream.index', compact('streams'));
    }

    public function create()
    {
        $stream = new LiveStream();
        return view('admin.live-stream.form', compact('stream'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title'              => 'required|string|max:255',
            'embed_url'          => 'required|url|max:500',
            'description'        => 'nullable|string|max:1000',
            'viewers_label'      => 'nullable|string|max:100',
            'is_active'          => 'boolean',
            'notify_subscribers' => 'boolean',
        ]);

        $notify = !empty($data['notify_subscribers']) && !empty($data['is_active']);
        unset($data['notify_subscribers']);

        if (!empty($data['is_active'])) {
            LiveStream::query()->update(['is_active' => false]);
        }

        $stream = LiveStream::create($data);

        if ($notify) {
            NotifyLiveStreamSubscribers::dispatch($stream);
        }

        return redirect()->route('admin.live-streams.index')
            ->with('success', __('admin.live_stream_created'));
    }

    public function edit(LiveStream $liveStream)
    {
        $stream = $liveStream;
        return view('admin.live-stream.form', compact('stream'));
    }

    public function update(Request $request, LiveStream $liveStream)
    {
        $data = $request->validate([
            'title'              => 'required|string|max:255',
            'embed_url'          => 'required|url|max:500',
            'description'        => 'nullable|string|max:1000',
            'viewers_label'      => 'nullable|string|max:100',
            'is_active'          => 'boolean',
            'notify_subscribers' => 'boolean',
        ]);

        $wasActive = $liveStream->is_active;
        $willBeActive = !empty($data['is_active']);
        $notify = !empty($data['notify_subscribers']) && $willBeActive && !$wasActive;
        unset($data['notify_subscribers']);

        if ($willBeActive) {
            LiveStream::where('id', '!=', $liveStream->id)->update(['is_active' => false]);
        }

        $liveStream->update($data);

        if ($notify) {
            NotifyLiveStreamSubscribers::dispatch($liveStream->fresh());
        }

        return redirect()->route('admin.live-streams.index')
            ->with('success', __('admin.live_stream_updated'));
    }

    public function destroy(LiveStream $liveStream)
    {
        $liveStream->delete();
        return redirect()->route('admin.live-streams.index')
            ->with('success', __('admin.live_stream_deleted'));
    }

    public function toggle(LiveStream $liveStream)
    {
        $wasActive = $liveStream->is_active;

        if (!$wasActive) {
            // Deactivate all others, then activate this one
            LiveStream::query()->update(['is_active' => false]);
            $liveStream->update(['is_active' => true]);

            // Notify all active newsletter subscribers
            NotifyLiveStreamSubscribers::dispatch($liveStream->fresh());
        } else {
            $liveStream->update(['is_active' => false]);
        }

        return redirect()->route('admin.live-streams.index')
            ->with('success', __('admin.live_stream_toggled'));
    }
}
