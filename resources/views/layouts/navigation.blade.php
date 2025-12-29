<!-- Customer Header Navigation -->
<header class="header">
    <div class="header-container">
        <a href="{{ route('home') }}" class="logo">
            <i class="fas fa-couch"></i>
            <span>Furniro</span>
        </a>
        
        <ul class="nav-menu">
            <li><a href="{{ route('home') }}" class="{{ request()->routeIs('home') ? 'active' : '' }}">{{ __('Home') }}</a></li>
            <li><a href="{{ route('customer.categories') }}" class="{{ request()->routeIs('customer.categories') || request()->routeIs('customer.products') ? 'active' : '' }}">{{ __('Categories') }}</a></li>
            <li><a href="{{ route('customer.orders') }}" class="{{ request()->routeIs('customer.orders*') ? 'active' : '' }}">{{ __('Orders') }}</a></li>
            <li><a href="{{ route('customer.about') }}" class="{{ request()->routeIs('customer.about') ? 'active' : '' }}">{{ __('About') }}</a></li>
            <li><a href="{{ route('customer.contact') }}" class="{{ request()->routeIs('customer.contact') ? 'active' : '' }}">{{ __('Contact') }}</a></li>
        </ul>
        
        <div class="header-icons">
            <a href="{{ route('profile.edit') }}" title="{{ __('Profile') }}">
                <i class="fas fa-user"></i>
            </a>
            <a href="{{ route('customer.cart.index') }}" title="{{ __('Cart') }}" style="position: relative;">
                <i class="fas fa-shopping-cart"></i>
                @if(session('cart') && count(session('cart')) > 0)
                    <span class="cart-count">{{ count(session('cart')) }}</span>
                @endif
            </a>
            <form method="POST" action="{{ route('logout') }}" id="logout-form" style="display: inline;">
                @csrf
                <button type="button" onclick="confirmLogout()" style="background: none; border: none; color: #333; font-size: 18px; cursor: pointer; transition: color 0.3s;" title="{{ __('Log Out') }}" onmouseover="this.style.color='#B88E2F'" onmouseout="this.style.color='#333'">
                    <i class="fas fa-sign-out-alt"></i>
                </button>
            </form>
        </div>
    </div>
</header>

<script>
function confirmLogout() {
    if (confirm('{{ __("Are you sure you want to logout?") }}')) {
        document.getElementById('logout-form').submit();
    }
}
</script>

<style>
    :root {
        --header-height: 80px;
    }

    .header {
        background: #fff;
        padding: 20px 0;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        z-index: 1000;
        transition: all 0.3s ease;
    }

    body {
        padding-top: var(--header-height);
    }

    .header-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 20px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .logo {
        display: flex;
        align-items: center;
        gap: 10px;
        font-size: 24px;
        font-weight: 700;
        color: #B88E2F;
        text-decoration: none;
    }

    .logo i {
        font-size: 32px;
    }

    .nav-menu {
        display: flex;
        gap: 40px;
        list-style: none;
        margin: 0;
        padding: 0;
    }

    .nav-menu a {
        text-decoration: none;
        color: #333;
        font-weight: 500;
        font-size: 16px;
        transition: color 0.3s;
    }

    .nav-menu a:hover,
    .nav-menu a.active {
        color: #B88E2F;
    }

    .header-icons {
        display: flex;
        gap: 15px;
        align-items: center;
    }

    .header-icons a {
        color: #333;
        font-size: 18px;
        text-decoration: none;
        transition: color 0.3s;
    }

    .header-icons a:hover {
        color: #B88E2F;
    }

    .cart-count {
        position: absolute;
        top: -8px;
        right: -8px;
        background: #B88E2F;
        color: white;
        border-radius: 50%;
        width: 20px;
        height: 20px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 12px;
        font-weight: bold;
    }
</style>
