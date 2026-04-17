@extends('layouts.dashboard')
@section('title', 'Inquiry - VetCare Admin')
@section('header-title', 'Inquiry Details')

@section('content')
@if(session('success'))
<div id="successMessage" class="mb-6 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl">
    {{ session('success') }}
</div>
@endif

<div class="mb-6 flex items-center justify-between">
    <a href="{{ route('admin.inquiries.index') }}" class="text-sm text-gray-500 hover:text-gray-700">← Back to Inquiries</a>
    @if($inquiry->status === 'closed')
    <button type="button" onclick="openDeleteModal()" class="px-4 py-2 bg-red-500 text-white text-sm rounded-lg hover:bg-red-600">Delete Inquiry</button>
    @endif
</div>

<div id="deleteModal" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-2xl p-6 w-full max-w-md mx-4">
        <h3 class="text-lg font-semibold text-gray-900 mb-2">Delete Inquiry</h3>
        <p class="text-gray-600 mb-4">Are you sure you want to delete this inquiry? This action cannot be undone.</p>
        <div class="bg-gray-50 rounded-lg p-3 mb-4">
            <p class="text-sm text-gray-700"><strong>From:</strong> {{ $inquiry->name }}</p>
            <p class="text-sm text-gray-700"><strong>Email:</strong> {{ $inquiry->email }}</p>
        </div>
        <form action="{{ route('admin.inquiries.delete', $inquiry) }}" method="POST">
            @csrf
            @method('DELETE')
            <div class="flex justify-end space-x-3">
                <button type="button" onclick="closeDeleteModal()" class="px-4 py-2 text-gray-600 hover:text-gray-800">Cancel</button>
                <button type="submit" class="px-6 py-2 bg-red-500 text-white font-medium rounded-lg hover:bg-red-600">Delete</button>
            </div>
        </form>
    </div>
</div>

<div class="grid lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2">
        <div class="bg-white rounded-2xl shadow-sm p-6">
            <div class="mb-6 pb-6 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Message</h3>
                <p class="text-gray-700 whitespace-pre-wrap">{{ $inquiry->message }}</p>
            </div>
            
            <div>
                <h4 class="text-sm font-medium text-gray-500 mb-4">Reply / Notes</h4>
                <form action="{{ route('admin.inquiries.status', $inquiry) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="mb-4">
                        <select name="status" class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:ring-2 focus:ring-orange-500">
                            <option value="new" {{ $inquiry->status === 'new' ? 'selected' : '' }}>New</option>
                            <option value="replied" {{ $inquiry->status === 'replied' ? 'selected' : '' }}>Replied</option>
                            <option value="closed" {{ $inquiry->status === 'closed' ? 'selected' : '' }}>Closed</option>
                        </select>
                    </div>
                    <button type="submit" class="px-6 py-2 bg-orange-500 text-white font-medium rounded-lg hover:bg-orange-600">
                        Update Status
                    </button>
                </form>
            </div>
        </div>
    </div>
    
    <div class="lg:col-span-1">
        <div class="bg-white rounded-2xl shadow-sm p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Contact Information</h3>
            <div class="space-y-4">
                <div>
                    <p class="text-sm text-gray-500">Name</p>
                    <p class="font-medium text-gray-900">{{ $inquiry->name }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Email</p>
                    <a href="mailto:{{ $inquiry->email }}" class="text-orange-500 hover:text-orange-600">{{ $inquiry->email }}</a>
                </div>
                @if($inquiry->phone)
                <div>
                    <p class="text-sm text-gray-500">Phone</p>
                    @php
                        $displayPhone = $inquiry->phone;
                        if(str_starts_with($inquiry->phone, '+63')) {
                            $displayPhone = '+63 ' . substr($inquiry->phone, 3, 4) . ' ' . substr($inquiry->phone, 7);
                        }
                    @endphp
                    <a href="tel:{{ $inquiry->phone }}" class="text-orange-500 hover:text-orange-600">{{ $displayPhone }}</a>
                </div>
                @endif
                <div>
                    <p class="text-sm text-gray-500">Received</p>
                    <p class="text-gray-700">{{ $inquiry->created_at->format('M d, Y h:i A') }}</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
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

function openDeleteModal() {
    document.getElementById('deleteModal').classList.remove('hidden');
    document.getElementById('deleteModal').classList.add('flex');
}

function closeDeleteModal() {
    document.getElementById('deleteModal').classList.add('hidden');
    document.getElementById('deleteModal').classList.remove('flex');
}
</script>
@endpush
