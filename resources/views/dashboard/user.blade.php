@extends('layouts.dashboard')
@section('title', 'Dashboard - VetCare')
@section('header-title', 'Welcome back, ' . Auth::user()->name)

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

<div class="space-y-4 mb-6">
    <div class="bg-white rounded-xl shadow-sm p-4 md:p-6">
        <h3 class="text-base md:text-lg font-semibold text-gray-900 mb-4">Quick Actions</h3>
        <div class="grid grid-cols-2 gap-3 max-w-md">
            <a href="{{ route('appointments.create') }}" class="flex items-center p-3 md:p-4 bg-orange-50 rounded-xl hover:bg-orange-100 transition">
                <div class="w-8 md:w-10 h-8 md:h-10 bg-orange-500 rounded-lg flex items-center justify-center mr-2 md:mr-3 flex-shrink-0">
                    <svg class="w-4 h-4 md:w-5 md:h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                </div>
                <span class="text-xs md:text-sm font-medium text-gray-700">New Appointment</span>
            </a>
            <a href="{{ route('appointments.history') }}" class="flex items-center p-3 md:p-4 bg-blue-50 rounded-xl hover:bg-blue-100 transition">
                <div class="w-8 md:w-10 h-8 md:h-10 bg-blue-500 rounded-lg flex items-center justify-center mr-2 md:mr-3 flex-shrink-0">
                    <svg class="w-4 h-4 md:w-5 md:h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                    </svg>
                </div>
                <span class="text-xs md:text-sm font-medium text-gray-700">View History</span>
            </a>
        </div>
    </div>
    
    <div class="bg-white rounded-xl shadow-sm p-4 md:p-6">
        <h3 class="text-base md:text-lg font-semibold text-gray-900 mb-3 md:mb-4">Status</h3>
        <div class="flex flex-wrap gap-3 md:gap-4">
            <div class="flex items-center">
                <span class="w-3 md:w-4 h-3 md:h-4 rounded bg-green-500 mr-1 md:mr-2"></span>
                <span class="text-xs md:text-sm text-gray-600">Approved</span>
            </div>
            <div class="flex items-center">
                <span class="w-3 md:w-4 h-3 md:h-4 rounded bg-orange-500 mr-1 md:mr-2"></span>
                <span class="text-xs md:text-sm text-gray-600">Pending</span>
            </div>
            <div class="flex items-center">
                <span class="w-3 md:w-4 h-3 md:h-4 rounded bg-blue-500 mr-1 md:mr-2"></span>
                <span class="text-xs md:text-sm text-gray-600">Completed</span>
            </div>
            <div class="flex items-center">
                <span class="w-3 md:w-4 h-3 md:h-4 rounded bg-red-500 mr-1 md:mr-2"></span>
                <span class="text-xs md:text-sm text-gray-600">Rejected / Cancelled</span>
            </div>
        </div>
    </div>
</div>

