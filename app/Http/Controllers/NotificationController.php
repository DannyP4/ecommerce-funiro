<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\JsonResponse;
use App\Models\User;

class NotificationController extends Controller
{
    /**
     * Get the count of unread notifications for the authenticated admin.
     */
    public function getUnreadCount(): JsonResponse
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();

        if (!$user || $user->role_id !== 1) { // Assuming role_id 1 is for admin
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $count = $user->notifications()->where('is_read', false)->count();

        return response()->json(['unread_count' => $count]);
    }

    /**
     * Get 5-10 most recent notifications for the authenticated admin.
     */
    public function getRecent(Request $request): JsonResponse
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();

        if (!$user || $user->role_id !== 1) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $limit = $request->query('limit', 10); // Default to 10, can be adjusted by query param
        $notifications = $user->notifications()
                               ->orderBy('created_at', 'desc')
                               ->limit($limit)
                               ->get();

        return response()->json($notifications);
    }

    /**
     * Mark a specific notification as read.
     */
    public function markAsRead($notification_id): JsonResponse
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();

        // Retrieve the notification using notification_id
        $notification = Notification::where('notification_id', $notification_id)->firstOrFail();

        if (!$user || $notification->user_id !== $user->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        if (!$notification->is_read) {
            $notification->update(['is_read' => true, 'read_at' => now()]);
        }

        return response()->json(['message' => 'Notification marked as read.', 'notification' => $notification]);
    }

    /**
     * Mark all unread notifications for the authenticated admin as read.
     */
    public function markAllAsRead(): JsonResponse
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();

        if (!$user || $user->role_id !== 1) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $user->notifications()->where('is_read', false)->update(['is_read' => true, 'read_at' => now()]);

        return response()->json(['message' => 'All notifications marked as read.']);
    }

    /**
     * Display a paginated listing of all notifications for the authenticated admin.
     */
    public function index(Request $request): JsonResponse
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();

        if (!$user || $user->role_id !== 1) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $notifications = $user->notifications()
                               ->orderBy('created_at', 'desc')
                               ->paginate(15);

        return response()->json($notifications);
    }
}
