@extends('layouts.dashboard')
@section('title', 'Reminders - VetCare')
@section('header-title', 'Reminders')

@section('content')
<div class="bg-white rounded-xl shadow-sm">
    <div class="p-4 md:p-6 border-b border-gray-200">
        <h3 class="text-base md:text-lg font-semibold text-gray-900">Upcoming Reminders</h3>
    </div>
    
    <div class="p-4 md:p-6">
        @if($reminders->count() > 0)
        <div class="space-y-3 md:space-y-4">
            @foreach($reminders as $reminder)
            <div class="flex items-center justify-between p-3 md:p-4 {{ $reminder->isUnread() ? 'bg-orange-50 border-l-4 border-orange-500' : 'bg-gray-50' }} rounded-xl">
                <div class="flex items-center min-w-0">
                    <div class="w-10 md:w-12 h-10 md:h-12 bg-orange-100 rounded-xl flex items-center justify-center mr-3 md:mr-4 flex-shrink-0">
                        <svg class="w-5 md:w-6 h-5 md:h-6 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                        </svg>
                    </div>
                    <div class="min-w-0">
                        <p class="font-medium text-gray-900 flex items-center text-sm md:text-base">
                            {{ $reminder->appointment->pet->name ?? 'Unknown Pet' }}
                            @if($reminder->isUnread())
                                <span class="ml-2 w-2 h-2 bg-orange-500 rounded-full"></span>
                            @endif
                        </p>
                        <p class="text-xs md:text-sm text-gray-500">
                            {{ $reminder->appointment->service ? $reminder->appointment->service->name : 'Checkup' }}
                        </p>
                        <p class="text-xs text-gray-400 mt-1">
                            {{ Carbon\Carbon::parse($reminder->send_at)->format('M d, Y h:i A') }}
                        </p>
                    </div>
                </div>
                <div class="flex items-center gap-2 md:gap-3 flex-shrink-0">
                    <button type="button" onclick="openDeleteReminderModal({{ $reminder->id }})" class="text-gray-400 hover:text-red-500 p-1">
                        <svg class="w-4 md:w-5 h-4 md:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                        </svg>
                    </button>
                    @if($reminder->is_sent)
                    <span class="inline-flex px-2 md:px-3 py-1 text-xs font-medium rounded-full bg-green-100 text-green-700">Sent</span>
                    @else
                    <span class="inline-flex px-2 md:px-3 py-1 text-xs font-medium rounded-full bg-yellow-100 text-yellow-700">Pending</span>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
        @if($reminders->hasPages())
        <div class="mt-4 md:mt-6">
            {{ $reminders->links() }}
        </div>
        @endif
        @else
        <div class="text-center py-8 md:py-12">
            <svg class="w-12 h-12 md:w-16 md:h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
            </svg>
            <p class="text-gray-500">No reminders scheduled</p>
        </div>
        @endif
    </div>
</div>

<div id="deleteReminderModal" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50 p-4">
    <div class="bg-white rounded-2xl p-4 md:p-6 w-full max-w-md">
        <div class="text-center">
            <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-6 h-6 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                </svg>
            </div>
            <h3 class="text-lg font-semibold text-gray-900 mb-2">Delete Reminder</h3>
            <p class="text-gray-500 mb-6 text-sm">Are you sure you want to delete this reminder?</p>
            <div class="flex justify-center gap-3">
                <button type="button" onclick="closeDeleteReminderModal()" class="px-5 py-2.5 bg-gray-100 text-gray-700 font-medium rounded-lg hover:bg-gray-200 transition w-full md:w-auto">Cancel</button>
                <form id="deleteReminderForm" method="POST">
                    @csrf @method('delete')
                    <button type="submit" class="px-5 py-2.5 bg-red-500 text-white font-medium rounded-lg hover:bg-red-600 transition w-full md:w-auto">Delete</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function openDeleteReminderModal(id) {
    document.getElementById('deleteReminderForm').action = '/reminders/' + id;
    document.getElementById('deleteReminderModal').classList.remove('hidden');
    document.getElementById('deleteReminderModal').classList.add('flex');
}

function closeDeleteReminderModal() {
    document.getElementById('deleteReminderModal').classList.add('hidden');
    document.getElementById('deleteReminderModal').classList.remove('flex');
}

document.getElementById('deleteReminderModal').addEventListener('click', function(e) {
    if (e.target === this) closeDeleteReminderModal();
});

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') closeDeleteReminderModal();
});
</script>
@endpush