<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('Daily Revenue Report') }}</title>
    <style>
        {!! file_get_contents(resource_path('views/emails/revenue-report.css')) !!}
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>{{ __('Daily Revenue Report') }}</h1>
            <div class="date">{{ $reportDate->format('d/m/Y') }}</div>
        </div>

        <div class="greeting">
            <h3>{{ __('Hello') }} {{ $adminName ?? 'Admin' }}!</h3>
            <p>{{ __('Here is your daily revenue report for') }} {{ $reportDate->format('l, d/m/Y') }}.</p>
        </div>

        <div class="summary-cards">
            <div class="summary-card revenue">
                <h3>{{ __('Total Revenue') }}</h3>
                <p class="value">{{ number_format($totalRevenue) }}</p>
                <p class="subtitle">VNĐ</p>
            </div>
            <div class="summary-card">
                <h3>{{ __('Delivered Orders') }}</h3>
                <p class="value">{{ $totalOrders }}</p>
                <p class="subtitle">{{ __('orders completed') }}</p>
            </div>
        </div>

        @if($totalOrders > 0)
            <div class="summary-table">
                <table>
                    <tr>
                        <td>{{ __('Average Order Value') }}</td>
                        <td>{{ number_format($totalRevenue / $totalOrders) }} VNĐ</td>
                    </tr>
                    <tr>
                        <td>{{ __('Total Items Sold') }}</td>
                        <td>{{ $orders->sum(function($order) { return $order->orderItems->sum('quantity'); }) }}</td>
                    </tr>
                    <tr>
                        <td>{{ __('Revenue Growth') }}</td>
                        <td>{{ __('vs. yesterday') }}</td>
                    </tr>
                </table>
            </div>
        @endif

        <div class="details">
            <h2>{{ __('Order Details') }}</h2>
            
            @if($orders->count() > 0)
                <table class="order-table">
                    <thead>
                        <tr>
                            <th>{{ __('Order ID') }}</th>
                            <th>{{ __('Customer') }}</th>
                            <th>{{ __('Order Date') }}</th>
                            <th>{{ __('Items') }}</th>
                            <th>{{ __('Amount') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($orders as $order)
                        <tr>
                            <td class="order-id">#{{ $order->order_id }}</td>
                            <td>{{ $order->user->name ?? $order->user->user_name ?? __('N/A') }}</td>
                            <td>{{ $order->order_date ? \Carbon\Carbon::parse($order->order_date)->format('d/m/Y H:i') : __('N/A') }}</td>
                            <td>{{ $order->orderItems->sum('quantity') }} {{ __('items') }}</td>
                            <td class="amount">{{ number_format($order->total_cost) }} VNĐ</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <div class="no-orders">
                    <div class="icon">📦</div>
                    <p><strong>{{ __('No delivered orders found for this date.') }}</strong></p>
                    <p>{{ __('No revenue was generated from completed orders on') }} {{ $reportDate->format('d/m/Y') }}.</p>
                </div>
            @endif
        </div>

        <div class="footer">
            <p>{{ __('This report was automatically generated on') }} {{ now()->format('d/m/Y H:i:s') }}</p>
            <p class="company">{{ config('app.name', 'E-Commerce Admin') }}</p>
            <p>{{ __('For any questions, please contact the system administrator.') }}</p>
        </div>
    </div>
</body>
</html>
