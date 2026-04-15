@extends('layouts.dashboard')
@section('title', 'Appointments - VetCare Admin')
@section('header-title', 'Appointment Management')

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
        <div class="flex flex-col md:flex-row md:items-center gap-3 md:gap-4">
            <h3 class="text-base md:text-lg font-semibold text-gray-900">All Appointments</h3>
            <button type="button" onclick="openHistoryModal()" class="md:hidden px-3 py-2 bg-gray-100 text-gray-700 rounded-lg text-sm w-full md:w-auto">History</button>
            <button type="button" onclick="openHistoryModal()" class="hidden md:block px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 text-sm">Appointment History</button>
        </div>
        
        <div class="mt-4 grid grid-cols-1 md:grid-cols-3 gap-3">
            <select name="status" class="px-3 py-2 rounded-lg border border-gray-300 text-sm" onchange="this.form.submit()">
                <option value="">All Status</option>
                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
            </select>
            <input type="date" name="date" value="{{ request('date') }}" class="px-3 py-2 rounded-lg border border-gray-300 text-sm" onchange="this.form.submit()">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search pet or owner..." class="px-3 py-2 rounded-lg border border-gray-300 text-sm" onchange="this.form.submit()">
        </div>
    </div>
    
    <div class="md:hidden p-4 space-y-3">
        @forelse($appointments as $appointment)
        <div class="border border-gray-200 rounded-xl p-4">
            <div class="flex items-start justify-between mb-3">
                <div>
                    <p class="font-medium text-gray-900">{{ $appointment->pet->name }}</p>
                    <p class="text-xs text-gray-500">{{ $appointment->user->name }}</p>
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
                @if($appointment->status === 'pending')
                <form action="{{ route('admin.appointments.approve', $appointment) }}" method="POST" class="flex-1">
                    @csrf @method('PUT')
                    <button type="submit" class="w-full text-xs bg-green-500 text-white px-3 py-2 rounded-lg hover:bg-green-600">Approve</button>
                </form>
                <button type="button" onclick="openRejectModal({{ $appointment->id }}, '{{ $appointment->pet->name }}', '{{ Carbon\Carbon::parse($appointment->appointment_date)->format('M d, Y') }}', '{{ Carbon\Carbon::parse($appointment->appointment_time)->format('h:i A') }}')" class="flex-1 text-xs bg-red-500 text-white px-3 py-2 rounded-lg hover:bg-red-600">Reject</button>
                @elseif($appointment->status === 'approved')
                <form action="{{ route('admin.appointments.complete', $appointment) }}" method="POST" class="flex-1">
                    @csrf @method('PUT')
                    <button type="submit" class="w-full text-xs bg-green-500 text-white px-3 py-2 rounded-lg hover:bg-green-600">Complete</button>
                </form>
                <a href="{{ route('appointments.reschedule', $appointment) }}" class="flex-1 text-center text-xs bg-blue-500 text-white px-3 py-2 rounded-lg hover:bg-blue-600">Reschedule</a>
                <form action="{{ route('admin.appointments.reminder', $appointment) }}" method="POST" class="flex-1">
                    @csrf
                    <button type="submit" class="w-full text-xs bg-purple-500 text-white px-3 py-2 rounded-lg hover:bg-purple-600">Remind</button>
                </form>
                @elseif($appointment->status === 'completed')
                <a href="{{ route('admin.patients.records', $appointment->pet) }}" class="flex-1 text-center text-xs bg-orange-100 text-orange-700 px-3 py-2 rounded-lg hover:bg-orange-200">View Records</a>
                @elseif(in_array($appointment->status, ['cancelled', 'rejected']))
                <span class="flex-1 text-center text-xs text-gray-400 py-2">{{ $appointment->status_label }}</span>
                @if(in_array($appointment->status, ['cancelled', 'rejected']))
                <form action="{{ route('admin.appointments.destroy', $appointment) }}" method="POST" class="inline-flex" id="deleteAppointmentFormMobile{{ $appointment->id }}">
                    @csrf
                    @method('DELETE')
                    <button type="button" class="inline-flex items-center justify-center rounded-lg border border-red-200 bg-red-50 p-2 text-red-600 hover:bg-red-100" onclick="openDeleteAppointmentModal('deleteAppointmentFormMobile{{ $appointment->id }}', '{{ addslashes($appointment->pet->name) }}', '{{ addslashes($appointment->user->name) }}', '{{ Carbon\Carbon::parse($appointment->appointment_date)->format('M d, Y') }}', '{{ Carbon\Carbon::parse($appointment->appointment_time)->format('h:i A') }}', '{{ $appointment->status_label }}')" title="Delete appointment" aria-label="Delete appointment">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                        </svg>
                    </button>
                </form>
                @endif
                @endif
            </div>
        </div>
        @empty
        <p class="text-center text-gray-500 py-8">No appointments found</p>
        @endforelse
    </div>
    
    <div class="hidden md:block overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Pet / Owner</th>
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
                        <p class="font-medium text-gray-900">{{ $appointment->pet->name }}</p>
                        <p class="text-xs text-gray-500">{{ $appointment->user->name }}</p>
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
                        <div class="flex items-center space-x-2">
                            @if($appointment->status === 'pending')
                            <form action="{{ route('admin.appointments.approve', $appointment) }}" method="POST" class="inline">
                                @csrf @method('PUT')
                                <button type="submit" class="text-xs bg-green-500 text-white px-3 py-1 rounded-lg hover:bg-green-600">Approve</button>
                            </form>
                            <button type="button" onclick="openRejectModal({{ $appointment->id }}, '{{ $appointment->pet->name }}', '{{ Carbon\Carbon::parse($appointment->appointment_date)->format('M d, Y') }}', '{{ Carbon\Carbon::parse($appointment->appointment_time)->format('h:i A') }}')" class="text-xs bg-red-500 text-white px-3 py-1 rounded-lg hover:bg-red-600">Reject</button>
                            @elseif($appointment->status === 'approved')
                            <form action="{{ route('admin.appointments.complete', $appointment) }}" method="POST" class="inline">
                                @csrf @method('PUT')
                                <button type="submit" class="text-xs bg-green-500 text-white px-3 py-1 rounded-lg hover:bg-green-600">Complete</button>
                            </form>
                            <a href="{{ route('appointments.reschedule', $appointment) }}" class="text-xs bg-blue-500 text-white px-3 py-1 rounded-lg hover:bg-blue-600">Reschedule</a>
                            <form action="{{ route('admin.appointments.reminder', $appointment) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="text-xs bg-purple-500 text-white px-3 py-1 rounded-lg hover:bg-purple-600">Remind</button>
                            </form>
                            @endif
                            <a href="{{ route('admin.patients.records', $appointment->pet) }}" class="text-xs bg-orange-100 text-orange-700 px-3 py-1 rounded-lg hover:bg-orange-200">Records</a>
                            @if(in_array($appointment->status, ['cancelled', 'rejected']))
                            <form action="{{ route('admin.appointments.destroy', $appointment) }}" method="POST" class="inline" id="deleteAppointmentFormDesktop{{ $appointment->id }}">
                                @csrf
                                @method('DELETE')
                                <button type="button" class="inline-flex items-center justify-center rounded-lg border border-red-200 bg-red-50 p-2 text-red-600 hover:bg-red-100" onclick="openDeleteAppointmentModal('deleteAppointmentFormDesktop{{ $appointment->id }}', '{{ addslashes($appointment->pet->name) }}', '{{ addslashes($appointment->user->name) }}', '{{ Carbon\Carbon::parse($appointment->appointment_date)->format('M d, Y') }}', '{{ Carbon\Carbon::parse($appointment->appointment_time)->format('h:i A') }}', '{{ $appointment->status_label }}')" title="Delete appointment" aria-label="Delete appointment">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                </button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-12 text-center">
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

