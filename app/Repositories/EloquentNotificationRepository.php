<?php

namespace App\Repositories;

use App\Models\Notification;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Carbon\Carbon;

class EloquentNotificationRepository implements NotificationRepository
{
    public function getAllNotifications(): Collection
    {
        return Notification::with('user')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function getNotificationById(int $id): ?Notification
    {
        return Notification::with('user')->find($id);
    }

    public function getNotificationsByUserId(int $userId, int $perPage): LengthAwarePaginator
    {
        return Notification::where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    public function getUnreadNotifications(int $userId): Collection
    {
        return Notification::where('user_id', $userId)
            ->where('is_read', false)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function createNotification(array $data): Notification
    {
        return Notification::create($data);
    }

    public function markAsRead(int $id): ?Notification
    {
        $notification = $this->getNotificationById($id);

        if (!$notification) {
            return null;
        }

        $notification->update(['is_read' => true]);

        return $notification->fresh();
    }

    public function markAllAsRead(int $userId): int
    {
        return Notification::where('user_id', $userId)
            ->where('is_read', false)
            ->update(['is_read' => true]);
    }

    public function deleteNotification(int $id): bool
    {
        $notification = $this->getNotificationById($id);

        if (!$notification) {
            return false;
        }

        return $notification->delete();
    }

    public function deleteOldNotifications(int $days = 30): int
    {
        $date = Carbon::now()->subDays($days);
        
        return Notification::where('created_at', '<', $date)
            ->where('is_read', true)
            ->delete();
    }

    public function count(): int
    {
        return Notification::count();
    }

    public function countUnread(int $userId): int
    {
        return Notification::where('user_id', $userId)
            ->where('is_read', false)
            ->count();
    }
}
