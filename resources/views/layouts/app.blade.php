<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
        <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
        <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        
        <style>
            body {
                font-family: 'Poppins', sans-serif;
                background-color: #ffffff;
            }
        </style>
    </head>
    <body class="font-sans antialiased">
        <!-- Flash Messages for Toast Notifications -->
        @if(session('success'))
            <div data-flash-message="{{ session('success') }}" data-flash-type="success" style="display: none;"></div>
        @endif
        @if(session('error'))
            <div data-flash-message="{{ session('error') }}" data-flash-type="error" style="display: none;"></div>
        @endif
        @if(session('warning'))
            <div data-flash-message="{{ session('warning') }}" data-flash-type="warning" style="display: none;"></div>
        @endif
        @if(session('info'))
            <div data-flash-message="{{ session('info') }}" data-flash-type="info" style="display: none;"></div>
        @endif

        <div class="min-h-screen bg-white">
            @include('layouts.navigation')

            <!-- Page Heading -->
            @if (isset($header))
                <header class="bg-white shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endif

            <!-- Page Content -->
            <main>
                {{ $slot }}
            </main>
        </div>
    </body>
</html>
