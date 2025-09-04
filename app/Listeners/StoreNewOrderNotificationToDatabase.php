<?php

namespace App\Listeners;

use App\Events\NewOrderPlaced;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class StoreNewOrderNotificationToDatabase implements ShouldQueue // implements ShouldQueue là quan trọng để nó được đẩy vào queue
{
    use InteractsWithQueue;

    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(NewOrderPlaced $event): void
    {
        $order = $event->order;

        // Find an admin user to assign the notification to
        // You might have a specific role_id for admin or a specific admin user ID
        $adminUser = User::where('role_id', 1)->first(); // Assuming role_id 1 is for admin

        if ($adminUser) {
            Notification::create([
                'type' => 'new_order',
                'title' => 'New Order Placed',
                'message' => 'A new order #' . $order->order_id . ' has been placed by ' . ($order->user ? $order->user->name : 'Guest') . '. Total: ' . number_format($order->total_cost) . ' VND.',
                'data' => [
                    'order_id' => $order->order_id,
                    'customer_id' => $order->customer_id,
                    'total_cost' => $order->total_cost,
                    'order_date' => $order->order_date->toDateTimeString(),
                    'status' => $order->status,
                ],
                'user_id' => $adminUser->id,
                'related_id' => $order->order_id,
                'related_type' => 'order',
            ]);
        }
    }
} 