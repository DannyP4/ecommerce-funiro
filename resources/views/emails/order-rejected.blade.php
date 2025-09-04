@extends('emails.layout')

@section('title', 'Order Update')

@section('content')
<div class="email-content">
    <h2 style="color: #dc3545; margin-bottom: 20px;">Order Update - #{{ $order->order_id }}</h2>
    
    <p>Dear {{ $customer->name ?? $customer->user_name ?? 'Valued Customer' }},</p>
    
    <p>We regret to inform you that we are unable to process your recent order at this time.</p>
    
    <div class="order-info">
        <h3>Order Details</h3>
        <p><strong>Order ID:</strong> #{{ $order->order_id }}</p>
        <p><strong>Order Date:</strong> {{ $order->created_at->format('F j, Y \a\t g:i A') }}</p>
        <p><strong>Total Amount:</strong> {{ number_format($order->total_cost, 0, ',', '.') }} VND</p>
        <p><strong>Status:</strong> <span class="status-badge status-rejected">Not Approved</span></p>
    </div>
    
    <h3 style="color: #B88E2F; margin-top: 30px;">Common Reasons:</h3>
    <ul style="line-height: 1.8;">
        <li>Item(s) currently out of stock</li>
        <li>Payment verification issues</li>
        <li>Delivery location not serviceable</li>
        <li>Incomplete order information</li>
    </ul>
    
    <h3 style="color: #B88E2F; margin-top: 30px;">What You Can Do:</h3>
    <ul style="line-height: 1.8;">
        <li>Browse our current available products</li>
        <li>Contact our customer service for assistance</li>
        <li>Place a new order with updated information</li>
        <li>Reach out to us for any clarifications</li>
    </ul>
    
    <div style="text-align: center; margin: 30px 0;">
        <a href="{{ route('customer.categories') }}" class="btn">Continue Shopping</a>
    </div>
    
    <p style="margin-top: 30px;">We apologize for any inconvenience this may cause. Our customer service team is ready to help you find the perfect furniture for your home.</p>
    
    <p style="margin-top: 20px;">
        Best regards,<br>
        <strong>The Furniro Store Team</strong>
    </p>
</div>
@endsection
