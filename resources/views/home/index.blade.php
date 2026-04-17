@extends('layouts.website')
@section('title', 'VetCare - Professional Veterinary Care')

@section('content')
<section class="relative bg-gradient-to-br from-orange-50 to-orange-100 overflow-hidden">
    <div class="absolute inset-0 bg-[url('data:image/svg+xml,%3Csvg width=\"60\" height=\"60\" viewBox=\"0 0 60 60\" xmlns=\"http://www.w3.org/2000/svg\"%3E%3Cg fill=\"none\" fill-rule=\"evenodd\"%3E%3Cg fill=\"%23f97316\" fill-opacity=\"0.05\"%3E%3Cpath d=\"M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z\"/%3E%3C/g%3E%3C/g%3E%3C/svg%3E')] opacity-50"></div>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20 lg:py-32 relative">
        <div class="grid lg:grid-cols-2 gap-12 items-center">
            <div>
                <div class="inline-flex items-center px-4 py-2 bg-orange-100 rounded-full mb-6">
                    <svg class="w-4 h-4 text-orange-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                    </svg>
                    <span class="text-sm font-medium text-orange-700">Trusted by Pet Owners</span>
                </div>
                <h1 class="text-4xl lg:text-6xl font-bold text-gray-900 leading-tight mb-6">
                    Compassionate Care for Your <span class="text-orange-500">Beloved Pets</span>
                </h1>
                <p class="text-lg text-gray-600 mb-8">
                    Professional veterinary services with a personal touch. Book appointments online and give your pets the healthcare they deserve.
                </p>
                <div class="flex flex-col sm:flex-row gap-4">
                    @auth
                    <a href="{{ Auth::user()->isAdmin() ? route('admin.appointments.index') : route('appointments.create') }}" class="inline-flex items-center justify-center px-8 py-4 bg-orange-500 text-white font-semibold rounded-xl hover:bg-orange-600 transition shadow-lg shadow-orange-500/30">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        {{ Auth::user()->isAdmin() ? 'Manage Appointments' : 'Book Appointment' }}
                    </a>
                    @else
                    <button type="button" id="guest-book-appointment" class="inline-flex items-center justify-center px-8 py-4 bg-orange-500 text-white font-semibold rounded-xl hover:bg-orange-600 transition shadow-lg shadow-orange-500/30">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        Book Appointment
                    </button>
                    @endauth
                    <a href="{{ route('services') }}" class="inline-flex items-center justify-center px-8 py-4 bg-white text-gray-700 font-semibold rounded-xl hover:bg-gray-50 transition border border-gray-200">
                        Our Services
                    </a>
                </div>
            </div>
            <div class="relative hidden lg:block">
                <div class="absolute -top-10 -right-10 w-72 h-72 bg-orange-200 rounded-full opacity-50 blur-3xl"></div>
                <div class="absolute -bottom-10 -left-10 w-72 h-72 bg-orange-300 rounded-full opacity-50 blur-3xl"></div>
                <div class="relative bg-gradient-to-br from-orange-400 to-orange-500 rounded-3xl p-8 shadow-2xl">
                    <div class="bg-white rounded-2xl p-6 shadow-lg">
                        <div class="flex items-center space-x-4 mb-6">
                            <div class="w-16 h-16 rounded-full overflow-hidden">
                                <img src="{{ asset('images/dockho.jpg') }}" alt="Dr. Wilfredo Kho" class="w-full h-full object-cover">
                            </div>
                            <div>
                                <h3 class="font-semibold text-gray-800">Dr. Wilfredo Kho</h3>
                                <p class="text-sm text-gray-500">Veterinarian</p>
                            </div>
                        </div>
                        <div class="space-y-3">
                            <div class="flex items-center text-sm text-gray-600">
                                <svg class="w-4 h-4 mr-2 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                </svg>
                                Available 24/7
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="py-20 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h2 class="text-3xl lg:text-4xl font-bold text-gray-900 mb-4">Why Choose VetCare?</h2>
            <p class="text-lg text-gray-600 max-w-2xl mx-auto">We provide comprehensive veterinary care with a focus on compassion, expertise, and convenience.</p>
        </div>
        <div class="grid md:grid-cols-3 gap-8">
            <div class="text-center p-8 bg-gray-50 rounded-2xl hover:shadow-lg transition">
                <div class="w-16 h-16 bg-orange-100 rounded-2xl flex items-center justify-center mx-auto mb-6">
                    <svg class="w-8 h-8 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-3">Expert Veterinarians</h3>
                <p class="text-gray-600">Our team of certified professionals provides top-quality care for your pets.</p>
            </div>
            <div class="text-center p-8 bg-gray-50 rounded-2xl hover:shadow-lg transition">
                <div class="w-16 h-16 bg-orange-100 rounded-2xl flex items-center justify-center mx-auto mb-6">
                    <svg class="w-8 h-8 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-3">Easy Online Booking</h3>
                <p class="text-gray-600">Book appointments anytime, anywhere with our convenient online scheduling system.</p>
            </div>
            <div class="text-center p-8 bg-gray-50 rounded-2xl hover:shadow-lg transition">
                <div class="w-16 h-16 bg-orange-100 rounded-2xl flex items-center justify-center mx-auto mb-6">
                    <svg class="w-8 h-8 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-3">Compassionate Care</h3>
                <p class="text-gray-600">We treat every pet like our own, with love and the highest standard of care.</p>
            </div>
        </div>
    </div>
</section>