<div class="bg-white rounded-xl shadow-sm p-4 md:p-6">
    <div class="flex flex-col md:flex-row md:items-center justify-between mb-4 md:mb-6 gap-3">
        <h3 class="text-base md:text-lg font-semibold text-gray-900">Your Appointments</h3>
        <a href="{{ route('appointments.create') }}" class="px-3 md:px-4 py-2 bg-orange-500 text-white text-xs md:text-sm font-medium rounded-lg hover:bg-orange-600 transition text-center md:text-left">
            + New Appointment
        </a>
    </div>
    
    @if($upcomingAppointments->count() > 0)
    <div class="space-y-3 md:hidden">
        @foreach($upcomingAppointments as $appointment)
        <div class="border border-gray-200 rounded-xl p-4">
            <div class="flex items-start justify-between mb-3">
                <div class="flex items-center">
                    <div class="w-8 h-8 bg-orange-100 rounded-full flex items-center justify-center mr-3">
                        <svg class="w-4 h-4 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                        </svg>
                    </div>
                    <div>
                        <p class="font-medium text-gray-900">{{ $appointment->pet->name ?? 'Unknown' }}</p>
                        <p class="text-xs text-gray-500">{{ $appointment->service ? $appointment->service->name : 'Checkup' }}</p>
                    </div>
                </div>
                <span class="px-2 py-1 text-xs font-medium rounded-full {{ $appointment->status_badge_class }}">{{ $appointment->status_label }}</span>
            </div>
            <div class="flex items-center justify-between text-xs text-gray-600 mb-3">
                <span>{{ Carbon\Carbon::parse($appointment->appointment_date)->format('M d, Y') }}</span>
                <span>{{ Carbon\Carbon::parse($appointment->appointment_time)->format('h:i A') }}</span>
            </div>
            @if($appointment->cancellation_reason)
            <p class="text-xs text-red-600 mb-3">Reason: {{ $appointment->cancellation_reason }}</p>
            @endif
            @if($appointment->canReschedule())
            <div class="flex gap-2">
                <a href="{{ route('appointments.reschedule', $appointment) }}" class="flex-1 text-center text-xs bg-blue-100 text-blue-700 px-3 py-2 rounded-lg hover:bg-blue-200">Reschedule</a>
                <button type="button" onclick="openCancelModal({{ $appointment->id }}, '{{ $appointment->pet->name }}', '{{ Carbon\Carbon::parse($appointment->appointment_date)->format('M d, Y') }}', '{{ Carbon\Carbon::parse($appointment->appointment_time)->format('h:i A') }}')" class="flex-1 text-center text-xs bg-red-100 text-red-700 px-3 py-2 rounded-lg hover:bg-red-200">Cancel</button>
            </div>
            @endif
        </div>
        @endforeach
    </div>
    
    <div class="hidden md:block overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Pet</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Service</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date & Time</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @foreach($upcomingAppointments as $appointment)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-4">
                        <div class="flex items-center">
                            <div class="w-8 h-8 bg-orange-100 rounded-full flex items-center justify-center mr-3">
                                <svg class="w-4 h-4 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                                </svg>
                            </div>
                            <span class="font-medium text-gray-900">{{ $appointment->pet->name ?? 'Unknown' }}</span>
                        </div>
                    </td>
                    <td class="px-4 py-4 text-sm text-gray-600">{{ $appointment->service ? $appointment->service->name : 'Checkup' }}</td>
                    <td class="px-4 py-4 text-sm">
                        <span class="text-gray-900">{{ Carbon\Carbon::parse($appointment->appointment_date)->format('M d, Y') }}</span><br>
                        <span class="text-gray-500">{{ Carbon\Carbon::parse($appointment->appointment_time)->format('h:i A') }}</span>
                    </td>
                    <td class="px-4 py-4">
                        <span class="px-3 py-1 text-xs font-medium rounded-full {{ $appointment->status_badge_class }}">{{ $appointment->status_label }}</span>
                    </td>
                    <td class="px-4 py-4 text-sm">
                        @if($appointment->canReschedule())
                        <a href="{{ route('appointments.reschedule', $appointment) }}" class="text-xs bg-blue-100 text-blue-700 px-3 py-1 rounded-lg hover:bg-blue-200 mr-2">Reschedule</a>
                        <button type="button" onclick="openCancelModal({{ $appointment->id }}, '{{ $appointment->pet->name }}', '{{ Carbon\Carbon::parse($appointment->appointment_date)->format('M d, Y') }}', '{{ Carbon\Carbon::parse($appointment->appointment_time)->format('h:i A') }}')" class="text-xs bg-red-100 text-red-700 px-3 py-1 rounded-lg hover:bg-red-200">Cancel</button>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @else
    <div class="text-center py-8 text-gray-500">
        <p>No upcoming appointments. <a href="{{ route('appointments.create') }}" class="text-orange-500 hover:text-orange-600">Book one now</a></p>
    </div>
    @endif
    
    <div class="mt-6 pt-6 border-t">
        <div class="flex items-center justify-between mb-4">
            <h4 class="text-md font-semibold text-gray-900">Calendar View</h4>
            <div class="flex items-center gap-4 text-sm">
                <div class="flex items-center gap-1">
                    <span class="w-3 h-3 rounded-full bg-orange-500"></span>
                    <span class="text-gray-600">Pending</span>
                </div>
                <div class="flex items-center gap-1">
                    <span class="w-3 h-3 rounded-full bg-green-500"></span>
                    <span class="text-gray-600">Approved</span>
                </div>
                <div class="flex items-center gap-1">
                    <span class="w-3 h-3 rounded-full bg-blue-500"></span>
                    <span class="text-gray-600">Completed</span>
                </div>
                <div class="flex items-center gap-1">
                    <span class="w-3 h-3 rounded-full bg-red-500"></span>
                    <span class="text-gray-600">Rejected / Cancelled</span>
                </div>
            </div>
        </div>
        <div id="calendar"></div>
    </div>
