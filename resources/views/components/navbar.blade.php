<nav x-data="{ open: false }" class="bg-white sticky top-0 z-50 shadow-sm">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex items-center">
                <a href="/" class="flex items-center space-x-2">
                    <img src="/images/logovet.jpg" alt="VetCare Logo" class="h-24 w-24 object-contain rounded">

                </a>
                <div class="hidden md:flex ml-10 space-x-8">
                    <a href="/" class="text-gray-600 hover:text-orange-500 px-3 py-2 text-sm font-medium transition">Home</a>
                    <a href="{{ route('services') }}" class="text-gray-600 hover:text-orange-500 px-3 py-2 text-sm font-medium transition">Services</a>
                    <a href="{{ route('about') }}" class="text-gray-600 hover:text-orange-500 px-3 py-2 text-sm font-medium transition">About Us</a>
                    <a href="#inquiry" class="text-gray-600 hover:text-orange-500 px-3 py-2 text-sm font-medium transition">Inquiry</a>
                </div>
            </div>
            <div class="hidden md:flex items-center space-x-4">
                @auth
                    <a href="{{ route('dashboard') }}" class="bg-orange-500 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-orange-600 transition">Dashboard</a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="text-gray-600 hover:text-orange-500 px-3 py-2 text-sm font-medium">Logout</button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="text-gray-600 hover:text-orange-500 px-3 py-2 text-sm font-medium">Login</a>
                    <a href="{{ route('register') }}" class="bg-orange-500 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-orange-600 transition">Register</a>
                @endauth
            </div>
            <div class="md:hidden flex items-center">
                <button @click="open = !open" class="text-gray-600 hover:text-orange-500 p-2">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path x-show="!open" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                        <path x-show="open" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
        </div>
    </div>
    <div x-show="open" class="md:hidden bg-white border-t">
        <div class="px-4 py-3 space-y-2">
            <a href="/" class="block text-gray-600 hover:text-orange-500 px-3 py-2">Home</a>
            <a href="{{ route('services') }}" class="block text-gray-600 hover:text-orange-500 px-3 py-2">Services</a>
            <a href="{{ route('about') }}" class="block text-gray-600 hover:text-orange-500 px-3 py-2">About Us</a>
            <a href="#inquiry" class="block text-gray-600 hover:text-orange-500 px-3 py-2">Inquiry</a>
            @auth
                <a href="{{ route('dashboard') }}" class="block bg-orange-500 text-white px-4 py-2 rounded-lg text-center">Dashboard</a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="block w-full text-left text-gray-600 hover:text-orange-500 px-3 py-2">Logout</button>
                </form>
            @else
                <a href="{{ route('login') }}" class="block text-gray-600 hover:text-orange-500 px-3 py-2">Login</a>
                <a href="{{ route('register') }}" class="block bg-orange-500 text-white px-4 py-2 rounded-lg text-center">Register</a>
            @endauth
        </div>
    </div>
</nav>