<div id="rejectModal" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50 p-4">
    <div class="bg-white rounded-2xl p-4 md:p-6 w-full max-w-md">
        <h3 class="text-lg font-semibold text-gray-900 mb-2">Reject Appointment</h3>
        <p class="text-gray-600 mb-4">Please provide a reason for rejecting this appointment.</p>
        <div class="bg-gray-50 rounded-lg p-3 mb-4">
            <p class="text-sm text-gray-700"><strong>Pet:</strong> <span id="rejectPetName"></span></p>
            <p class="text-sm text-gray-700"><strong>Date:</strong> <span id="rejectDate"></span></p>
            <p class="text-sm text-gray-700"><strong>Time:</strong> <span id="rejectTime"></span></p>
        </div>
        <form id="rejectForm" method="POST">
            @csrf @method('PUT')
            <textarea name="reason" rows="4" required class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm mb-4" placeholder="Enter the reason for rejection"></textarea>
            <div class="flex flex-col md:flex-row justify-end gap-2 md:space-x-3">
                <button type="button" onclick="closeRejectModal()" class="px-4 py-2 text-gray-600 hover:text-gray-800 w-full md:w-auto">Close</button>
                <button type="submit" class="px-6 py-2 bg-red-500 text-white font-medium rounded-lg hover:bg-red-600 w-full md:w-auto">Reject Appointment</button>
            </div>
        </form>
    </div>
