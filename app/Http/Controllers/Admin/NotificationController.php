<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index()
    {
        $notifications = Notification::latest()->paginate(30);
        return view('admin.notifications.index', compact('notifications'));
    }

    public function markRead(Notification $notification)
    {
        $notification->markAsRead();

        if (request()->wantsJson()) {
            return response()->json(['ok' => true]);
        }

        return back();
    }

    public function markAllRead()
    {
        Notification::whereNull('read_at')->update(['read_at' => now()]);

        if (request()->wantsJson()) {
            return response()->json(['ok' => true]);
        }

        return back()->with('success', __('admin.notif_marked_all_read'));
    }

    public function destroy(Notification $notification)
    {
        $notification->delete();

        if (request()->wantsJson()) {
            return response()->json(['ok' => true]);
        }

        return back()->with('success', __('admin.notif_deleted'));
    }

    public function destroyRead()
    {
        Notification::whereNotNull('read_at')->delete();
        return back()->with('success', __('admin.notif_cleared'));
    }
}
