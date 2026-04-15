@extends('layouts.website')
@section('title', 'Our Services - VetCare')

@section('content')
<section class="bg-gradient-to-br from-orange-50 to-orange-100 py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center">
            <h1 class="text-4xl lg:text-5xl font-bold text-gray-900 mb-4">Our Services</h1>
            <p class="text-lg text-gray-600 max-w-2xl mx-auto">Comprehensive veterinary care tailored to your pet's needs.</p>
        </div>
    </div>
</section>

<section class="py-20 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
            @forelse($services as $service)
            <div class="bg-white border border-gray-200 rounded-2xl overflow-hidden hover:shadow-xl transition">
                @if($service->image && file_exists(public_path($service->image)))
                    <img src="{{ asset($service->image) }}" alt="{{ $service->name }}" class="w-full h-48 object-cover">
                @else
                    <div class="h-48 bg-gradient-to-br from-orange-100 to-orange-200 flex items-center justify-center">
                        <svg class="w-20 h-20 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                        </svg>
                    </div>
                @endif
                <div class="p-6">
                    <h3 class="text-xl font-bold text-gray-900 mb-2">{{ $service->name }}</h3>
                    <p class="text-gray-600 mb-4">{{ $service->description ?? 'Professional veterinary service for your beloved pets.' }}</p>
                    <div class="flex items-center justify-between border-t pt-4">
                        <div>
                            <span class="text-sm text-gray-500">{{ $service->duration }}</span>
                        </div>
                        <a href="{{ auth()->check() ? route('appointments.create') : route('register') }}" class="px-4 py-2 bg-orange-500 text-white text-sm font-medium rounded-lg hover:bg-orange-600 transition">
                            Book Now
                        </a>
                    </div>
                </div>
            </div>
            @empty
            <div class="col-span-3 text-center py-16">
                <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                </svg>
                <p class="text-gray-500 text-lg">Services coming soon. Please check back later!</p>
            </div>
            @endforelse
        </div>
    </div>
</section>

<section class="py-20 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h2 class="text-3xl font-bold text-gray-900 mb-4">How It Works</h2>
            <p class="text-gray-600 max-w-2xl mx-auto">Getting your pet the care they need is easy with our simple booking process.</p>
        </div>
        <div class="grid md:grid-cols-4 gap-8">
            <div class="text-center">
                <div class="w-16 h-16 bg-orange-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <span class="text-2xl font-bold text-orange-500">1</span>
                </div>
                <h3 class="font-semibold text-gray-900 mb-2">Create Account</h3>
                <p class="text-sm text-gray-600">Sign up for free and add your pet's information.</p>
            </div>
            <div class="text-center">
                <div class="w-16 h-16 bg-orange-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <span class="text-2xl font-bold text-orange-500">2</span>
                </div>
                <h3 class="font-semibold text-gray-900 mb-2">Choose Service</h3>
                <p class="text-sm text-gray-600">Select the service that fits your pet's needs.</p>
            </div>
            <div class="text-center">
                <div class="w-16 h-16 bg-orange-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <span class="text-2xl font-bold text-orange-500">3</span>
                </div>
                <h3 class="font-semibold text-gray-900 mb-2">Book Appointment</h3>
                <p class="text-sm text-gray-600">Pick a date and time that works for you.</p>
            </div>
            <div class="text-center">
                <div class="w-16 h-16 bg-orange-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <span class="text-2xl font-bold text-orange-500">4</span>
                </div>
                <h3 class="font-semibold text-gray-900 mb-2">Visit & Care</h3>
                <p class="text-sm text-gray-600">Bring your pet in for top-quality veterinary care.</p>
            </div>
        </div>
    </div>
</section>

<section id="inquiry" class="py-20 bg-gradient-to-br from-orange-500 to-orange-600">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h2 class="text-3xl lg:text-4xl font-bold text-white mb-4">Have Questions?</h2>
            <p class="text-lg text-orange-100">Send us a message and we'll get back to you as soon as possible.</p>
        </div>
        @if(session('success'))
        <div class="bg-white rounded-2xl p-6 mb-8 text-center">
            <svg class="w-12 h-12 text-green-500 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <p class="text-gray-700 font-medium">{{ session('success') }}</p>
        </div>
        @endif
        <form action="{{ route('inquiry.store') }}" method="POST" class="bg-white rounded-2xl p-8 shadow-2xl">
            @csrf
            <div class="grid md:grid-cols-2 gap-6 mb-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Your Name</label>
                    <input type="text" name="name" required class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:ring-2 focus:ring-orange-500 focus:border-orange-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Email Address</label>
                    <input type="email" name="email" required class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:ring-2 focus:ring-orange-500 focus:border-orange-500">
                </div>
            </div>
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">Phone Number</label>
                <div class="flex items-center">
                    <div class="flex-shrink-0 px-3 py-3 bg-gray-100 border border-r-0 border-gray-300 rounded-l-xl">
                        <span class="text-gray-500 font-medium">+63</span>
                    </div>
                    <input type="tel" name="phone" placeholder=" " pattern="[0-9]{11}" maxlength="11" class="flex-1 px-4 py-3 rounded-r-xl border border-gray-300 focus:ring-2 focus:ring-orange-500 focus:border-orange-500">
                </div>
                <p class="text-xs text-gray-500 mt-1">Enter 11-digit mobile number</p>
            </div>
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">Your Message</label>
                <textarea name="message" rows="4" required class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:ring-2 focus:ring-orange-500 focus:border-orange-500"></textarea>
            </div>
            <button type="submit" class="w-full bg-orange-500 text-white font-semibold py-4 rounded-xl hover:bg-orange-600 transition">
                Send Message
            </button>
        </form>
    </div>
</section>
@endsection