</div>

<div id="dateModal" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50 p-4">
    <div class="bg-white rounded-2xl p-4 md:p-6 w-full max-w-md">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-semibold text-gray-900" id="modalDateTitle">Appointments</h3>
            <button onclick="closeDateModal()" class="text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
        <div id="modalContent">
            <p class="text-gray-500 text-center py-4">Loading...</p>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js"></script>
<script>
function openDateModal(date) {
    document.getElementById('dateModal').classList.remove('hidden');
    document.getElementById('dateModal').classList.add('flex');
    document.getElementById('modalDateTitle').textContent = 'My Appointments - ' + formatDate(date);
    document.getElementById('modalContent').innerHTML = '<p class="text-gray-500 text-center py-4">Loading...</p>';
    
    fetch('/api/appointments/by-date?date=' + date)
        .then(response => response.json())
        .then(data => {
            if (data.appointments.length === 0) {
                document.getElementById('modalContent').innerHTML = '<p class="text-gray-500 text-center py-4">No appointments on this date</p>';
                return;
            }
            
            let html = '<div class="space-y-3">';
            data.appointments.forEach(apt => {
                const statusColors = {
                    'pending': 'bg-yellow-100 text-yellow-700',
                    'approved': 'bg-green-100 text-green-700',
                    'cancelled': 'bg-red-100 text-red-700',
                    'rejected': 'bg-red-100 text-red-700',
                    'completed': 'bg-blue-100 text-blue-700'
                };
                html += `
                    <div class="p-3 bg-gray-50 rounded-xl">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="font-medium text-gray-900">${apt.time}</p>
                                <p class="text-sm text-gray-600">${apt.pet_name} - ${apt.service}</p>
                            </div>
                            <span class="px-2 py-1 text-xs rounded-full ${statusColors[apt.status] || 'bg-gray-100 text-gray-700'}">
                                ${apt.status_label ?? apt.status}
                            </span>
                        </div>
                        ${apt.cancellation_reason ? `<p class="text-xs text-red-600 mt-2">Reason: ${apt.cancellation_reason}</p>` : ''}
                    </div>
                `;
            });
            html += '</div>';
            document.getElementById('modalContent').innerHTML = html;
        })
        .catch(error => {
            document.getElementById('modalContent').innerHTML = '<p class="text-red-500 text-center py-4">Error loading appointments</p>';
        });
}

function closeDateModal() {
    document.getElementById('dateModal').classList.add('hidden');
    document.getElementById('dateModal').classList.remove('flex');
}

function formatDate(dateStr) {
    const date = new Date(dateStr);
    return date.toLocaleDateString('en-US', { weekday: 'short', month: 'short', day: 'numeric', year: 'numeric' });
}

