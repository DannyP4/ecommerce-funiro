<?php

namespace App\Repositories;

use App\Models\Notification;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

interface NotificationRepository
{
    public function getAllNotifications(): Collection;
    
    public function getNotificationById(int $id): ?Notification;
    
    public function getNotificationsByUserId(int $userId, int $perPage): LengthAwarePaginator;
    
    public function getUnreadNotifications(int $userId): Collection;
    
    public function createNotification(array $data): Notification;
    
    public function markAsRead(int $id): ?Notification;
    
    public function markAllAsRead(int $userId): int;
    
    public function deleteNotification(int $id): bool;
    
    public function deleteOldNotifications(int $days = 30): int;
    
    public function count(): int;
    
    public function countUnread(int $userId): int;
}