<section class="py-20 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h2 class="text-3xl lg:text-4xl font-bold text-gray-900 mb-4">Our Services</h2>
            <p class="text-lg text-gray-600 max-w-2xl mx-auto">Comprehensive veterinary services to keep your pets healthy and happy.</p>
        </div>
        <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-6">
            @forelse($services as $service)
            <div class="bg-white rounded-2xl p-6 shadow-sm hover:shadow-lg transition">
                @if($service->image && file_exists(public_path($service->image)))
                    <img src="{{ asset($service->image) }}" alt="{{ $service->name }}" class="w-full h-32 object-cover rounded-xl mb-4">
                @else
                    <div class="w-full h-32 bg-orange-100 rounded-xl flex items-center justify-center mb-4">
                        <svg class="w-12 h-12 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                        </svg>
                    </div>
                @endif
                <h3 class="text-lg font-semibold text-gray-900 mb-2">{{ $service->name }}</h3>
                <p class="text-sm text-gray-600 mb-4">{{ Str::limit($service->description, 80) }}</p>
                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-500">{{ $service->duration }}</span>
                </div>
            </div>
            @empty
            <div class="col-span-4 text-center py-12">
                <p class="text-gray-500">Services coming soon...</p>
            </div>
            @endforelse
        </div>
        <div class="text-center mt-12">
            <a href="{{ route('services') }}" class="inline-flex items-center text-orange-500 font-semibold hover:text-orange-600">
                View All Services
                <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
            </a>
        </div>
    </div>
</section>

<section class="py-20 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid lg:grid-cols-2 gap-16 items-center">
            <div>
                <h2 class="text-3xl lg:text-4xl font-bold text-gray-900 mb-6">What Pet Owners Say</h2>
                <p class="text-lg text-gray-600 mb-8">Don't just take our word for it. Here's what our happy customers have to say about their experience with VetCare.</p>
                <div class="space-y-6">
                    @foreach($feedback as $fb)
                    <div class="flex items-start space-x-4">
                        <div class="w-12 h-12 bg-orange-100 rounded-full flex items-center justify-center flex-shrink-0">
                            <span class="text-orange-500 font-semibold">{{ substr($fb->user->name, 0, 1) }}</span>
                        </div>
                        <div>
                            <div class="flex items-center mb-1">
                                @for($i = 0; $i < 5; $i++)
                                <svg class="w-4 h-4 {{ $i < $fb->rating ? 'text-orange-400' : 'text-gray-300' }}" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                </svg>
                                @endfor
                            </div>
                            <p class="text-gray-600 italic">"{{ Str::limit($fb->message, 100) }}"</p>
                            <p class="text-sm text-gray-500 mt-1">- {{ $fb->user->name }}</p>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            <div class="relative">
                <div class="bg-gradient-to-br from-orange-400 to-orange-500 rounded-3xl p-8 shadow-2xl">
                    <div class="bg-white rounded-2xl p-8">
                        <div class="text-center mb-6">
                            <div class="w-20 h-20 bg-orange-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                <svg class="w-10 h-10 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                                </svg>
                            </div>
                            <h3 class="text-2xl font-bold text-gray-900">10,000+</h3>
                            <p class="text-gray-600">Happy Pet Owners</p>
                        </div>
                        <div class="grid grid-cols-2 gap-4 text-center">
                            <div class="bg-gray-50 rounded-xl p-4">
                                <p class="text-2xl font-bold text-orange-500">15+</p>
                                <p class="text-sm text-gray-600">Years Experience</p>
                            </div>
                            <div class="bg-gray-50 rounded-xl p-4">
                                <p class="text-2xl font-bold text-orange-500">98%</p>
                                <p class="text-sm text-gray-600">Satisfaction Rate</p>
                            </div>
                        </div>
                    </div>
                </div>
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

@include('home.partials.inquiry-success-modal')

@guest
<div id="guest-booking-modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-gray-900/60 px-4">
    <div class="w-full max-w-md rounded-3xl bg-white p-8 shadow-2xl">
        <div class="flex items-start justify-between gap-4">
            <div>
                <h3 class="text-2xl font-bold text-gray-900">Book an Appointment</h3>
                <p class="mt-3 text-gray-600">
                    Already have an account?
                    <a href="{{ route('login') }}" class="font-medium text-orange-500 hover:text-orange-600">Sign in</a>.
                    If not,
                    <a href="{{ route('register') }}" class="font-medium text-orange-500 hover:text-orange-600">register</a>.
                </p>
            </div>
            <button type="button" id="close-guest-booking-modal" class="rounded-full p-2 text-gray-400 hover:bg-gray-100 hover:text-gray-600" aria-label="Close booking prompt">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
        <div class="mt-6 flex flex-col gap-3 sm:flex-row">
            <a href="{{ route('login') }}" class="inline-flex flex-1 items-center justify-center rounded-xl bg-orange-500 px-5 py-3 font-semibold text-white transition hover:bg-orange-600">
                Sign In
            </a>
            <a href="{{ route('register') }}" class="inline-flex flex-1 items-center justify-center rounded-xl border border-gray-200 px-5 py-3 font-semibold text-gray-700 transition hover:bg-gray-50">
                Register
            </a>
        </div>
    </div>
</div>
@endguest
@endsection

@guest
@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const trigger = document.getElementById('guest-book-appointment');
        const modal = document.getElementById('guest-booking-modal');
        const closeButton = document.getElementById('close-guest-booking-modal');

        if (!trigger || !modal || !closeButton) {
            return;
        }

        const openModal = function () {
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            document.body.classList.add('overflow-hidden');
        };

        const closeModal = function () {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
            document.body.classList.remove('overflow-hidden');
        };

        trigger.addEventListener('click', openModal);
        closeButton.addEventListener('click', closeModal);
        modal.addEventListener('click', function (event) {
            if (event.target === modal) {
                closeModal();
            }
        });
        document.addEventListener('keydown', function (event) {
            if (event.key === 'Escape' && !modal.classList.contains('hidden')) {
                closeModal();
            }
        });
    });
</script>
@endpush
@endguest
