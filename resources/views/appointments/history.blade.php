@extends('layouts.dashboard')
@section('title', 'Appointment History - VetCare')
@section('header-title', 'Appointment History')

@section('content')
@if(session('success'))
<div class="mb-4 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl">
    {{ session('success') }}
</div>
@endif

@if(session('error'))
<div class="mb-4 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl">
    {{ session('error') }}
</div>
@endif

<div class="bg-white rounded-xl shadow-sm">
    <div class="p-4 md:p-6 border-b border-gray-200">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3 md:gap-4">
            <h3 class="text-base md:text-lg font-semibold text-gray-900">All Appointments</h3>
            <a href="{{ route('appointments.create') }}" class="inline-flex items-center justify-center px-4 py-2 bg-orange-500 text-white text-sm font-medium rounded-lg hover:bg-orange-600 transition w-full md:w-auto">
                <svg class="w-4 h-4 mr-1 md:mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                New
            </a>
        </div>
    </div>
    
    <div class="md:hidden p-4 space-y-3">
        @forelse($appointments as $appointment)
        <div class="border border-gray-200 rounded-xl p-4">
            <div class="flex items-start justify-between mb-3">
                <div class="flex items-center">
                    <div class="w-8 h-8 bg-orange-100 rounded-full flex items-center justify-center mr-3">
                        <svg class="w-4 h-4 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                        </svg>
                    </div>
                    <div>
                        <p class="font-medium text-gray-900">{{ $appointment->pet->name }}</p>
                        <p class="text-xs text-gray-500">{{ $appointment->pet->type }}</p>
                    </div>
                </div>
                <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full {{ $appointment->status_badge_class }}">
                    {{ $appointment->status_label }}
                </span>
            </div>
            <p class="text-sm text-gray-600 mb-1">{{ $appointment->service ? $appointment->service->name : 'General Checkup' }}</p>
            <p class="text-xs text-gray-500 mb-3">{{ Carbon\Carbon::parse($appointment->appointment_date)->format('M d, Y') }} at {{ Carbon\Carbon::parse($appointment->appointment_time)->format('h:i A') }}</p>
            @if($appointment->cancellation_reason)
            <p class="text-xs text-red-600 mb-3">Reason: {{ $appointment->cancellation_reason }}</p>
            @endif
            <div class="flex flex-wrap gap-2">
                @if($appointment->status === 'completed')
                <a href="{{ route('pets.records', $appointment->pet) }}" class="flex-1 text-center text-xs bg-orange-100 text-orange-700 px-3 py-2 rounded-lg hover:bg-orange-200">View Records</a>
                @elseif($appointment->canReschedule())
                <a href="{{ route('appointments.reschedule', $appointment) }}" class="flex-1 text-center text-xs bg-blue-100 text-blue-700 px-3 py-2 rounded-lg hover:bg-blue-200">Reschedule</a>
                <button type="button" onclick="openCancelModal({{ $appointment->id }}, '{{ $appointment->pet->name }}', '{{ Carbon\Carbon::parse($appointment->appointment_date)->format('M d, Y') }}', '{{ Carbon\Carbon::parse($appointment->appointment_time)->format('h:i A') }}')" class="flex-1 text-center text-xs bg-red-100 text-red-700 px-3 py-2 rounded-lg hover:bg-red-200">Cancel</button>
                @elseif($appointment->status === 'cancelled')
                <form action="{{ route('appointments.destroy', $appointment) }}" method="POST" class="inline-flex" id="deleteHistoryFormMobile{{ $appointment->id }}">
                    @csrf
                    @method('DELETE')
                    <button type="button" class="inline-flex items-center justify-center rounded-lg border border-red-200 bg-red-50 p-2 text-red-600 hover:bg-red-100" onclick="openDeleteHistoryModal('deleteHistoryFormMobile{{ $appointment->id }}', '{{ addslashes($appointment->pet->name) }}', '{{ Carbon\Carbon::parse($appointment->appointment_date)->format('M d, Y') }}', '{{ Carbon\Carbon::parse($appointment->appointment_time)->format('h:i A') }}')" title="Delete cancelled appointment" aria-label="Delete cancelled appointment">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                        </svg>
                    </button>
                </form>
                @endif
            </div>
        </div>
        @empty
        <div class="text-center py-8">
            <svg class="w-12 h-12 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
            </svg>
            <p class="text-gray-500">No appointments found</p>
            <a href="{{ route('appointments.create') }}" class="inline-block mt-3 text-orange-500 hover:text-orange-600 text-sm">Book your first appointment</a>
        </div>
        @endforelse
    </div>
    
    <div class="hidden md:block overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Pet</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Service</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date & Time</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($appointments as $appointment)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4">
                        <div class="flex items-center">
                            <div class="w-8 h-8 bg-orange-100 rounded-full flex items-center justify-center mr-3">
                                <svg class="w-4 h-4 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                                </svg>
                            </div>
                            <div>
                                <p class="font-medium text-gray-900">{{ $appointment->pet->name }}</p>
                                <p class="text-xs text-gray-500">{{ $appointment->pet->type }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-600">
                        {{ $appointment->service ? $appointment->service->name : 'General Checkup' }}
                    </td>
                    <td class="px-6 py-4">
                        <p class="text-sm font-medium text-gray-900">{{ Carbon\Carbon::parse($appointment->appointment_date)->format('M d, Y') }}</p>
                        <p class="text-xs text-gray-500">{{ Carbon\Carbon::parse($appointment->appointment_time)->format('h:i A') }}</p>
                    </td>
                    <td class="px-6 py-4">
                        <span class="inline-flex px-3 py-1 text-xs font-medium rounded-full {{ $appointment->status_badge_class }}">
                            {{ $appointment->status_label }}
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        @if($appointment->status === 'completed')
                        <a href="{{ route('pets.records', $appointment->pet) }}" class="text-xs bg-orange-100 text-orange-700 px-3 py-1 rounded-lg hover:bg-orange-200">View Records</a>
                        @elseif($appointment->canReschedule())
                        <div class="flex items-center space-x-2">
                            <a href="{{ route('appointments.reschedule', $appointment) }}" class="text-xs bg-blue-100 text-blue-700 px-3 py-1 rounded-lg hover:bg-blue-200">Reschedule</a>
                            <button type="button" onclick="openCancelModal({{ $appointment->id }}, '{{ $appointment->pet->name }}', '{{ Carbon\Carbon::parse($appointment->appointment_date)->format('M d, Y') }}', '{{ Carbon\Carbon::parse($appointment->appointment_time)->format('h:i A') }}')" class="text-xs bg-red-100 text-red-700 px-3 py-1 rounded-lg hover:bg-red-200">Cancel</button>
                        </div>
                        @elseif($appointment->status === 'cancelled')
                        <form action="{{ route('appointments.destroy', $appointment) }}" method="POST" class="inline" id="deleteHistoryFormDesktop{{ $appointment->id }}">
                            @csrf
                            @method('DELETE')
                            <button type="button" class="inline-flex items-center justify-center rounded-lg border border-red-200 bg-red-50 p-2 text-red-600 hover:bg-red-100" onclick="openDeleteHistoryModal('deleteHistoryFormDesktop{{ $appointment->id }}', '{{ addslashes($appointment->pet->name) }}', '{{ Carbon\Carbon::parse($appointment->appointment_date)->format('M d, Y') }}', '{{ Carbon\Carbon::parse($appointment->appointment_time)->format('h:i A') }}')" title="Delete cancelled appointment" aria-label="Delete cancelled appointment">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                </svg>
                            </button>
                        </form>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-12 text-center">
                        <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        <p class="text-gray-500">No appointments found</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    @if($appointments->hasPages())
    <div class="px-4 md:px-6 py-4 border-t border-gray-200">
        {{ $appointments->links() }}
    </div>
    @endif
</div>

<div id="cancelModal" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50 p-4">
    <div class="bg-white rounded-2xl p-4 md:p-6 w-full max-w-md">
        <h3 class="text-lg font-semibold text-gray-900 mb-2">Cancel Appointment</h3>
        <p class="text-gray-600 mb-4">Please provide a reason for cancellation.</p>
        <div class="bg-gray-50 rounded-lg p-3 mb-4">
            <p class="text-sm text-gray-700"><strong>Pet:</strong> <span id="cancelPetName"></span></p>
            <p class="text-sm text-gray-700"><strong>Date:</strong> <span id="cancelDate"></span></p>
            <p class="text-sm text-gray-700"><strong>Time:</strong> <span id="cancelTime"></span></p>
        </div>
        <form id="cancelForm" method="POST">
            @csrf @method('PUT')
            <textarea name="reason" rows="4" required class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm mb-4" placeholder="Enter the reason for cancellation"></textarea>
            <div class="flex flex-col md:flex-row justify-end gap-2 md:space-x-3">
                <button type="button" onclick="closeCancelModal()" class="px-4 py-2 text-gray-600 hover:text-gray-800 w-full md:w-auto">No, Keep</button>
                <button type="submit" class="px-6 py-2 bg-red-500 text-white font-medium rounded-lg hover:bg-red-600 w-full md:w-auto">Yes, Cancel</button>
            </div>
        </form>
    </div>
</div>

<div id="deleteHistoryModal" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50 p-4">
    <div class="bg-white rounded-2xl p-4 md:p-6 w-full max-w-md">
        <div class="flex items-center gap-3 mb-4">
            <div class="w-12 h-12 rounded-full bg-red-100 flex items-center justify-center">
                <svg class="w-6 h-6 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                </svg>
            </div>
            <div>
                <h3 class="text-lg font-semibold text-gray-900">Delete Cancelled Appointment</h3>
                <p class="text-sm text-gray-500">This will permanently remove it from your history.</p>
            </div>
        </div>
        <div class="bg-gray-50 rounded-lg p-3 mb-4 space-y-1">
            <p class="text-sm text-gray-700"><strong>Pet:</strong> <span id="deleteHistoryPet"></span></p>
            <p class="text-sm text-gray-700"><strong>Date:</strong> <span id="deleteHistoryDate"></span></p>
            <p class="text-sm text-gray-700"><strong>Time:</strong> <span id="deleteHistoryTime"></span></p>
        </div>
        <div class="flex flex-col md:flex-row justify-end gap-2 md:space-x-3">
            <button type="button" onclick="closeDeleteHistoryModal()" class="px-4 py-2 text-gray-600 hover:text-gray-800 w-full md:w-auto">Cancel</button>
            <button type="button" onclick="submitDeleteHistory()" class="px-6 py-2 bg-red-500 text-white font-medium rounded-lg hover:bg-red-600 w-full md:w-auto">Delete Appointment</button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
let deleteHistoryFormId = null;

function openCancelModal(id, petName, date, time) {
    document.getElementById('cancelForm').action = '/appointments/' + id + '/cancel';
    document.getElementById('cancelPetName').textContent = petName;
    document.getElementById('cancelDate').textContent = date;
    document.getElementById('cancelTime').textContent = time;
    document.getElementById('cancelModal').classList.remove('hidden');
    document.getElementById('cancelModal').classList.add('flex');
}
function closeCancelModal() {
    document.getElementById('cancelModal').classList.add('hidden');
    document.getElementById('cancelModal').classList.remove('flex');
}

function openDeleteHistoryModal(formId, petName, date, time) {
    deleteHistoryFormId = formId;
    document.getElementById('deleteHistoryPet').textContent = petName;
    document.getElementById('deleteHistoryDate').textContent = date;
    document.getElementById('deleteHistoryTime').textContent = time;
    document.getElementById('deleteHistoryModal').classList.remove('hidden');
    document.getElementById('deleteHistoryModal').classList.add('flex');
}

function closeDeleteHistoryModal() {
    deleteHistoryFormId = null;
    document.getElementById('deleteHistoryModal').classList.add('hidden');
    document.getElementById('deleteHistoryModal').classList.remove('flex');
}

function submitDeleteHistory() {
    if (deleteHistoryFormId) {
        document.getElementById(deleteHistoryFormId).submit();
    }
}
</script>
@endpush
