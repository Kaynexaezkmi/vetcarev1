@extends('layouts.dashboard')
@section('title', 'Appointments - VetCare Admin')
@section('header-title', 'Appointment Management')

@php
    $appointmentsForModal = $appointments->getCollection()->map(function ($appointment) {
        $serviceAmount = (float) ($appointment->service_amount ?: $appointment->service?->price ?: 0);
        $reservationFee = (float) ($appointment->reservation_fee ?: ($serviceAmount * 0.2));
        $paymentProofPath = $appointment->payment_proof_path;
        $proofExtension = $paymentProofPath ? strtolower(pathinfo($paymentProofPath, PATHINFO_EXTENSION)) : null;

        return [
            'id' => $appointment->id,
            'pet' => $appointment->pet?->name ?? '-',
            'owner' => $appointment->user?->name ?? '-',
            'service' => $appointment->service?->name ?? 'General Checkup',
            'service_id' => $appointment->service_id,
            'date' => $appointment->appointment_date?->format('Y-m-d'),
            'date_label' => $appointment->appointment_date?->format('M d, Y') ?? '-',
            'time' => \Carbon\Carbon::parse($appointment->appointment_time)->format('H:i'),
            'time_label' => \Carbon\Carbon::parse($appointment->appointment_time)->format('h:i A'),
            'status' => $appointment->status_label,
            'status_key' => $appointment->status,
            'status_class' => $appointment->status_badge_class,
            'service_amount' => $serviceAmount,
            'reservation_fee' => $reservationFee,
            'payment_method' => $appointment->payment_method ?: 'GCash',
            'payment_reference' => $appointment->payment_reference ?: 'Not provided',
            'payment_submitted_at' => $appointment->payment_submitted_at?->format('M d, Y - h:i A') ?? $appointment->created_at?->format('M d, Y - h:i A'),
            'payment_proof_url' => $paymentProofPath ? asset('storage/'.$paymentProofPath) : null,
            'payment_proof_name' => $paymentProofPath ? basename($paymentProofPath) : null,
            'payment_proof_is_image' => in_array($proofExtension, ['jpg', 'jpeg', 'png', 'gif', 'webp'], true),
            'approve_url' => route('admin.appointments.approve', $appointment),
            'reject_url' => route('admin.appointments.reject', $appointment),
            'reschedule_url' => route('admin.appointments.reschedule', $appointment),
            'complete_url' => route('admin.appointments.complete', $appointment),
            'records_url' => route('admin.patients.records', $appointment->pet),
            'delete_form_id' => 'deleteAppointmentForm'.$appointment->id,
        ];
    })->values();
@endphp

@section('content')
<style>
    .appointment-modal-backdrop {
        align-items: center;
        background: rgba(17, 24, 39, 0.62);
        inset: 0;
        justify-content: center;
        padding: 16px;
        position: fixed;
        z-index: 50;
    }

    .appointment-modal-panel {
        background: #fff;
        border-radius: 14px;
        box-shadow: 0 25px 55px rgba(15, 23, 42, 0.28);
        max-height: 92vh;
        overflow-y: auto;
        padding: 20px;
        width: min(100%, 460px);
    }

    .appointment-modal-panel--payment {
        width: min(100%, 760px);
    }

    .appointment-modal-panel--history {
        width: min(100%, 860px);
    }

    .appointment-detail-row {
        align-items: center;
        border-bottom: 1px solid #eef2f7;
        display: grid;
        gap: 12px;
        grid-template-columns: 128px minmax(0, 1fr);
        padding: 8px 0;
    }

    .appointment-detail-row:last-child {
        border-bottom: 0;
        padding-bottom: 0;
    }

    .appointment-detail-label {
        color: #667085;
        font-size: 13px;
    }

    .appointment-detail-value {
        color: #111827;
        font-size: 13px;
        font-weight: 700;
    }

    .appointment-filter-control {
        height: 34px;
        max-width: 100%;
        padding-bottom: 6px !important;
        padding-top: 6px !important;
    }

    .appointment-filter-form {
        align-items: center;
        display: grid;
        gap: 8px;
        grid-template-columns: 160px 170px minmax(220px, 1fr) 92px auto;
    }

    .appointment-history-button {
        height: 34px;
        white-space: nowrap;
    }

    @media (max-width: 640px) {
        .appointment-detail-row {
            grid-template-columns: 1fr;
            gap: 3px;
        }

        .appointment-filter-form {
            grid-template-columns: 1fr;
        }
    }

    @media (min-width: 641px) and (max-width: 1180px) {
        .appointment-filter-form {
            grid-template-columns: 1fr 1fr;
        }
    }
