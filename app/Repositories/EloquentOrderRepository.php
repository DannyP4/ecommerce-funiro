<?php

namespace App\Repositories;

use App\Models\Order;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class EloquentOrderRepository implements OrderRepository
{
    public function getAllOrders(): Collection
    {
        return Order::with(['user', 'orderItems.product'])->get();
    }

    public function getOrderById(int $id): ?Order
    {
        return Order::with(['user', 'orderItems.product', 'deliveryInfo'])->find($id);
    }

    public function getOrdersByUserId(int $userId, int $perPage): LengthAwarePaginator
    {
        return Order::where('customer_id', $userId)
            ->with(['orderItems.product'])
            ->orderBy('order_date', 'desc')
            ->paginate($perPage);
    }

    public function getOrdersByStatus(string $status, int $perPage): LengthAwarePaginator
    {
        return Order::where('status', $status)
            ->with(['user', 'orderItems.product'])
            ->orderBy('order_date', 'desc')
            ->paginate($perPage);
    }

    public function createOrder(array $data): Order
    {
        return Order::create($data);
    }

    public function updateOrderStatus(int $id, string $status): ?Order
    {
        $order = $this->getOrderById($id);

        if (!$order) {
            return null;
        }

        $order->update(['status' => $status]);

        return $order->fresh();
    }

    public function cancelOrder(int $id): ?Order
    {
        return $this->updateOrderStatus($id, 'cancelled');
    }

    public function getRecentOrders(int $limit = 10): Collection
    {
        return Order::with(['user', 'orderItems.product'])
            ->orderBy('order_date', 'desc')
            ->limit($limit)
            ->get();
    }

    public function getPendingOrders(): Collection
    {
        return Order::where('status', 'pending')
            ->with(['user', 'orderItems.product'])
            ->orderBy('order_date', 'desc')
            ->get();
    }

    public function getOrdersWithItems(int $perPage): LengthAwarePaginator
    {
        return Order::with(['user', 'orderItems.product', 'deliveryInfo'])
            ->orderBy('order_date', 'desc')
            ->paginate($perPage);
    }

    public function getTotalRevenue(): float
    {
        return Order::whereIn('status', ['delivering', 'delivered'])
            ->sum('total_cost');
    }

    public function getRevenueByDateRange(string $startDate, string $endDate): float
    {
        return Order::whereIn('status', ['delivering', 'delivered'])
            ->whereBetween('order_date', [$startDate, $endDate])
            ->sum('total_cost');
    }

    public function count(): int
    {
        return Order::count();
    }

    public function countByStatus(string $status): int
    {
        return Order::where('status', $status)->count();
    }
    
    public function getFilteredOrders(array $filters, int $perPage): LengthAwarePaginator
    {
        $query = Order::with('user')->orderBy('updated_at', 'desc');

        // Filter by status
        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        // Filter by date range
        if (!empty($filters['from_date'])) {
            $query->whereDate('order_date', '>=', $filters['from_date']);
        }

        if (!empty($filters['to_date'])) {
            $query->whereDate('order_date', '<=', $filters['to_date']);
        }

        // Order by most recent first
        return $query->orderBy('order_date', 'desc')->paginate($perPage);
    }
    
    public function countByDate(string $date): int
    {
        return Order::whereDate('order_date', $date)->count();
    }
    
    public function getRevenueByDate(string $date): float
    {
        return Order::where('status', 'delivered')
            ->where(function($query) use ($date) {
                $query->whereDate('updated_at', $date)
                      ->orWhere(function($subQuery) use ($date) {
                          $subQuery->whereDate('created_at', $date)
                                   ->where('status', 'delivered');
                      });
            })
            ->sum('total_cost');
    }
    
    public function countDeliveredBetween($startDate, $endDate): int
    {
        return Order::where('status', 'delivered')
            ->whereBetween('updated_at', [$startDate, $endDate])
            ->count();
    }
    
    public function countByDateRange($startDate, $endDate): int
    {
        return Order::whereBetween('order_date', [$startDate, $endDate])->count();
    }
}
