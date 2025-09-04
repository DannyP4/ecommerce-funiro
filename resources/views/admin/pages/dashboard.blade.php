@extends('admin.layouts.app')

@section('content')
<div class="content-section active">
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-header">
                <div>
                    <div class="stat-number">{{ number_format($stats['total_users']) }}</div>
                    <div class="stat-label">{{ __('Total Users') }}</div>
                </div>
                <div class="stat-icon">
                    <i class="fas fa-users"></i>
                </div>
            </div>
            <div class="stat-change positive">
                <i class="fas fa-arrow-up"></i> {{ $stats['total_users'] }} {{ __('registered users') }}
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-header">
                <div>
                    <div class="stat-number">{{ number_format($stats['total_categories']) }}</div>
                    <div class="stat-label">{{ __('Categories') }}</div>
                </div>
                <div class="stat-icon">
                    <i class="fas fa-tags"></i>
                </div>
            </div>
            <div class="stat-change positive">
                <i class="fas fa-arrow-up"></i> {{ $stats['total_categories'] }} {{ __('active categories') }}
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-header">
                <div>
                    <div class="stat-number">{{ number_format($stats['total_products']) }}</div>
                    <div class="stat-label">{{ __('Products') }}</div>
                </div>
                <div class="stat-icon">
                    <i class="fas fa-box"></i>
                </div>
            </div>
            <div class="stat-change positive">
                <i class="fas fa-arrow-up"></i> {{ $stats['total_products'] }} {{ __('products available') }}
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-header">
                <div>
                    <div class="stat-number">{{ number_format($stats['total_orders']) }}</div>
                    <div class="stat-label">{{ __('Total Orders') }}</div>
                </div>
                <div class="stat-icon">
                    <i class="fas fa-shopping-cart"></i>
                </div>
            </div>
            <div class="stat-change positive">
                <i class="fas fa-arrow-up"></i> {{ $stats['total_orders'] }} {{ __('total orders (all statuses)') }}
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-header">
                <div>
                    <div class="stat-number">{{ number_format($stats['total_feedbacks']) }}</div>
                    <div class="stat-label">{{ __('Customer Reviews') }}</div>
                </div>
                <div class="stat-icon">
                    <i class="fas fa-star"></i>
                </div>
            </div>
            <div class="stat-change positive">
                <i class="fas fa-arrow-up"></i> {{ $stats['total_feedbacks'] }} {{ __('customer feedbacks') }}
            </div>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="charts-section">
        <div class="charts-grid">
            <div class="chart-container">
                <div class="chart-header">
                    <h3>{{ __('Daily Revenue') }}</h3>
                    <p class="chart-subtitle">{{ __('Revenue from delivered orders in the last 7 days') }}</p>
                </div>
                <canvas id="dailyRevenueChart"></canvas>
            </div>

            <div class="chart-container">
                <div class="chart-header">
                    <h3>{{ __('Active Users') }}</h3>
                    <p class="chart-subtitle">{{ __('Users who placed orders or gave feedback') }}</p>
                </div>
                <canvas id="activeUsersChart"></canvas>
            </div>
            
            <div class="chart-container">
                <div class="chart-header">
                    <h3>{{ __('Products Ordered') }}</h3>
                    <p class="chart-subtitle">{{ __('Total quantity of products ordered') }}</p>
                </div>
                <canvas id="orderedProductsChart"></canvas>
            </div>
            
            <div class="chart-container orders-container">
                <div class="chart-header">
                    <h3>{{ __('Orders') }}</h3>
                    <p class="chart-subtitle">{{ __('Order management and completion tracking') }}</p>
                </div>
                <div class="dual-chart-layout">
                    <div class="chart-section">
                        <h4>{{ __('New Orders') }}</h4>
                        <canvas id="newOrdersChart"></canvas>
                    </div>
                    <div class="chart-section">
                        <h4>{{ __('Completed Orders') }}</h4>
                        <canvas id="completedOrdersChart"></canvas>
                    </div>
                </div>
            </div>
            
            <div class="chart-container feedback-container">
                <div class="chart-header">
                    <h3>{{ __('Customer Feedback') }}</h3>
                    <p class="chart-subtitle">{{ __('Feedback trends and rating analysis') }}</p>
                </div>
                <div class="dual-chart-layout">
                    <div class="chart-section">
                        <h4>{{ __('New Feedback') }}</h4>
                        <canvas id="newFeedbacksChart"></canvas>
                    </div>
                    <div class="chart-section">
                        <h4>{{ __('Rating Distribution') }}</h4>
                        <canvas id="ratingDistributionChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const TIMEOUT_MS = 200;

window.dashboardTranslations = {
    completed: "{{ __('Completed') }}",
    pending: "{{ __('Pending') }}",
    oneStar: "{{ __('1 star') }}",
    twoStars: "{{ __('2 stars') }}",
    threeStars: "{{ __('3 stars') }}",
    fourStars: "{{ __('4 stars') }}",
    fiveStars: "{{ __('5 stars') }}",
    orders: "{{ __('orders') }}",
    reviews: "{{ __('Reviews') }}"
};

document.addEventListener('DOMContentLoaded', function() {
    function initCharts() {
        if (typeof Chart !== 'undefined' && typeof window.loadWeeklyCharts === 'function') {
            window.loadWeeklyCharts();
        } else {
            setTimeout(initCharts, TIMEOUT_MS);
        }
    }
    
    initCharts();
});
</script>
@endpush
@endsection