</style>

@if(session('success'))
<div class="mb-4 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">{{ session('success') }}</div>
@endif

@if(session('error'))
<div class="mb-4 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">{{ session('error') }}</div>
@endif

@if($errors->any())
<div class="mb-4 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
    {{ $errors->first() }}
</div>
@endif

<div class="rounded-xl border border-gray-200 bg-white shadow-sm">
    <div class="border-b border-gray-100 px-4 py-3">
        <form method="GET" action="{{ route('admin.appointments.index') }}" class="appointment-filter-form">
            <select name="status" class="appointment-filter-control w-full rounded-lg border border-gray-300 px-3 text-sm" onchange="this.form.submit()">
                <option value="">All Status</option>
                @foreach(['pending' => 'Pending', 'approved' => 'Approved', 'rejected' => 'Rejected', 'cancelled' => 'Cancelled'] as $value => $label)
                <option value="{{ $value }}" @selected(request('status') === $value)>{{ $label }}</option>
                @endforeach
            </select>
            <input type="date" name="date" value="{{ request('date') }}" class="appointment-filter-control w-full rounded-lg border border-gray-300 px-3 text-sm" onchange="this.form.submit()">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search pet, owner, or service..." class="appointment-filter-control w-full rounded-lg border border-gray-300 px-3 text-sm">
            <button type="submit" class="appointment-filter-control rounded-lg bg-gray-900 px-4 text-sm font-semibold text-white hover:bg-gray-800">Search</button>
            <button type="button" class="appointment-history-button rounded-lg border border-orange-200 bg-orange-50 px-4 text-sm font-semibold text-orange-700 hover:bg-orange-100" data-action="history">
                Appointment History ({{ $completedAppointments->count() }})
            </button>
        </form>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full min-w-[860px]">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-gray-500">Pet / Owner</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-gray-500">Service</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-gray-500">Schedule</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-gray-500">Status</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-gray-500">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($appointments as $appointment)
                <tr class="hover:bg-gray-50/70">
                    <td class="px-4 py-3">
                        <p class="text-sm font-semibold text-gray-900">{{ $appointment->pet->name }}</p>
                        <p class="text-xs text-gray-500">{{ $appointment->user->name }}</p>
                    </td>
                    <td class="px-4 py-3 text-sm text-gray-700">{{ $appointment->service?->name ?? 'General Checkup' }}</td>
                    <td class="px-4 py-3">
                        <p class="text-sm font-medium text-gray-900">{{ $appointment->appointment_date->format('M d, Y') }}</p>
                        <p class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($appointment->appointment_time)->format('h:i A') }}</p>
                    </td>
                    <td class="px-4 py-3">
                        <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold {{ $appointment->status_badge_class }}">{{ $appointment->status_label }}</span>
                    </td>
                    <td class="px-4 py-3">
                        <div class="flex flex-wrap gap-2">
                            @if($appointment->status === 'pending')
                            <button type="button" class="rounded-md bg-green-500 px-3 py-1.5 text-xs font-semibold text-white hover:bg-green-600" data-action="approve" data-appointment-id="{{ $appointment->id }}">Approve</button>
                            <button type="button" class="rounded-md bg-red-500 px-3 py-1.5 text-xs font-semibold text-white hover:bg-red-600" data-action="reject" data-appointment-id="{{ $appointment->id }}">Reject</button>
                            @elseif($appointment->status === 'approved')
                            <form action="{{ route('admin.appointments.complete', $appointment) }}" method="POST">
                                @csrf
                                @method('PUT')
                                <button type="submit" class="rounded-md bg-green-500 px-3 py-1.5 text-xs font-semibold text-white hover:bg-green-600">Complete</button>
                            </form>
                            @endif

                            @if($appointment->canReschedule(true))
                            <button type="button" class="rounded-md bg-orange-100 px-3 py-1.5 text-xs font-semibold text-orange-700 hover:bg-orange-200" data-action="reschedule" data-appointment-id="{{ $appointment->id }}">Reschedule</button>
                            @endif

                            <button type="button" class="rounded-md bg-blue-50 px-3 py-1.5 text-xs font-semibold text-blue-700 hover:bg-blue-100" data-action="payment" data-appointment-id="{{ $appointment->id }}">View Payment</button>
                            <a href="{{ route('admin.patients.records', $appointment->pet) }}" class="rounded-md bg-gray-100 px-3 py-1.5 text-xs font-semibold text-gray-700 hover:bg-gray-200">Records</a>

                            @if(in_array($appointment->status, ['cancelled', 'rejected'], true))
                            <form action="{{ route('admin.appointments.destroy', $appointment) }}" method="POST" id="deleteAppointmentForm{{ $appointment->id }}">
                                @csrf
                                @method('DELETE')
                                <button type="button" class="rounded-md bg-red-50 px-3 py-1.5 text-xs font-semibold text-red-600 hover:bg-red-100" data-action="delete" data-appointment-id="{{ $appointment->id }}">Delete</button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-4 py-10 text-center text-sm text-gray-500">No appointments found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($appointments->hasPages())
    <div class="border-t border-gray-100 px-4 py-3">{{ $appointments->links() }}</div>
    @endif
