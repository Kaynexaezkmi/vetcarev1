<footer class="bg-gray-900 text-gray-300">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
            <div>
                <div class="flex items-center space-x-2 mb-4">
                    <div class="w-14 h-14 rounded-xl flex items-center justify-center overflow-hidden">
                        <img src="{{ asset('images/logovet.jpg') }}" alt="Logo" class="w-14 h-14 object-contain">
                    </div>
                    <span class="text-xl font-bold text-white">Vet<span class="text-orange-500">Care</span></span>
                </div>
                <p class="text-sm text-gray-400">Professional veterinary care for your beloved pets. Quality healthcare with compassion.</p>
            </div>
            <div>
                <h3 class="text-white font-semibold mb-4">Quick Links</h3>
                <ul class="space-y-2 text-sm">
                    <li><a href="/" class="hover:text-orange-500 transition">Home</a></li>
                    <li><a href="{{ route('services') }}" class="hover:text-orange-500 transition">Services</a></li>
                    <li><a href="{{ route('about') }}" class="hover:text-orange-500 transition">About Us</a></li>
                    <li><a href="#inquiry" class="hover:text-orange-500 transition">Contact</a></li>
                </ul>
            </div>
            <div>
                <h3 class="text-white font-semibold mb-4">Services</h3>
                <ul class="space-y-2 text-sm">
                    <li><a href="{{ route('services') }}" class="hover:text-orange-500 transition">Grooming</a></li>
                    <li><a href="{{ route('services') }}" class="hover:text-orange-500 transition">Vaccination</a></li>
                    <li><a href="{{ route('services') }}" class="hover:text-orange-500 transition">Surgery</a></li>
                    <li><a href="{{ route('services') }}" class="hover:text-orange-500 transition">Deworming</a></li>
                </ul>
            </div>
            <div>
                <h3 class="text-white font-semibold mb-4">Contact Info</h3>
                <ul class="space-y-2 text-sm">
                    <li class="flex items-start space-x-2">
                        <svg class="w-5 h-5 text-orange-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                        <span>TCDC Compound, Zone 5, Concepcion Grande, Naga City, Philippines 4400</span>
                    </li>
                    <li class="flex items-center space-x-2">
                        <svg class="w-5 h-5 text-orange-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                        </svg>
                        <span>0929 694 4414</span>
                    </li>
                    <li class="flex items-center space-x-2">
                        <svg class="w-5 h-5 text-orange-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                        </svg>
                        <span>williefredokho@yahoo.com</span>
                    </li>
                </ul>
            </div>
        </div>
        <div class="border-t border-gray-800 mt-8 pt-8 text-center text-sm text-gray-500">
            <p>&copy; {{ date('Y') }} VetCare. All rights reserved.</p>
        </div>
    </div>
</footer>
