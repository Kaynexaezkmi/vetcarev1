<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'VetCare - Login' }}</title>
    <link rel="icon" type="image/jpeg" href="{{ asset('images/logovet.jpg') }}">
    <link rel="shortcut icon" type="image/jpeg" href="{{ asset('images/logovet.jpg') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased min-h-screen bg-gradient-to-br from-orange-50 to-orange-100 flex items-center justify-center p-4">
    <div class="w-full max-w-md">
        <div class="text-center mb-8">
            <a href="/" class="inline-flex items-center space-x-2 mb-6">
                <img src="/images/logovet.jpg" alt="VetCare Logo" class="h-32 w-32 object-contain rounded-full">
        
            </a>
        </div>
        
        <div class="bg-white rounded-2xl shadow-xl p-8">
            {{ $slot }}
        </div>
        
        <p class="text-center text-gray-500 text-sm mt-6">
            &copy; {{ date('Y') }} VetCare. All rights reserved.
        </p>
    </div>
</body>
</html>
