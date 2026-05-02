@extends('layouts.dashboard')
@section('title', 'Admin Dashboard - VetCare')
@section('header-title', 'Admin Dashboard')

@section('content')
@if(session('success'))
<div class="mb-4 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl">
    {{ session('success') }}
</div>
@endif

<div class="grid grid-cols-2 lg:grid-cols-4 gap-3 md:gap-6 mb-6">
    <div class="bg-white rounded-xl shadow-sm p-4 md:p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs md:text-sm text-gray-500">Today's Appts</p>
                <p class="text-xl md:text-3xl font-bold text-gray-900 mt-1">{{ $stats['today'] }}</p>
            </div>
            <div class="w-10 md:w-12 h-10 md:h-12 bg-orange-100 rounded-xl flex items-center justify-center">
                <svg class="w-5 md:w-6 h-5 md:h-6 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                </svg>
            </div>
        </div>
    </div>
    <div class="bg-white rounded-xl shadow-sm p-4 md:p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs md:text-sm text-gray-500">Pending</p>
                <p class="text-xl md:text-3xl font-bold text-yellow-500 mt-1">{{ $stats['pending'] }}</p>
            </div>
            <div class="w-10 md:w-12 h-10 md:h-12 bg-yellow-100 rounded-xl flex items-center justify-center">
                <svg class="w-5 md:w-6 h-5 md:h-6 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
        </div>
    </div>
    <div class="bg-white rounded-xl shadow-sm p-4 md:p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs md:text-sm text-gray-500">Completed</p>
                <p class="text-xl md:text-3xl font-bold text-green-500 mt-1">{{ $stats['completed'] }}</p>
            </div>
            <div class="w-10 md:w-12 h-10 md:h-12 bg-green-100 rounded-xl flex items-center justify-center">
                <svg class="w-5 md:w-6 h-5 md:h-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
        </div>
    </div>
    <div class="bg-white rounded-xl shadow-sm p-4 md:p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs md:text-sm text-gray-500">Inquiries</p>
                <p class="text-xl md:text-3xl font-bold text-red-500 mt-1">{{ $stats['unread_inquiries'] }}</p>
            </div>
            <div class="w-10 md:w-12 h-10 md:h-12 bg-red-100 rounded-xl flex items-center justify-center">
                <svg class="w-5 md:w-6 h-5 md:h-6 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                </svg>
            </div>
        </div>
    </div>
</div>

<div class="grid lg:grid-cols-3 gap-4 md:gap-6 mb-6">
    <div class="bg-white rounded-xl shadow-sm p-4 md:p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="font-semibold text-gray-900 text-sm md:text-base">Pending Appointments</h3>
            <a href="{{ route('admin.appointments.index') }}" class="text-xs md:text-sm text-orange-500 hover:text-orange-600">View All</a>
        </div>
        @if($pendingAppointments->count() > 0)
        <div class="space-y-3">
            @foreach($pendingAppointments as $apt)
            <div class="p-3 bg-gray-50 rounded-xl">
                <div class="flex justify-between items-start">
                    <div class="min-w-0">
                        <p class="font-medium text-gray-900 text-sm truncate">{{ $apt->pet->name }}</p>
                        <p class="text-xs text-gray-500 truncate">{{ $apt->user->name }}</p>
                        <p class="text-xs text-gray-400 mt-1">{{ Carbon\Carbon::parse($apt->appointment_date)->format('M d') }} at {{ Carbon\Carbon::parse($apt->appointment_time)->format('h:i A') }}</p>
                    </div>
                    <form action="{{ route('admin.appointments.approve', $apt) }}" method="POST" class="inline flex-shrink-0 ml-2">
                        @csrf
                        @method('PUT')
                        <button type="submit" class="text-xs bg-green-500 text-white px-2 py-1 rounded hover:bg-green-600">Approve</button>
                    </form>
                </div>
            </div>
            @endforeach
        </div>
        @else
        <p class="text-sm text-gray-500 text-center py-4">No pending appointments</p>
        @endif
    </div>
    
    <div class="bg-white rounded-xl shadow-sm p-4 md:p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="font-semibold text-gray-900 text-sm md:text-base">Today's Schedule</h3>
        </div>
        @if($todayAppointments->count() > 0)
        <div class="space-y-3">
            @foreach($todayAppointments as $apt)
            <div class="flex items-center space-x-2 md:space-x-3 p-2 md:p-3 bg-gray-50 rounded-xl">
                <div class="w-8 md:w-10 h-8 md:h-10 bg-orange-100 rounded-lg flex items-center justify-center flex-shrink-0">
                    <span class="text-xs font-semibold text-orange-500">{{ Carbon\Carbon::parse($apt->appointment_time)->format('h:i') }}</span>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="font-medium text-gray-900 text-sm truncate">{{ $apt->pet->name }}</p>
                    <p class="text-xs text-gray-500 truncate">{{ $apt->user->name }}</p>
                </div>
                <span class="px-2 py-1 text-xs rounded-full flex-shrink-0 
                    @if($apt->status === 'pending') bg-yellow-100 text-yellow-700
                    @elseif($apt->status === 'approved') bg-green-100 text-green-700
                    @else bg-gray-100 text-gray-700 @endif">
                    {{ ucfirst($apt->status) }}
                </span>
            </div>
            @endforeach
        </div>
        @else
        <p class="text-sm text-gray-500 text-center py-4">No appointments today</p>
        @endif
    </div>
    
    <div class="bg-white rounded-xl shadow-sm p-4 md:p-6">
        <h3 class="font-semibold text-gray-900 mb-4 text-sm md:text-base">Quick Stats</h3>
        <div class="space-y-3 md:space-y-4">
            <div class="flex items-center justify-between p-2 md:p-3 bg-gray-50 rounded-xl">
                <div class="flex items-center">
                    <svg class="w-4 md:w-5 h-4 md:h-5 text-orange-500 mr-2 md:mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                    </svg>
                    <span class="text-xs md:text-sm text-gray-600">Total Patients</span>
                </div>
                <span class="font-semibold text-gray-900 text-sm md:text-base">{{ $stats['total_patients'] }}</span>
            </div>
            <div class="flex items-center justify-between p-2 md:p-3 bg-gray-50 rounded-xl">
                <div class="flex items-center">
                    <svg class="w-4 md:w-5 h-4 md:h-5 text-orange-500 mr-2 md:mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                    </svg>
                    <span class="text-xs md:text-sm text-gray-600">Services</span>
                </div>
                <span class="font-semibold text-gray-900 text-sm md:text-base">{{ $stats['total_services'] }}</span>
            </div>
        </div>
    </div>
