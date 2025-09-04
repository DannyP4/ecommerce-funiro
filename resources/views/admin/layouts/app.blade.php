<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Furniro Admin Panel</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script>
        window.Laravel = {
            adminId: "{{ Auth::check() ? Auth::user()->id : 'null' }}",
            routes: {
                notificationsIndex: "{{ route('admin.notifications.index') }}",
                notificationsMarkAsRead: "{{ url('/admin/notifications') }}", // Base URL for markAsRead, JavaScript will append /<id>/read
                notificationsRecent: "{{ route('admin.notifications.recent') }}",
                notificationsCount: "{{ route('admin.notifications.count') }}",
                ordersDetails: "{{ url('/admin/orders') }}" // Base URL for order details, JS will append /<id>/details
            },
            translations: {
                approved: "{{ __('Approved') }}",
                pending: "{{ __('Pending') }}",
                rejected: "{{ __('Rejected') }}",
                delivered: "{{ __('Delivered') }}",
                cancelled: "{{ __('Cancelled') }}",
                noItemsFound: "{{ __('No items found in this order.') }}",
                loadingOrderDetails: "{{ __('Loading order details...') }}",
                failedToLoadOrderDetails: "{{ __('Failed to load order details.') }}",
                retry: "{{ __('Retry') }}"
            }
        };
    </script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    @vite(['resources/css/admin.css', 'resources/js/admin.js', 'resources/css/pagination.css'])
</head>
<body>
    @include('admin.components.sidebar')

    <!-- Main Content -->
    <div class="main-content">
        @include('admin.components.header')

        <!-- Content Sections -->
        <div id="content-area">
            @yield('content')
        </div>
    </div>

    <!-- Modals -->
    <div id="modal-area">
        @yield('modals')
        @include('admin.components.modals')
    </div>

    @yield('scripts')
    @stack('scripts')
</body>
</html>
