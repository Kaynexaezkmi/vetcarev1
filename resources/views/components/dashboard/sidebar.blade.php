<aside class="w-64 bg-white border-r border-gray-200 flex flex-col sidebar-desktop">
    <div class="p-6 border-b border-gray-200">
        <a href="/" class="flex items-center space-x-2">
            <div class="w-28 h-28 rounded-xl flex items-center justify-center overflow-hidden">
                <img src="{{ asset('images/logovet.jpg') }}" alt="Logo" class="w-full h-full object-contain">
            </div>
        </a>
    </div>
    <nav class="flex-1 p-4 space-y-1">
        <a href="{{ route('dashboard') }}" class="flex items-center px-4 py-3 text-sm font-medium rounded-lg {{ request()->routeIs('dashboard') ? 'bg-orange-50 text-orange-600' : 'text-gray-600 hover:bg-gray-50' }}">
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
            </svg>
            Dashboard
        </a>
        
        @if(Auth::user()->isAdmin())
            <a href="{{ route('admin.appointments.index') }}" class="flex items-center px-4 py-3 text-sm font-medium rounded-lg {{ request()->routeIs('admin.appointments.*') ? 'bg-orange-50 text-orange-600' : 'text-gray-600 hover:bg-gray-50' }}">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                </svg>
                Appointments
                @if(isset($pendingAppointmentsCount) && $pendingAppointmentsCount > 0)
                    <span class="ml-auto bg-orange-500 text-white text-xs font-bold rounded-full h-5 w-5 flex items-center justify-center">
                        {{ $pendingAppointmentsCount > 9 ? '9+' : $pendingAppointmentsCount }}
                    </span>
                @endif
            </a>
            <a href="{{ route('admin.patients.index') }}" class="flex items-center px-4 py-3 text-sm font-medium rounded-lg {{ request()->routeIs('admin.patients.*') ? 'bg-orange-50 text-orange-600' : 'text-gray-600 hover:bg-gray-50' }}">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                </svg>
                Patient Records
            </a>
            <a href="{{ route('admin.inquiries.index') }}" class="flex items-center px-4 py-3 text-sm font-medium rounded-lg {{ request()->routeIs('admin.inquiries.*') ? 'bg-orange-50 text-orange-600' : 'text-gray-600 hover:bg-gray-50' }}">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path>
                </svg>
                Inquiries
                @if(isset($unreadInquiriesCount) && $unreadInquiriesCount > 0)
                    <span class="ml-auto bg-orange-500 text-white text-xs font-bold rounded-full h-5 w-5 flex items-center justify-center">
                        {{ $unreadInquiriesCount > 9 ? '9+' : $unreadInquiriesCount }}
                    </span>
                @endif
            </a>
            <a href="{{ route('admin.feedback.index') }}" class="flex items-center px-4 py-3 text-sm font-medium rounded-lg {{ request()->routeIs('admin.feedback.*') ? 'bg-orange-50 text-orange-600' : 'text-gray-600 hover:bg-gray-50' }}">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path>
                </svg>
                Feedback
            </a>
            <a href="{{ route('admin.services.index') }}" class="flex items-center px-4 py-3 text-sm font-medium rounded-lg {{ request()->routeIs('admin.services.*') ? 'bg-orange-50 text-orange-600' : 'text-gray-600 hover:bg-gray-50' }}">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                </svg>
                Services
            </a>
            <a href="{{ route('admin.users.index') }}" class="flex items-center px-4 py-3 text-sm font-medium rounded-lg {{ request()->routeIs('admin.users.*') ? 'bg-orange-50 text-orange-600' : 'text-gray-600 hover:bg-gray-50' }}">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                </svg>
                Users
            </a>
            <a href="{{ route('reminders') }}" class="flex items-center px-4 py-3 text-sm font-medium rounded-lg {{ request()->routeIs('reminders') ? 'bg-orange-50 text-orange-600' : 'text-gray-600 hover:bg-gray-50' }}">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                </svg>
                Reminders
                @if(isset($unreadRemindersCount) && $unreadRemindersCount > 0)
                    <span class="ml-auto bg-orange-500 text-white text-xs font-bold rounded-full h-5 w-5 flex items-center justify-center">
                        {{ $unreadRemindersCount > 9 ? '9+' : $unreadRemindersCount }}
                    </span>
                @endif
            </a>
        @else
            <a href="{{ route('appointments.create') }}" class="flex items-center px-4 py-3 text-sm font-medium rounded-lg {{ request()->routeIs('appointments.create') ? 'bg-orange-50 text-orange-600' : 'text-gray-600 hover:bg-gray-50' }}">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Book Appointment
            </a>
            <a href="{{ route('appointments.history') }}" class="flex items-center px-4 py-3 text-sm font-medium rounded-lg {{ request()->routeIs('appointments.history') ? 'bg-orange-50 text-orange-600' : 'text-gray-600 hover:bg-gray-50' }}">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                </svg>
                Appointment History
            </a>
            <a href="{{ route('medical-records') }}" class="flex items-center px-4 py-3 text-sm font-medium rounded-lg {{ request()->routeIs('medical-records') ? 'bg-orange-50 text-orange-600' : 'text-gray-600 hover:bg-gray-50' }}">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                Medical Records
                @if(isset($recentMedicalRecordsCount) && $recentMedicalRecordsCount > 0)
                    <span class="medical-records-badge ml-auto bg-orange-500 text-white text-xs font-bold rounded-full h-5 w-5 flex items-center justify-center">
                        {{ $recentMedicalRecordsCount > 9 ? '9+' : $recentMedicalRecordsCount }}
                    </span>
                @endif
            </a>
            <a href="{{ route('reminders') }}" class="flex items-center px-4 py-3 text-sm font-medium rounded-lg {{ request()->routeIs('reminders') ? 'bg-orange-50 text-orange-600' : 'text-gray-600 hover:bg-gray-50' }}">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                </svg>
                Reminders
                @if(isset($unreadRemindersCount) && $unreadRemindersCount > 0)
                    <span class="ml-auto bg-orange-500 text-white text-xs font-bold rounded-full h-5 w-5 flex items-center justify-center">
                        {{ $unreadRemindersCount > 9 ? '9+' : $unreadRemindersCount }}
                    </span>
                @endif
            </a>
            <a href="{{ route('feedback') }}" class="flex items-center px-4 py-3 text-sm font-medium rounded-lg {{ request()->routeIs('feedback') ? 'bg-orange-50 text-orange-600' : 'text-gray-600 hover:bg-gray-50' }}">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path>
                </svg>
                Feedback
            </a>
        @endif
    </nav>
</aside>