</div>

<div id="modalBackdrop" class="appointment-modal-backdrop hidden">
    <div id="approveModal" class="appointment-modal-panel hidden">
        <div class="flex justify-end"><button type="button" class="text-gray-400 hover:text-gray-700" data-close-modal>&times;</button></div>
        <div class="text-center">
            <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-full border-2 border-green-400 text-green-500">
                <svg class="h-7 w-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m5 13 4 4L19 7"></path></svg>
            </div>
            <h3 class="mt-3 text-xl font-bold text-gray-950">Approve Appointment</h3>
            <p class="mt-2 text-sm text-gray-600">This will confirm the appointment and notify the pet owner.</p>
        </div>
        <div class="mt-4 rounded-lg border border-green-100 bg-green-50/60 p-3">
            <p class="mb-2 text-sm font-bold text-green-700">Appointment Details</p>
            <div class="space-y-2 text-sm" data-detail-list></div>
        </div>
        <form id="approveForm" method="POST" class="mt-4 flex items-center justify-between gap-3">
            @csrf
            @method('PUT')
            <button type="button" class="rounded-lg border border-gray-200 px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50" data-close-modal>Cancel</button>
            <button type="submit" class="rounded-lg bg-green-500 px-4 py-2 text-sm font-semibold text-white hover:bg-green-600">Approve Appointment</button>
        </form>
    </div>

    <div id="rejectModal" class="appointment-modal-panel hidden">
        <div class="flex justify-end"><button type="button" class="text-gray-400 hover:text-gray-700" data-close-modal>&times;</button></div>
        <div class="text-center">
            <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-full border-2 border-red-400 text-red-500">
                <svg class="h-7 w-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18 18 6M6 6l12 12"></path></svg>
            </div>
            <h3 class="mt-3 text-xl font-bold text-gray-950">Reject Appointment</h3>
            <p class="mt-2 text-sm text-gray-600">This reason will be visible to the pet owner.</p>
        </div>
        <div class="mt-4 rounded-lg border border-red-100 bg-red-50/50 p-3">
            <p class="mb-2 text-sm font-bold text-red-600">Appointment Details</p>
            <div class="space-y-2 text-sm" data-detail-list></div>
        </div>
        <form id="rejectForm" method="POST" class="mt-4">
            @csrf
            @method('PUT')
            <label class="mb-2 block text-sm font-semibold text-gray-800">Reason for rejection *</label>
            <textarea name="reason" rows="4" maxlength="250" required class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-red-400 focus:ring-red-400" placeholder="Enter reason for rejection..."></textarea>
            <div class="mt-4 flex items-center justify-between gap-3">
                <button type="button" class="rounded-lg border border-gray-200 px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50" data-close-modal>Cancel</button>
                <button type="submit" class="rounded-lg bg-red-500 px-4 py-2 text-sm font-semibold text-white hover:bg-red-600">Reject Appointment</button>
            </div>
        </form>
    </div>

    <div id="rescheduleModal" class="appointment-modal-panel hidden">
        <div class="flex justify-end"><button type="button" class="text-gray-400 hover:text-gray-700" data-close-modal>&times;</button></div>
        <div class="text-center">
            <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-full border-2 border-orange-400 text-orange-500">
                <svg class="h-7 w-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3M5 11h14M7 21h10a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2H7a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2Z"></path></svg>
            </div>
            <h3 class="mt-3 text-xl font-bold text-gray-950">Reschedule Appointment</h3>
            <p class="mt-2 text-sm text-gray-600">Select a new date & time and provide a reason for rescheduling.</p>
        </div>
        <div class="mt-4 rounded-lg border border-orange-100 bg-orange-50/50 p-3">
            <p class="mb-2 text-sm font-bold text-orange-600">Appointment Details</p>
            <div class="space-y-2 text-sm" data-detail-list></div>
        </div>
        <form id="rescheduleForm" method="POST" class="mt-4">
            @csrf
            @method('PUT')
            <div class="grid gap-3 sm:grid-cols-2">
                <div>
                    <label class="mb-1 block text-sm font-semibold text-gray-800">New Date *</label>
                    <input type="date" name="appointment_date" id="rescheduleDate" min="{{ now()->toDateString() }}" required class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-orange-400 focus:ring-orange-400">
                </div>
                <div>
                    <label class="mb-1 block text-sm font-semibold text-gray-800">New Time *</label>
                    <select name="appointment_time" id="rescheduleTime" required class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-orange-400 focus:ring-orange-400">
                        <option value="">Select date first</option>
                    </select>
                </div>
            </div>
            <label class="mb-1 mt-3 block text-sm font-semibold text-gray-800">Reason for rescheduling *</label>
            <textarea name="reason" id="rescheduleReason" rows="4" maxlength="250" required class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-orange-400 focus:ring-orange-400" placeholder="Enter reason for rescheduling..."></textarea>
            <p class="-mt-1 text-right text-xs text-gray-500"><span id="rescheduleReasonCount">0</span> / 250</p>
            <div class="mt-3 rounded-lg bg-blue-50 px-3 py-2 text-xs font-semibold text-blue-700">
                The pet owner will be notified about the new schedule.
            </div>
            <div class="mt-4 flex items-center justify-between gap-3">
                <button type="button" class="rounded-lg border border-gray-200 px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50" data-close-modal>Cancel</button>
                <button type="submit" class="rounded-lg bg-orange-500 px-4 py-2 text-sm font-semibold text-white hover:bg-orange-600">Reschedule Appointment</button>
            </div>
        </form>
    </div>

    <div id="paymentModal" class="appointment-modal-panel appointment-modal-panel--payment hidden">
        <div class="flex justify-end"><button type="button" class="text-gray-400 hover:text-gray-700" data-close-modal>&times;</button></div>
        <div class="text-center">
            <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-full border-2 border-orange-300 text-orange-500">
                <svg class="h-7 w-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1M6 19h12a2 2 0 0 0 2-2V8a2 2 0 0 0-2-2H6a2 2 0 0 0-2 2v9a2 2 0 0 0 2 2Z"></path></svg>
            </div>
            <h3 class="mt-3 text-xl font-bold text-gray-950">View Payment</h3>
            <p class="mt-2 text-sm text-gray-600">Review reservation fee details and submitted proof.</p>
        </div>
        <div class="mt-5 grid gap-4 md:grid-cols-2">
            <section class="rounded-lg border border-gray-200 p-4">
                <h4 class="mb-3 text-sm font-bold text-orange-600">Appointment Details</h4>
                <div class="space-y-2 text-sm" data-detail-list></div>
            </section>
            <section class="rounded-lg border border-gray-200 p-4">
                <h4 class="mb-3 text-sm font-bold text-orange-600">Payment Summary</h4>
                <div class="space-y-2 text-sm">
                    <div class="flex justify-between"><span class="text-gray-500">Service Amount</span><strong id="paymentServiceAmount"></strong></div>
                    <div class="flex justify-between"><span class="text-gray-500">Reservation Fee (20%)</span><strong id="paymentReservationFee"></strong></div>
                    <div class="flex justify-between border-t border-gray-100 pt-2"><span class="font-semibold text-gray-800">Expected Payment</span><strong class="text-orange-600" id="paymentExpected"></strong></div>
                </div>
                <div class="mt-3 rounded-lg bg-blue-50 px-3 py-2 text-xs text-blue-700">This fee will be deducted from the total service fee.</div>
            </section>
            <section class="rounded-lg border border-gray-200 p-4">
                <h4 class="mb-3 text-sm font-bold text-orange-600">Payment Proof</h4>
                <div id="paymentProofBox" class="rounded-lg bg-gray-50 p-3 text-center text-sm text-gray-500"></div>
            </section>
            <section class="rounded-lg border border-gray-200 p-4">
                <h4 class="mb-3 text-sm font-bold text-orange-600">Payment Information</h4>
                <div class="space-y-2 text-sm">
                    <div class="flex justify-between gap-3"><span class="text-gray-500">Method</span><strong id="paymentMethod"></strong></div>
                    <div class="flex justify-between gap-3"><span class="text-gray-500">Reference</span><strong class="text-right" id="paymentReference"></strong></div>
                    <div class="flex justify-between gap-3"><span class="text-gray-500">Submitted</span><strong class="text-right" id="paymentSubmitted"></strong></div>
                </div>
            </section>
        </div>
        <div class="mt-5 flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-end">
            <button type="button" class="rounded-lg border border-gray-200 px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50" data-close-modal>Cancel</button>
            <button type="button" class="rounded-lg border border-red-200 px-4 py-2 text-sm font-semibold text-red-600 hover:bg-red-50" id="paymentRejectButton">Reject Payment</button>
            <form id="paymentApproveForm" method="POST">
                @csrf
                @method('PUT')
                <button type="submit" class="w-full rounded-lg bg-green-500 px-4 py-2 text-sm font-semibold text-white hover:bg-green-600">Approve Payment</button>
            </form>
        </div>
    </div>

    <div id="deleteModal" class="appointment-modal-panel hidden">
        <h3 class="text-lg font-bold text-gray-950">Delete Appointment</h3>
        <p class="mt-2 text-sm text-gray-600">This will permanently remove the cancelled or rejected appointment.</p>
        <div class="mt-4 flex justify-end gap-3">
            <button type="button" class="rounded-lg border border-gray-200 px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50" data-close-modal>Cancel</button>
            <button type="button" class="rounded-lg bg-red-500 px-4 py-2 text-sm font-semibold text-white hover:bg-red-600" id="confirmDeleteButton">Delete</button>
        </div>
    </div>

    <div id="historyModal" class="appointment-modal-panel appointment-modal-panel--history hidden">
        <div class="flex items-start justify-between gap-4">
            <div>
                <h3 class="text-xl font-bold text-gray-950">Appointment History</h3>
                <p class="mt-1 text-sm text-gray-600">Completed appointments are stored here for quick review.</p>
            </div>
            <button type="button" class="text-2xl leading-none text-gray-400 hover:text-gray-700" data-close-modal>&times;</button>
        </div>

        <div class="mt-5 overflow-x-auto rounded-lg border border-gray-200">
            <table class="w-full min-w-[720px]">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-gray-500">Pet / Owner</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-gray-500">Service</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-gray-500">Schedule</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-gray-500">Completed</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-gray-500">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($completedAppointments as $completedAppointment)
                    <tr>
                        <td class="px-4 py-3">
                            <p class="text-sm font-semibold text-gray-900">{{ $completedAppointment->pet?->name ?? '-' }}</p>
                            <p class="text-xs text-gray-500">{{ $completedAppointment->user?->name ?? '-' }}</p>
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-700">{{ $completedAppointment->service?->name ?? 'General Checkup' }}</td>
                        <td class="px-4 py-3">
                            <p class="text-sm font-medium text-gray-900">{{ $completedAppointment->appointment_date?->format('M d, Y') ?? '-' }}</p>
                            <p class="text-xs text-gray-500">{{ $completedAppointment->appointment_time ? \Carbon\Carbon::parse($completedAppointment->appointment_time)->format('h:i A') : '-' }}</p>
                        </td>
                        <td class="px-4 py-3">
                            <span class="inline-flex rounded-full bg-green-100 px-2.5 py-1 text-xs font-semibold text-green-700">Completed</span>
                            <p class="mt-1 text-xs text-gray-500">{{ $completedAppointment->completed_at?->format('M d, Y h:i A') ?? 'Marked complete' }}</p>
                        </td>
                        <td class="px-4 py-3">
                            @if($completedAppointment->pet)
                            <a href="{{ route('admin.patients.records', $completedAppointment->pet) }}" class="rounded-md bg-gray-100 px-3 py-1.5 text-xs font-semibold text-gray-700 hover:bg-gray-200">Records</a>
                            @else
                            <span class="text-xs text-gray-400">No pet record</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-4 py-10 text-center text-sm text-gray-500">No completed appointments yet.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
