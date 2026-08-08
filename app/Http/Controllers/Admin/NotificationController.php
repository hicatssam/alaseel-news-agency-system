<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;


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

  public function checkNew(Request $request): JsonResponse
{
    $validated = $request->validate([
        'after_id' => ['nullable', 'integer', 'min:0'],
    ]);

    $afterId = (int) ($validated['after_id'] ?? 0);

    $notifications = Notification::query()
        ->where('id', '>', $afterId)
        ->orderByDesc('id')
        ->limit(10)
        ->get()
        ->map(function (Notification $notification) {
            return [
                'id'         => $notification->id,
                'title'      => $notification->title,
                'message'    => $notification->message,
                'type'       => $notification->type,
                'created_at' => $notification->created_at
                    ?->diffForHumans(),
            ];
        })
        ->values();

    return response()->json([
        'has_new' => $notifications->isNotEmpty(),

        'latest_id' => (int) (
            Notification::query()->max('id') ?? $afterId
        ),

        'unread_count' => Notification::query()
            ->whereNull('read_at')
            ->count(),

        'notifications' => $notifications,
    ]);
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
