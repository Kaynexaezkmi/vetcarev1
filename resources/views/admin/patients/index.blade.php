@extends('layouts.dashboard')
@section('title', 'Patients - VetCare Admin')
@section('header-title', 'Patient Records')

@section('content')
<div class="bg-white rounded-xl shadow-sm">
    <div class="p-4 md:p-6 border-b border-gray-200">
        <div class="flex flex-col md:flex-row md:items-center gap-3 md:gap-4">
            <h3 class="text-base md:text-lg font-semibold text-gray-900">All Owners</h3>
        </div>
        <div class="mt-3">
            <div class="relative">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search owner, email, phone, or pet..." class="w-full pl-10 pr-4 py-2 rounded-lg border border-gray-300 text-sm focus:ring-2 focus:ring-orange-500 focus:border-orange-500" id="searchInput" onchange="document.getElementById('searchForm').submit()">
                <svg class="w-5 h-5 text-gray-400 absolute left-3 top-1/2 transform -translate-y-1/2 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
            </div>
        </div>
    </div>
    
    <div class="md:hidden p-4 space-y-3">
        @forelse($owners as $owner)
        <div class="border border-gray-200 rounded-xl p-4">
            <div class="flex items-center mb-3">
                <div class="w-10 h-10 bg-orange-100 rounded-full flex items-center justify-center mr-3">
                    <span class="text-orange-500 font-semibold">{{ substr($owner->name, 0, 1) }}</span>
                </div>
                <div class="min-w-0 flex-1">
                    <p class="font-medium text-gray-900 truncate">{{ $owner->name }}</p>
                    <p class="text-xs text-gray-500 truncate">{{ $owner->email }}</p>
                </div>
            </div>
            <p class="text-xs text-gray-500 mb-2">Phone: {{ isset($owner->phone) ? (str_starts_with($owner->phone, '+63') ? substr($owner->phone, 3) : $owner->phone) : '-' }}</p>
            <div class="flex flex-wrap gap-1 mb-3">
                @foreach($owner->pets as $pet)
                    <span class="inline-flex items-center px-2 py-1 text-xs font-medium bg-orange-50 text-orange-700 rounded-full">
                        {{ $pet->name }} ({{ $pet->type }})
                    </span>
                @endforeach
            </div>
            <a href="{{ route('admin.patients.records', $owner->pets->first()) }}" class="block w-full text-center px-3 py-2 text-xs font-medium text-white bg-orange-500 rounded-lg hover:bg-orange-600 transition">View Records</a>
        </div>
        @empty
        <div class="text-center py-8">
            <svg class="w-12 h-12 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <p class="text-gray-500">No owners found</p>
        </div>
        @endforelse
    </div>
    
    <div class="hidden md:block overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Owner</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Phone</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Pets</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($owners as $owner)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-orange-100 rounded-full flex items-center justify-center mr-3">
                                <span class="text-orange-500 font-semibold">{{ substr($owner->name, 0, 1) }}</span>
                            </div>
                            <span class="font-medium text-gray-900">{{ $owner->name }}</span>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-600">{{ $owner->email }}</td>
                    <td class="px-6 py-4 text-sm text-gray-600">{{ isset($owner->phone) ? (str_starts_with($owner->phone, '+63') ? substr($owner->phone, 3) : $owner->phone) : '-' }}</td>
                    <td class="px-6 py-4">
                        @foreach($owner->pets as $pet)
                            <span class="inline-flex items-center px-2.5 py-1 text-xs font-medium bg-orange-50 text-orange-700 rounded-full mr-1 mb-1">
                                {{ $pet->name }} ({{ $pet->type }})
                            </span>
                        @endforeach
                    </td>
                    <td class="px-6 py-4">
                        <a href="{{ route('admin.patients.records', $owner->pets->first()) }}" class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-white bg-orange-500 rounded-lg hover:bg-orange-600 transition">View</a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-12 text-center">
                        <svg class="w-12 h-12 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <p class="text-gray-500">No owners found</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    @if($owners->hasPages())
    <div class="px-4 md:px-6 py-4 border-t border-gray-200">
        {{ $owners->links() }}
    </div>
    @endif
</div>
@endsection

<form action="{{ route('admin.patients.index') }}" method="GET" id="searchForm"></form>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchInput');
    let timeout = null;
    searchInput.addEventListener('input', function() {
        clearTimeout(timeout);
        timeout = setTimeout(function() {
            document.getElementById('searchForm').submit();
        }, 500);
    });
});
</script>
@endpush