const appointments = @json($appointmentsForModal);
const appointmentsById = appointments.reduce(function (items, appointment) {
    items[appointment.id] = appointment;

    return items;
}, {});

let activeAppointment = null;
let deleteFormId = null;

const modalIds = ['approveModal', 'rejectModal', 'rescheduleModal', 'paymentModal', 'deleteModal', 'historyModal'];

function modalBackdrop() {
    return document.getElementById('modalBackdrop');
}

function formatPeso(amount) {
    return '₱' + Number(amount || 0).toLocaleString('en-PH', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
    });
}

function detailRows(appointment, currentLabel = 'Date & Time') {
    return [
        ['Pet Name', appointment.pet],
        ['Owner', appointment.owner],
        ['Service', appointment.service],
        [currentLabel, appointment.date_label + ' • ' + appointment.time_label],
        ['Status', appointment.status],
    ].map(function (row) {
        return '<div class="appointment-detail-row"><span class="appointment-detail-label">' + row[0] + '</span><strong class="appointment-detail-value">' + row[1] + '</strong></div>';
    }).join('');
}

function showModal(id, appointment) {
    activeAppointment = appointment;
    modalIds.forEach(function (modalId) {
        const modal = document.getElementById(modalId);
        modal.classList.toggle('hidden', modalId !== id);
        modal.style.display = modalId === id ? 'block' : 'none';
    });
    modalBackdrop().classList.remove('hidden');
    modalBackdrop().style.display = 'flex';
}