</div>

<div id="historyModal" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50 p-2 md:p-4">
    <div class="bg-white rounded-2xl p-4 md:p-6 w-full max-w-4xl max-h-[80vh] overflow-hidden flex flex-col">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-semibold text-gray-900">Appointment History</h3>
            <button onclick="closeHistoryModal()" class="text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
        <div class="overflow-auto flex-1">
            <div class="md:hidden space-y-3">
                @forelse($completedAppointments as $appointment)
                <div class="border border-gray-200 rounded-xl p-4">
                    <div class="flex items-start justify-between mb-2">
                        <div>
                            <p class="font-medium text-gray-900">{{ $appointment->pet->name }}</p>
                            <p class="text-xs text-gray-500">{{ $appointment->user->name }}</p>
                        </div>
                        <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full bg-gray-100 text-gray-700">Completed</span>
                    </div>
                    <p class="text-sm text-gray-600">{{ $appointment->service ? $appointment->service->name : 'General Checkup' }}</p>
                    <p class="text-xs text-gray-500">{{ Carbon\Carbon::parse($appointment->appointment_date)->format('M d, Y') }}</p>
                    <a href="{{ route('admin.patients.records', $appointment->pet) }}" class="mt-2 block text-center text-xs bg-orange-100 text-orange-700 px-3 py-2 rounded-lg hover:bg-orange-200">View Records</a>
                </div>
                @empty
                <p class="text-center text-gray-500 py-8">No completed appointments</p>
                @endforelse
            </div>
            <table class="hidden md:table w-full">
                <thead class="bg-gray-50 sticky top-0">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Pet / Owner</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Service</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date & Time</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($completedAppointments as $appointment)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3">
                            <p class="font-medium text-gray-900">{{ $appointment->pet->name }}</p>
                            <p class="text-xs text-gray-500">{{ $appointment->user->name }}</p>
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-600">
                            {{ $appointment->service ? $appointment->service->name : 'General Checkup' }}
                        </td>
                        <td class="px-4 py-3">
                            <p class="text-sm font-medium text-gray-900">{{ Carbon\Carbon::parse($appointment->appointment_date)->format('M d, Y') }}</p>
                            <p class="text-xs text-gray-500">{{ Carbon\Carbon::parse($appointment->appointment_time)->format('h:i A') }}</p>
                        </td>
                        <td class="px-4 py-3">
                            <span class="inline-flex px-3 py-1 text-xs font-medium rounded-full bg-gray-100 text-gray-700">Completed</span>
                        </td>
                        <td class="px-4 py-3">
                            <a href="{{ route('admin.patients.records', $appointment->pet) }}" class="text-xs bg-orange-100 text-orange-700 px-3 py-1 rounded-lg hover:bg-orange-200">View Records</a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-4 py-8 text-center">
                            <p class="text-gray-500">No completed appointments found</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<div id="deleteAppointmentModal" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50 p-4">
    <div class="bg-white rounded-2xl p-4 md:p-6 w-full max-w-md">
        <div class="flex items-center gap-3 mb-4">
            <div class="w-12 h-12 rounded-full bg-red-100 flex items-center justify-center">
                <svg class="w-6 h-6 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                </svg>
            </div>
            <div>
                <h3 class="text-lg font-semibold text-gray-900">Delete Appointment</h3>
                <p class="text-sm text-gray-500">This will permanently remove the appointment.</p>
            </div>
        </div>
        <div class="bg-gray-50 rounded-lg p-3 mb-4 space-y-1">
            <p class="text-sm text-gray-700"><strong>Status:</strong> <span id="deleteAppointmentStatus"></span></p>
            <p class="text-sm text-gray-700"><strong>Pet:</strong> <span id="deleteAppointmentPet"></span></p>
            <p class="text-sm text-gray-700"><strong>Owner:</strong> <span id="deleteAppointmentOwner"></span></p>
            <p class="text-sm text-gray-700"><strong>Date:</strong> <span id="deleteAppointmentDate"></span></p>
            <p class="text-sm text-gray-700"><strong>Time:</strong> <span id="deleteAppointmentTime"></span></p>
        </div>
        <div class="flex flex-col md:flex-row justify-end gap-2 md:space-x-3">
            <button type="button" onclick="closeDeleteAppointmentModal()" class="px-4 py-2 text-gray-600 hover:text-gray-800 w-full md:w-auto">Cancel</button>
            <button type="button" onclick="submitDeleteAppointment()" class="px-6 py-2 bg-red-500 text-white font-medium rounded-lg hover:bg-red-600 w-full md:w-auto">Delete Appointment</button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
