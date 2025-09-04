@extends('emails.layout')

@section('title', 'Order On The Way')

@section('content')
<div class="email-content">
    <h2 style="color: #17a2b8; margin-bottom: 20px;">Your Order Is On The Way!</h2>
    
    <p>Dear {{ $customer->name ?? $customer->user_name ?? 'Valued Customer' }},</p>
    
    <p>Exciting news! Your order has been dispatched and is now <strong>on its way</strong> to your delivery address.</p>
    
    <div class="order-info">
        <h3>Delivery Information</h3>
        <p><strong>Order ID:</strong> #{{ $order->order_id }}</p>
        <p><strong>Order Date:</strong> {{ $order->created_at->format('F j, Y \a\t g:i A') }}</p>
        <p><strong>Total Amount:</strong> {{ number_format($order->total_cost, 0, ',', '.') }} VND</p>
        <p><strong>Status:</strong> <span class="status-badge status-delivering">Out For Delivery</span></p>
        <p><strong>Delivery Address:</strong> {{ $order->address }}</p>
        <p><strong>Phone Number:</strong> {{ $order->phone_number }}</p>
        <p><strong>Estimated Delivery:</strong> 3-5 business days</p>
    </div>
    
    <h3 style="color: #B88E2F; margin-top: 30px;">Delivery Timeline:</h3>
    <div style="background-color: #f8f9fa; padding: 20px; border-radius: 6px; margin: 20px 0;">
        <div style="margin-bottom: 10px;">
            <span style="color: #28a745; margin-right: 10px;">COMPLETED:</span>
            <span>Order Confirmed & Approved</span>
        </div>
        <div style="margin-bottom: 10px;">
            <span style="color: #28a745; margin-right: 10px;">COMPLETED:</span>
            <span>Order Prepared & Packaged</span>
        </div>
        <div style="margin-bottom: 10px;">
            <span style="color: #17a2b8; margin-right: 10px;">CURRENT:</span>
            <strong>Out for Delivery</strong>
        </div>
        <div>
            <span style="color: #6c757d; margin-right: 10px;">NEXT:</span>
            <span style="color: #6c757d;">Delivered (Coming Soon)</span>
        </div>
    </div>
    
    <h3 style="color: #B88E2F; margin-top: 30px;">Important Notes:</h3>
    <ul style="line-height: 1.8;">
        <li>Please keep your phone available for delivery coordination</li>
        <li>Ensure someone is available at the delivery address</li>
        <li>Check your items upon delivery for any damage</li>
        <li>Keep your order ID handy for reference</li>
    </ul>
    
    <div style="text-align: center; margin: 30px 0;">
        <a href="{{ route('customer.orders') }}" class="btn">Track Your Order</a>
    </div>
    
    <p style="margin-top: 30px;">We're excited for you to receive your new furniture! If you have any questions about your delivery, please don't hesitate to contact us.</p>
    
    <p style="margin-top: 20px;">
        Best regards,<br>
        <strong>The Furniro Store Team</strong>
    </p>
</div>
@endsection
