@extends('emails.layout')

@section('title', 'Order Approved')

@section('content')
<div class="email-content">
    <h2 style="color: #28a745; margin-bottom: 20px;">Great News! Your Order Has Been Approved</h2>
    
    <p>Dear {{ $customer->name ?? $customer->user_name ?? 'Valued Customer' }},</p>
    
    <p>We're excited to let you know that your order has been <strong>approved</strong> and is now being prepared for delivery!</p>
    
    <div class="order-info">
        <h3>Order Details</h3>
        <p><strong>Order ID:</strong> #{{ $order->order_id }}</p>
        <p><strong>Order Date:</strong> {{ $order->created_at->format('F j, Y \a\t g:i A') }}</p>
        <p><strong>Total Amount:</strong> {{ number_format($order->total_cost, 0, ',', '.') }} VND</p>
        <p><strong>Status:</strong> <span class="status-badge status-approved">Approved</span></p>
        <p><strong>Delivery Address:</strong> {{ $order->address }}</p>
        <p><strong>Phone Number:</strong> {{ $order->phone_number }}</p>
    </div>
    
    <h3 style="color: #B88E2F; margin-top: 30px;">What's Next?</h3>
    <ul style="line-height: 1.8;">
        <li>Your order is being carefully prepared by our team</li>
        <li>You'll receive another email when it's out for delivery</li>
        <li>Track your order status anytime in your account</li>
        <li>Contact us if you have any questions</li>
    </ul>
    
    <div style="text-align: center; margin: 30px 0;">
        <a href="{{ route('customer.orders') }}" class="btn">View Your Orders</a>
    </div>
    
    <p style="margin-top: 30px;">Thank you for choosing Furniro Store. We appreciate your business and look forward to serving you again!</p>
    
    <p style="margin-top: 20px;">
        Best regards,<br>
        <strong>The Furniro Store Team</strong>
    </p>
</div>
@endsection
