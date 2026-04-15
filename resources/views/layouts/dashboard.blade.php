<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'VetCare Dashboard' }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/@fullcalendar/core@6.1.10/index.global.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@fullcalendar/daygrid@6.1.10/index.global.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@fullcalendar/timegrid@6.1.10/index.global.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@fullcalendar/interaction@6.1.10/index.global.min.js"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        .fc-button-primary { background-color: #fb923c !important; border-color: #fb923c !important; }
        .fc-button-primary:hover { background-color: #f97316 !important; border-color: #f97316 !important; }
        .fc-button-primary:not(:disabled).fc-button-active, .fc-button-primary:not(:disabled):active { background-color: #ea580c !important; border-color: #ea580c !important; }
        .fc-button-primary:disabled { background-color: #fed7aa !important; border-color: #fed7aa !important; color: #9a3412 !important; }
        a.no-underline { text-decoration: none !important; }
        a.no-underline:hover { text-decoration: none !important; }
        .line-clamp-1 { display: -webkit-box; -webkit-line-clamp: 1; -webkit-box-orient: vertical; overflow: hidden; }
        .fc-event { display: none !important; }
        
        @media (max-width: 768px) {
            .sidebar-desktop { display: none !important; }
            .mobile-header { display: flex !important; }
            .main-content { padding-bottom: 20px !important; }
            .hide-mobile { display: none !important; }
            .mobile-full-width { width: 100% !important; }
        }
        
        @media (min-width: 769px) {
            .sidebar-desktop { display: flex !important; }
            .mobile-header { display: none !important; }
        }
    </style>
</head>
<body class="font-sans antialiased bg-gray-50">
    <div class="flex h-screen overflow-hidden">
        @include('components.dashboard.sidebar')
        
        <div class="flex flex-col flex-1 overflow-hidden">
            @include('components.dashboard.header')
            
            <main class="flex-1 overflow-y-auto p-4 md:p-6 bg-gray-50 main-content">
                <div id="dashboard-content-wrapper">
                    @yield('content')
                </div>
            </main>
        </div>
    </div>
    
    <div id="mobileSidebar" class="fixed inset-0 z-50 hidden">
        <div class="absolute inset-0 bg-black/50" onclick="toggleMobileSidebar()"></div>
        <div class="absolute left-0 top-0 bottom-0 w-72 bg-white shadow-xl transform -translate-x-full transition-transform duration-300" id="sidebarPanel">
            <div class="p-4 border-b border-gray-200 flex justify-between items-center bg-orange-50">
                <a href="/" class="flex items-center space-x-2">
                    <div class="w-28 h-28 rounded-xl overflow-hidden">
                        <img src="{{ asset('images/logovet.jpg') }}" alt="Logo" class="w-full h-full object-contain">
                    </div>
                    <span class="font-bold text-orange-600 text-lg">VetCare</span>
                </a>
                <button onclick="toggleMobileSidebar()" class="text-gray-500 hover:text-gray-700 p-1">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            @include('components.dashboard.sidebar-menu')
        </div>
    </div>

    <script>
    function toggleMobileSidebar() {
        const sidebar = document.getElementById('sidebarPanel');
        const overlay = document.getElementById('mobileSidebar');
        if (sidebar.classList.contains('-translate-x-full')) {
            sidebar.classList.remove('-translate-x-full');
            overlay.classList.remove('hidden');
        } else {
            sidebar.classList.add('-translate-x-full');
            setTimeout(() => overlay.classList.add('hidden'), 300);
        }
    }
    document.addEventListener('DOMContentLoaded', function() {
        const main = document.querySelector('main.main-content');
        const wrapper = document.getElementById('dashboard-content-wrapper');
        if (main && wrapper) {
            const content = wrapper.innerHTML.trim();
            if (content) {
                main.innerHTML = content;
            }
        }
    });
    </script>
    
    @stack('scripts')
</body>
</html>