</div>

<div class="bg-white rounded-xl shadow-sm p-4 md:p-6">
    <div class="flex flex-col md:flex-row md:items-center justify-between mb-4 gap-2">
        <h3 class="font-semibold text-gray-900 text-sm md:text-base">Appointment Calendar</h3>
        <div class="flex items-center gap-2 md:gap-4 text-xs md:text-sm">
            <div class="flex items-center gap-1">
                <span class="w-2 md:w-3 h-2 md:h-3 rounded-full bg-orange-500"></span>
                <span class="text-gray-600">Pending</span>
            </div>
            <div class="flex items-center gap-1">
                <span class="w-2 md:w-3 h-2 md:h-3 rounded-full bg-green-500"></span>
                <span class="text-gray-600">Approved</span>
            </div>
            <div class="flex items-center gap-1">
                <span class="w-2 md:w-3 h-2 md:h-3 rounded-full bg-blue-500"></span>
                <span class="text-gray-600">Completed</span>
            </div>
            <div class="flex items-center gap-1">
                <span class="w-2 md:w-3 h-2 md:h-3 rounded-full bg-red-500"></span>
                <span class="text-gray-600">Rejected / Cancelled</span>
            </div>
        </div>
    </div>
    <div id="adminCalendar" class="hidden md:block"></div>
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
<script>
function openDateModal(date) {
    document.getElementById('dateModal').classList.remove('hidden');
    document.getElementById('dateModal').classList.add('flex');
    document.getElementById('modalDateTitle').textContent = 'Appointments - ' + formatDate(date);
    document.getElementById('modalContent').innerHTML = '<p class="text-gray-500 text-center py-4">Loading...</p>';
    
    fetch('/appointments/by-date?date=' + date)
        .then(response => response.json())
        .then(data => {
            if (data.appointments.length === 0) {
                document.getElementById('modalContent').innerHTML = '<p class="text-gray-500 text-center py-4">No appointments on this date</p>';
                return;
            }
            
            let html = '<div class="space-y-3">';
            data.appointments.forEach(apt => {
                const statusColors = {
                    'pending': 'bg-orange-100 text-orange-700',
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
                                <p class="text-xs text-gray-500">${apt.user_name}</p>
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
    var calendarEl = document.getElementById('adminCalendar');
    if (calendarEl) {
        var calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            height: 'auto',
            eventDisplay: 'none',
            initialDate: new Date().toISOString().split('T')[0],
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek,timeGridDay'
            },
            events: function(info, successCallback, failureCallback) {
                fetch('/appointments/calendar-events')
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
                        <div class="fc-daygrid-day-frame fc-scrollgrid-sync-inner" style="cursor: pointer; min-height: auto; padding: 2px;">
                            <div class="fc-daygrid-day-number" style="text-align: center; margin-bottom: 2px;">${dayNum}</div>
                            <div class="fc-daycell-appointments" id="apt-count-${dateStr}" style="text-align: center;"></div>
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
    fetch('/appointments/calendar-events?start=' + start.toISOString().split('T')[0] + '&end=' + end.toISOString().split('T')[0])
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
@endpush
