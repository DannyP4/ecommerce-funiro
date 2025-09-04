<!-- Sidebar -->
<div class="sidebar">
    <div class="sidebar-header">
        <a href="{{ route('admin.dashboard') }}" class="logo">
            <i class="fas fa-couch"></i>
            <span>Furniro</span>
        </a>
        <div class="admin-label">{{ __('Admin Panel') }}</div>
    </div>

    <nav class="sidebar-menu">
        <a href="{{ route('admin.dashboard') }}" class="menu-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
            <i class="fas fa-chart-pie"></i>
            <span>{{ __('Dashboard') }}</span>
        </a>
        <a href="{{ route('admin.users') }}" class="menu-item {{ request()->routeIs('admin.users') ? 'active' : '' }}">
            <i class="fas fa-users"></i>
            <span>{{ __('User Management') }}</span>
        </a>
        <a href="{{ route('admin.categories') }}" class="menu-item {{ request()->routeIs('admin.categories') ? 'active' : '' }}">
            <i class="fas fa-tags"></i>
            <span>{{ __('Category Management') }}</span>
        </a>
        <a href="{{ route('admin.products') }}" class="menu-item {{ request()->routeIs('admin.products') ? 'active' : '' }}">
            <i class="fas fa-box"></i>
            <span>{{ __('Product Management') }}</span>
        </a>
        <a href="{{ route('admin.orders') }}" class="menu-item {{ request()->routeIs('admin.orders') ? 'active' : '' }}">
            <i class="fas fa-shopping-cart"></i>
            <span>{{ __('Order Management') }}</span>
        </a>
        <a href="{{ route('admin.feedbacks') }}" class="menu-item {{ request()->routeIs('admin.feedbacks') ? 'active' : '' }}">
            <i class="fas fa-star"></i>
            <span>{{ __('Feedbacks') }}</span>
        </a>
    </nav>
</div>
