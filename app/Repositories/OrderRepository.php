<?php

namespace App\Repositories;

use App\Models\Order;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

interface OrderRepository
{
    public function getAllOrders(): Collection;
    
    public function getOrderById(int $id): ?Order;
    
    public function getOrdersByUserId(int $userId, int $perPage): LengthAwarePaginator;
    
    public function getOrdersByStatus(string $status, int $perPage): LengthAwarePaginator;
    
    public function createOrder(array $data): Order;
    
    public function updateOrderStatus(int $id, string $status): ?Order;
    
    public function cancelOrder(int $id): ?Order;
    
    public function getRecentOrders(int $limit = 10): Collection;
    
    public function getPendingOrders(): Collection;
    
    public function getOrdersWithItems(int $perPage): LengthAwarePaginator;
    
    public function getTotalRevenue(): float;
    
    public function getRevenueByDateRange(string $startDate, string $endDate): float;
    
    public function count(): int;
    
    public function countByStatus(string $status): int;
    
    public function getFilteredOrders(array $filters, int $perPage): LengthAwarePaginator;
    
    public function countByDate(string $date): int;
    
    public function getRevenueByDate(string $date): float;
    
    public function countDeliveredBetween($startDate, $endDate): int;
    
    public function countByDateRange($startDate, $endDate): int;
}
