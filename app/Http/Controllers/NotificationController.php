<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\NotificationService;

class NotificationController extends Controller
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    public function index()
    {
        $notifications = Notification::where('user_id', Auth::id())
                                   ->latest()
                                   ->paginate(10);

        return view('notifications.index', compact('notifications'));
    }

    public function markAsRead($id)
    {
        $notification = Notification::where('id', $id)
                                  ->where('user_id', Auth::id())
                                  ->first();

        if ($notification) {
            $notification->update(['read_at' => now()]);
        }

        return redirect()->back();
    }

    public function markAllAsRead()
    {
        Notification::where('user_id', Auth::id())
                   ->whereNull('read_at')
                   ->update(['read_at' => now()]);

        if (request()->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'Semua notifikasi telah ditandai sebagai dibaca']);
        }

        return redirect()->back()->with('success', 'Semua notifikasi telah ditandai sebagai dibaca');
    }

    public function getUnreadCount()
    {
        $count = Notification::where('user_id', Auth::id())
                            ->whereNull('read_at')
                            ->count();

        return response()->json(['count' => $count]);
    }

    public function getRecent()
    {
        $notifications = $this->notificationService->getRecentNotifications(Auth::user(), 5);

        return response()->json([
            'notifications' => $notifications->map(function($notification) {
                return [
                    'id' => $notification->id,
                    'title' => $notification->title,
                    'message' => $notification->message,
                    'type' => $notification->type,
                    'type_label' => $notification->getTypeLabel(),
                    'type_icon' => $notification->getTypeIcon(),
                    'is_read' => $notification->isRead(),
                    'created_at' => $notification->created_at->diffForHumans(),
                    'action_url' => $notification->data['action_url'] ?? null
                ];
            }),
            'unread_count' => $this->notificationService->getUnreadCount(Auth::user())
        ]);
    }

    public function show($id)
    {
        $notification = Notification::where('id', $id)
                                  ->where('user_id', Auth::id())
                                  ->firstOrFail();

        // Mark as read
        if ($notification->isUnread()) {
            $notification->markAsRead();
        }

        // Redirect to action URL if available
        if (isset($notification->data['action_url'])) {
            return redirect($notification->data['action_url']);
        }

        return redirect()->route('notifications.index');
    }
}