let deleteAppointmentFormId = null;

function openRejectModal(id, petName, date, time) {
    document.getElementById('rejectForm').action = '/admin/appointments/' + id + '/reject';
    document.getElementById('rejectPetName').textContent = petName;
    document.getElementById('rejectDate').textContent = date;
    document.getElementById('rejectTime').textContent = time;
    document.getElementById('rejectModal').classList.remove('hidden');
    document.getElementById('rejectModal').classList.add('flex');
}

function closeRejectModal() {
    document.getElementById('rejectModal').classList.add('hidden');
    document.getElementById('rejectModal').classList.remove('flex');
}

function openHistoryModal() {
    document.getElementById('historyModal').classList.remove('hidden');
    document.getElementById('historyModal').classList.add('flex');
}

function closeHistoryModal() {
    document.getElementById('historyModal').classList.add('hidden');
    document.getElementById('historyModal').classList.remove('flex');
}

function openDeleteAppointmentModal(formId, petName, ownerName, date, time, status) {
    deleteAppointmentFormId = formId;
    document.getElementById('deleteAppointmentStatus').textContent = status;
    document.getElementById('deleteAppointmentPet').textContent = petName;
    document.getElementById('deleteAppointmentOwner').textContent = ownerName;
    document.getElementById('deleteAppointmentDate').textContent = date;
    document.getElementById('deleteAppointmentTime').textContent = time;
    document.getElementById('deleteAppointmentModal').classList.remove('hidden');
    document.getElementById('deleteAppointmentModal').classList.add('flex');
}

function closeDeleteAppointmentModal() {
    deleteAppointmentFormId = null;
    document.getElementById('deleteAppointmentModal').classList.add('hidden');
    document.getElementById('deleteAppointmentModal').classList.remove('flex');
}

function submitDeleteAppointment() {
    if (deleteAppointmentFormId) {
        document.getElementById(deleteAppointmentFormId).submit();
    }
}
</script>
@endpush
