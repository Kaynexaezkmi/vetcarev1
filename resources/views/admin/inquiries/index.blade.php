@extends('layouts.dashboard')
@section('title', 'Inquiries - VetCare Admin')
@section('header-title', 'Inquiries')

@section('content')
@if(session('success'))
<div id="successMessage" class="mb-4 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl">
    {{ session('success') }}
</div>
@endif

<div class="bg-white rounded-xl shadow-sm">
    <div class="p-4 md:p-6 border-b border-gray-200">
        <div class="flex flex-col md:flex-row md:items-center gap-3 md:gap-4">
            <h3 class="text-base md:text-lg font-semibold text-gray-900">Customer Inquiries</h3>
        </div>
        <div class="mt-3">
            <form method="GET" action="{{ route('admin.inquiries.index') }}" class="flex items-center gap-2">
                <select name="status" onchange="this.form.submit()" class="w-30 md:w-40 px-2 py-1.5 rounded-lg border border-gray-200 text-sm bg-gray-50 focus:ring-2 focus:ring-orange-500 focus:border-orange-500">
                    <option value="" {{ request('status', '') == '' ? 'selected' : '' }}>All</option>
                    <option value="new" {{ request('status') == 'new' ? 'selected' : '' }}>New</option>
                    <option value="replied" {{ request('status') == 'replied' ? 'selected' : '' }}>Replied</option>
                    <option value="closed" {{ request('status') == 'closed' ? 'selected' : '' }}>Closed</option>
                </select>
            </form>
        </div>
    </div>
    
    <div class="divide-y divide-gray-200">
        @forelse($inquiries as $inquiry)
        <div class="flex items-start justify-between p-4 md:p-6 hover:bg-gray-50">
            <div class="flex items-start space-x-3 md:space-x-4 min-w-0">
                <a href="{{ route('admin.inquiries.show', $inquiry) }}" class="flex items-start space-x-3 md:space-x-4 min-w-0">
                    <div class="w-10 h-10 bg-orange-100 rounded-full flex items-center justify-center flex-shrink-0">
                        <span class="text-orange-500 font-semibold text-sm">{{ substr($inquiry->name, 0, 1) }}</span>
                    </div>
                    <div class="min-w-0">
                        <h4 class="font-semibold text-gray-900 text-sm md:text-base">{{ $inquiry->name }}</h4>
                        <p class="text-xs md:text-sm text-gray-500">{{ $inquiry->email }}</p>
                        <p class="text-xs md:text-sm text-gray-600 mt-1 line-clamp-1">{{ Str::limit($inquiry->message, 60) }}</p>
                    </div>
                </a>
            </div>
            <div class="text-right ml-2 flex-shrink-0 flex items-center gap-2">
                <div>
                    <p class="text-xs text-gray-500">{{ $inquiry->created_at->format('M d, Y') }}</p>
                    <span class="inline-flex px-2 py-1 mt-1 text-xs font-medium rounded-full
                        @if($inquiry->status === 'new') bg-yellow-100 text-yellow-700
                        @elseif($inquiry->status === 'replied') bg-green-100 text-green-700
                        @else bg-gray-100 text-gray-700 @endif">
                        {{ ucfirst($inquiry->status) }}
                    </span>
                </div>
                <button type="button" onclick="openDeleteInquiryModal({{ $inquiry->id }})" class="text-gray-400 hover:text-red-500 p-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                        </svg>
                    </button>
            </div>
        </div>
        @empty
        <div class="p-8 md:p-12 text-center">
            <svg class="w-12 h-12 md:w-16 md:h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path>
            </svg>
            <p class="text-gray-500">No inquiries found</p>
        </div>
        @endforelse
    </div>
    
    @if($inquiries->hasPages())
    <div class="px-4 md:px-6 py-4 border-t border-gray-200">
        {{ $inquiries->links() }}
    </div>
    @endif
</div>

<div id="deleteInquiryModal" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50 p-4">
    <div class="bg-white rounded-2xl p-4 md:p-6 w-full max-w-md">
        <div class="text-center">
            <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-6 h-6 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                </svg>
            </div>
            <h3 class="text-lg font-semibold text-gray-900 mb-2">Delete Inquiry</h3>
            <p class="text-gray-500 mb-6 text-sm">Are you sure you want to delete this inquiry?</p>
            <div class="flex justify-center gap-3">
                <button type="button" onclick="closeDeleteInquiryModal()" class="px-5 py-2.5 bg-gray-100 text-gray-700 font-medium rounded-lg hover:bg-gray-200 transition">Cancel</button>
                <form id="deleteInquiryForm" method="POST">
                    @csrf @method('delete')
                    <button type="submit" class="px-5 py-2.5 bg-red-500 text-white font-medium rounded-lg hover:bg-red-600 transition">Delete</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function openDeleteInquiryModal(id) {
    document.getElementById('deleteInquiryForm').action = '/admin/inquiries/' + id;
    document.getElementById('deleteInquiryModal').classList.remove('hidden');
    document.getElementById('deleteInquiryModal').classList.add('flex');
}

function closeDeleteInquiryModal() {
    document.getElementById('deleteInquiryModal').classList.add('hidden');
    document.getElementById('deleteInquiryModal').classList.remove('flex');
}

document.addEventListener('DOMContentLoaded', function() {
    const successMessage = document.getElementById('successMessage');
    if (successMessage) {
        setTimeout(function() {
            successMessage.style.transition = 'opacity 0.5s';
            successMessage.style.opacity = '0';
            setTimeout(function() {
                successMessage.remove();
            }, 500);
        }, 3000);
    }
});
</script>
@endpush