function closeModal() {
    if (!modalBackdrop()) {
        return;
    }

    modalBackdrop().classList.add('hidden');
    modalBackdrop().style.display = 'none';
    modalIds.forEach(function (modalId) {
        const modal = document.getElementById(modalId);
        modal.classList.add('hidden');
        modal.style.display = 'none';
    });
}

function fillDetails(modalId, appointment, currentLabel) {
    document.querySelector('#' + modalId + ' [data-detail-list]').innerHTML = detailRows(appointment, currentLabel);
}

function openApprove(appointment) {
    fillDetails('approveModal', appointment, 'Date & Time');
    document.getElementById('approveForm').action = appointment.approve_url;
    showModal('approveModal', appointment);
}

function openReject(appointment) {
    fillDetails('rejectModal', appointment, 'Date & Time');
    document.getElementById('rejectForm').action = appointment.reject_url;
    showModal('rejectModal', appointment);
}

function openReschedule(appointment) {
    fillDetails('rescheduleModal', appointment, 'Current Date & Time');
    document.getElementById('rescheduleForm').action = appointment.reschedule_url;
    document.getElementById('rescheduleDate').value = '';
    document.getElementById('rescheduleTime').innerHTML = '<option value="">Select date first</option>';
    document.getElementById('rescheduleReason').value = '';
    document.getElementById('rescheduleReasonCount').textContent = '0';
    showModal('rescheduleModal', appointment);
}