document.addEventListener('DOMContentLoaded', function() {
    var calendarEl = document.getElementById('calendar');
    if (calendarEl) {
        var calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek,timeGridDay'
            },
            height: 'auto',
            eventDisplay: 'none',
            events: function(info, successCallback, failureCallback) {
                fetch('/api/appointments/calendar')
                    .then(response => response.json())
                    .then(data => {
                        const events = data.map(apt => ({
                            date: apt.date,
                            count: apt.count,
                            pending: apt.pending,
                            approved: apt.approved,
                            completed: apt.completed,
                            cancelled: apt.cancelled,
                            rejected: apt.rejected
                        }));
                        successCallback(events);
                    })
                    .catch(error => failureCallback(error));
            },
            dayCellContent: function(arg) {
                const dateStr = arg.date.toISOString().split('T')[0];
                const dayNum = arg.dayNumberText.replace(/\D/g, '');
                
                return {
                    html: `
                        <div class="fc-daygrid-day-frame fc-scrollgrid-sync-inner" style="cursor: pointer; min-height: 60px; padding: 2px; display: flex; flex-direction: column; align-items: center; justify-content: center;">
                            <div class="fc-daygrid-day-number">${dayNum}</div>
                            <div class="fc-daycell-appointments" id="apt-count-${dateStr}"></div>
                        </div>
                    `
                };
            },
            datesSet: function(info) {
                loadAppointmentCounts(info.start, info.end);
            },
            dateClick: function(info) {
                openDateModal(info.dateStr);
            }
        });
        calendar.render();
    }
});

function loadAppointmentCounts(start, end) {
    fetch('/api/appointments/calendar?start=' + start.toISOString().split('T')[0] + '&end=' + end.toISOString().split('T')[0])
        .then(response => response.json())
        .then(data => {
            data.forEach(apt => {
                const el = document.getElementById('apt-count-' + apt.date);
                if (el) {
                    const total = apt.pending + apt.approved + apt.completed + apt.cancelled + (apt.rejected || 0);
                    if (total > 0) {
                        let html = '<div style="display: flex; align-items: center; justify-content: center; gap: 4px; flex-wrap: wrap;">';
                        if (apt.pending > 0) {
                            html += '<span style="display: inline-flex; align-items: center; gap: 2px;"><span class="w-2 h-2 rounded-full bg-orange-500"></span><span class="text-xs font-semibold text-orange-600">' + apt.pending + '</span></span>';
                        }
                        if (apt.approved > 0) {
                            html += '<span style="display: inline-flex; align-items: center; gap: 2px;"><span class="w-2 h-2 rounded-full bg-green-500"></span><span class="text-xs font-semibold text-green-600">' + apt.approved + '</span></span>';
                        }
                        if (apt.completed > 0) {
                            html += '<span style="display: inline-flex; align-items: center; gap: 2px;"><span class="w-2 h-2 rounded-full bg-blue-500"></span><span class="text-xs font-semibold text-blue-600">' + apt.completed + '</span></span>';
                        }
                        if (apt.cancelled > 0) {
                            html += '<span style="display: inline-flex; align-items: center; gap: 2px;"><span class="w-2 h-2 rounded-full bg-red-500"></span><span class="text-xs font-semibold text-red-600">' + apt.cancelled + '</span></span>';
                        }
                        if ((apt.rejected || 0) > 0) {
                            html += '<span style="display: inline-flex; align-items: center; gap: 2px;"><span class="w-2 h-2 rounded-full bg-red-500"></span><span class="text-xs font-semibold text-red-600">' + apt.rejected + '</span></span>';
                        }
                        html += '</div>';
                        el.innerHTML = html;
                    }
                }
            });
        });
}
</script>

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
            @csrf
            @method('PUT')
            <textarea name="reason" rows="4" required class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm mb-4" placeholder="Enter the reason for cancellation"></textarea>
            <div class="flex justify-end space-x-3">
                <button type="button" onclick="closeCancelModal()" class="px-4 py-2 text-gray-600 hover:text-gray-800">No, Keep</button>
                <button type="submit" class="px-6 py-2 bg-red-500 text-white font-medium rounded-lg hover:bg-red-600">Yes, Cancel</button>
            </div>
        </form>
    </div>
</div>

<script>
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
</script>
@endpush
