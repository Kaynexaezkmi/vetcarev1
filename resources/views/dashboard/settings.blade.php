@extends('layouts.dashboard')
@section('title', 'Settings - VetCare')
@section('header-title', 'Settings')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-2xl shadow-sm p-8">
        @if(session('success'))
        <div class="mb-6 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl">
            {{ session('success') }}
        </div>
        @endif
        
        <h3 class="text-lg font-semibold text-gray-900 mb-6">Profile Settings</h3>
        
        <form action="{{ route('settings.update') }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="space-y-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Full Name</label>
                    <input type="text" name="name" value="{{ Auth::user()->name }}" required class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:ring-2 focus:ring-orange-500 focus:border-orange-500">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Email Address</label>
                    <input type="email" value="{{ Auth::user()->email }}" disabled class="w-full px-4 py-3 rounded-xl border border-gray-200 bg-gray-50 text-gray-500">
                    <p class="text-xs text-gray-500 mt-1">Email cannot be changed</p>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Phone Number</label>
                    <div class="flex items-center">
                        <div class="flex-shrink-0 px-3 py-3 bg-gray-100 border border-r-0 border-gray-300 rounded-l-xl">
                            <span class="text-gray-500 font-medium">+63</span>
                        </div>
                        <input type="tel" name="phone" value="{{ isset(Auth::user()->phone) ? (str_starts_with(Auth::user()->phone, '+63') ? substr(Auth::user()->phone, 3) : Auth::user()->phone) : '' }}" placeholder=" " pattern="[0-9]{11}" maxlength="11" class="flex-1 px-4 py-3 rounded-r-xl border border-gray-300 focus:ring-2 focus:ring-orange-500 focus:border-orange-500">
                    </div>
                    <p class="text-xs text-gray-500 mt-1">Enter 11-digit mobile number</p>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Address</label>
                    <textarea name="address" rows="2" placeholder="Enter your address" class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:ring-2 focus:ring-orange-500 focus:border-orange-500">{{ Auth::user()->address }}</textarea>
                </div>
            </div>
            
            <div class="mt-8 pt-6 border-t">
                <button type="submit" class="w-full px-6 py-3 bg-orange-500 text-white font-semibold rounded-xl hover:bg-orange-600 transition">
                    Save Changes
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