function openPayment(appointment) {
    fillDetails('paymentModal', appointment, 'Date & Time');
    document.getElementById('paymentServiceAmount').textContent = formatPeso(appointment.service_amount);
    document.getElementById('paymentReservationFee').textContent = formatPeso(appointment.reservation_fee);
    document.getElementById('paymentExpected').textContent = formatPeso(appointment.reservation_fee);
    document.getElementById('paymentMethod').textContent = appointment.payment_method;
    document.getElementById('paymentReference').textContent = appointment.payment_reference;
    document.getElementById('paymentSubmitted').textContent = appointment.payment_submitted_at || '-';
    document.getElementById('paymentApproveForm').action = appointment.approve_url;

    const proofBox = document.getElementById('paymentProofBox');
    if (!appointment.payment_proof_url) {
        proofBox.innerHTML = '<p>No uploaded payment proof.</p>';
    } else if (appointment.payment_proof_is_image) {
        proofBox.innerHTML = '<img src="' + appointment.payment_proof_url + '" alt="Payment proof" class="mx-auto max-h-64 rounded-lg object-contain"><a href="' + appointment.payment_proof_url + '" target="_blank" class="mt-3 inline-flex rounded-lg bg-white px-3 py-2 text-xs font-semibold text-gray-700 hover:bg-gray-100">View Full Size</a>';
    } else {
        proofBox.innerHTML = '<p class="mb-3">' + appointment.payment_proof_name + '</p><a href="' + appointment.payment_proof_url + '" target="_blank" class="inline-flex rounded-lg bg-white px-3 py-2 text-xs font-semibold text-gray-700 hover:bg-gray-100">Open File</a>';
    }

    showModal('paymentModal', appointment);
}

