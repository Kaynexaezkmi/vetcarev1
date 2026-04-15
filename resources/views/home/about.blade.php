@extends('layouts.website')
@section('title', 'About Us - VetCare')

@section('content')
<section class="bg-gradient-to-br from-orange-50 to-orange-100 py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center">
            <h1 class="text-4xl lg:text-5xl font-bold text-gray-900 mb-4">About VetCare</h1>
            <p class="text-lg text-gray-600 max-w-2xl mx-auto">Dedicated to providing exceptional veterinary care</p>
        </div>
    </div>
</section>

<section class="py-20 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid lg:grid-cols-2 gap-16 items-center">
            <div>
                <h2 class="text-3xl font-bold text-gray-900 mb-6">Our Story</h2>
                <p class="text-gray-600 mb-4">
                    VetCare was founded by a graduating student as part of their capstone project, with Kho Veterinary Clinic as our valued beneficiary. What began as an academic endeavor has since grown into a meaningful mission: to provide compassionate, high-quality veterinary care for all pets.
                </p>
                <p class="text-gray-600 mb-4">
                    From its humble beginnings, VetCare has developed into a trusted name in pet healthcare, serving countless families in our community. Our partnership with Kho Veterinary Clinic continues to inspire us, reminding us that innovation and compassion can go hand in hand.
                </p>
                <p class="text-gray-600">
                    Our team of dedicated veterinarians and support staff treat every pet like family, guided by the belief that pets deserve the same level of care and attention as any loved one. With state-of-the-art facilities and a commitment to continuing education, we remain at the forefront of veterinary medicine—ensuring that your beloved companions receive the best possible care today and in the future.
                </p>
            </div>
            <div class="bg-gradient-to-br from-orange-400 to-orange-500 rounded-3xl p-8">
                <div class="bg-white rounded-2xl p-8 text-center">
                    <div class="grid grid-cols-2 gap-6">
                        <div>
                            <p class="text-4xl font-bold text-orange-500 mb-2">--</p>
                            <p class="text-gray-600 text-sm">Years of Experience</p>
                        </div>
                        <div>
                            <p class="text-4xl font-bold text-orange-500 mb-2"></p>
                            <p class="text-gray-600 text-sm">Happy Pets Served</p>
                        </div>
                        <div>
                            <p class="text-4xl font-bold text-orange-500 mb-2">2</p>
                            <p class="text-gray-600 text-sm">Veterinarians</p>
                        </div>
                        <div>
                            <p class="text-4xl font-bold text-orange-500 mb-2"></p>
                            <p class="text-gray-600 text-sm">Client Satisfaction</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="py-20 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h2 class="text-3xl font-bold text-gray-900 mb-4">Meet Our Team</h2>
            <p class="text-gray-600 max-w-2xl mx-auto">Our experienced team of veterinary professionals is here to help.</p>
        </div>
        <div class="grid md:grid-cols-3 gap-8">
            <div class="bg-white rounded-2xl p-6 text-center shadow-sm">
                <div class="w-24 h-24 rounded-full flex items-center justify-center mx-auto mb-4 overflow-hidden">
                    <img src="{{ asset('images/dockho.jpg') }}" alt="Dr. Wilfredo Kho" class="w-full h-full object-cover">
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-1">Dr. Wilfredo Kho</h3>
                <p class="text-orange-500 font-medium mb-3">Veterinarian</p>
                <p class="text-sm text-gray-600"></p>
            </div>
            <div class="bg-white rounded-2xl p-6 text-center shadow-sm">
                <div class="w-24 h-24 bg-orange-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <span class="text-3xl font-bold text-orange-500"></span>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-1">Dr. </h3>
                <p class="text-orange-500 font-medium mb-3"></p>
                <p class="text-sm text-gray-600"></p>
            </div>
            <div class="bg-white rounded-2xl p-6 text-center shadow-sm">
                <div class="w-24 h-24 bg-orange-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <span class="text-3xl font-bold text-orange-500"></span>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-1">Dr. </h3>
                <p class="text-orange-500 font-medium mb-3"></p>
                <p class="text-sm text-gray-600"></p>
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
