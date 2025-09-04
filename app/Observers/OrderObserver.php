<?php

namespace App\Observers;

use App\Models\Order;
use App\Mail\OrderApproved;
use App\Mail\OrderRejected;
use App\Mail\OrderDelivering;
use Illuminate\Support\Facades\Mail;

class OrderObserver
{
    /**
     * Handle the Order "created" event.
     */
    public function created(Order $order): void
    {
        //
    }

    /**
     * Handle the Order "updated" event.
     */
    public function updated(Order $order): void
    {
        // Check if status was changed
        if ($order->wasChanged('status')) {
            $this->handleStatusChange($order);
        }
    }

    /**
     * Handle order status change and send appropriate email
     */
    private function handleStatusChange(Order $order): void
    {
        // Load the customer relationship
        $order->load('user');
        
        switch ($order->status) {
            case 'approved':
                Mail::to($order->user->email)->send(new OrderApproved($order));
                break;
                
            case 'rejected':
                Mail::to($order->user->email)->send(new OrderRejected($order));
                break;
                
            case 'delivering':
                Mail::to($order->user->email)->send(new OrderDelivering($order));
                break;
        }
    }

    /**
     * Handle the Order "deleted" event.
     */
    public function deleted(Order $order): void
    {
        //
    }

    /**
     * Handle the Order "restored" event.
     */
    public function restored(Order $order): void
    {
        //
    }

    /**
     * Handle the Order "force deleted" event.
     */
    public function forceDeleted(Order $order): void
    {
        //
    }
}