function openDelete(appointment) {
    deleteFormId = appointment.delete_form_id;
    showModal('deleteModal', appointment);
}

function openHistory() {
    showModal('historyModal', null);
}

document.addEventListener('click', function (event) {
    const closeButton = event.target.closest('[data-close-modal]');

    if (closeButton) {
        closeModal();
        return;
    }

    if (modalBackdrop() && event.target === modalBackdrop()) {
        closeModal();
        return;
    }

    const paymentRejectButton = event.target.closest('#paymentRejectButton');

    if (paymentRejectButton && activeAppointment) {
        openReject(activeAppointment);
        return;
    }

    const confirmDeleteButton = event.target.closest('#confirmDeleteButton');

    if (confirmDeleteButton && deleteFormId) {
        document.getElementById(deleteFormId).submit();
        return;
    }

    const button = event.target.closest('[data-action]');

    if (!button) {
        return;
    }

    if (button.dataset.action === 'history') {
        openHistory();
        return;
    }

    const appointment = appointmentsById[Number(button.dataset.appointmentId)];

    if (!appointment) {
        return;
    }

    if (button.dataset.action === 'approve') {
        openApprove(appointment);
    } else if (button.dataset.action === 'reject') {
        openReject(appointment);
    } else if (button.dataset.action === 'reschedule') {
        openReschedule(appointment);
    } else if (button.dataset.action === 'payment') {
        openPayment(appointment);
    } else if (button.dataset.action === 'delete') {
        openDelete(appointment);
    }
});

document.addEventListener('change', function (event) {
    if (event.target.id !== 'rescheduleDate') {
        return;
    }

    const select = document.getElementById('rescheduleTime');

    if (!activeAppointment || !event.target.value) {
        select.innerHTML = '<option value="">Select date first</option>';
        return;
    }

    select.innerHTML = '<option value="">Loading...</option>';

    let url = '/appointments/slots?date=' + encodeURIComponent(event.target.value);
    if (activeAppointment.service_id) {
        url += '&service_id=' + encodeURIComponent(activeAppointment.service_id);
    }
    url += '&exclude_appointment_id=' + encodeURIComponent(activeAppointment.id);

    fetch(url)
        .then(function (response) {
            return response.json();
        })
        .then(function (result) {
            const slots = result.data || [];
            const availableSlots = slots.filter(function (slot) {
                return slot.available;
            });

            select.innerHTML = '<option value="">Select new time</option>';

            if (!availableSlots.length) {
                select.innerHTML = '<option value="">No slots available</option>';
                return;
            }

            availableSlots.forEach(function (slot) {
                const option = document.createElement('option');
                option.value = slot.time;
                option.textContent = slot.display;
                select.appendChild(option);
            });
        })
        .catch(function () {
            select.innerHTML = '<option value="">Unable to load slots</option>';
        });
});

document.addEventListener('input', function (event) {
    if (event.target.id !== 'rescheduleReason') {
        return;
    }

    document.getElementById('rescheduleReasonCount').textContent = event.target.value.length;
});

document.addEventListener('keydown', function (event) {
    if (event.key === 'Escape') {
        closeModal();
    }
});
</script>
@endpush
