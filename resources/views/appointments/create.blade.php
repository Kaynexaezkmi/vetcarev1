@extends('layouts.dashboard')
@section('title', 'Book Appointment - VetCare')
@section('header-title', 'Book New Appointment')

@php
    $submittedAppointment = $submittedAppointment ?? null;
    $appointmentServiceCatalog = collect($serviceCatalog ?? [])->filter(fn ($service) => ! empty($service['service_id']))->values();
    $selectedPet = $submittedAppointment?->pet ?? $pets->firstWhere('id', (int) old('pet_id')) ?? $pets->first();
    $selectedService = $submittedAppointment?->service ?? $services->firstWhere('id', (int) old('service_id'));
    $selectedServiceCatalog = $appointmentServiceCatalog->firstWhere('service_id', $selectedService?->id);
    $clinicLocation = '713 Earnshaw St, Sampaloc, Manila, 1008 Metro Manila';
    $serviceAmount = (float) ($selectedServiceCatalog['booking_amount'] ?? $selectedService?->price ?? 0);
    $reservationFee = $serviceAmount * 0.2;
    $steps = [
        ['number' => 1, 'label' => 'Select Pet', 'active' => true],
        ['number' => 2, 'label' => 'Service', 'active' => false],
        ['number' => 3, 'label' => 'Schedule', 'active' => false],
        ['number' => 4, 'label' => 'Payment', 'active' => false],
        ['number' => 5, 'label' => 'Confirm', 'active' => false],
        ['number' => 6, 'label' => 'Notes', 'active' => false],
    ];
@endphp

@section('content')
<div class="mx-auto max-w-7xl">
    @if(session('error'))
    <div class="mb-4 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
        {{ session('error') }}
    </div>
    @endif

    @if($errors->any())
    <div class="mb-4 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-red-700">
        <ul class="list-disc list-inside space-y-1 text-sm">
            @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    @if($submittedAppointment)
    @php
        $submittedDateTime = $submittedAppointment->appointment_date->format('M d, Y').' at '.\Carbon\Carbon::parse($submittedAppointment->appointment_time)->format('h:i A');
        $submittedPetDetails = collect([$submittedAppointment->pet?->type, $submittedAppointment->pet?->gender, $submittedAppointment->pet?->breed])->filter()->implode(' • ');
    @endphp

    <div class="mb-4">
        <div class="mb-2 flex items-center gap-3">
            <span class="rounded-lg bg-green-500 px-3 py-1.5 text-xs font-bold uppercase tracking-normal text-white">Next Step</span>
            <h2 class="text-2xl font-bold text-gray-950">Booking Submitted!</h2>
        </div>
        <p class="text-sm text-gray-600">Your appointment has been submitted successfully.</p>
        <p class="mt-1 text-sm text-gray-600">Please wait while our admin reviews your appointment and payment.</p>
    </div>

    <div class="grid gap-6 lg:grid-cols-[minmax(0,1fr)_340px]">
        <div class="space-y-4">
            <section class="rounded-xl border border-orange-100 bg-orange-50 p-4 shadow-sm">
                <div class="flex items-center gap-3">
                    <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-full bg-white text-orange-500">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div>
                        <p class="text-xs font-semibold uppercase text-gray-500">Current Status</p>
                        <h3 class="text-lg font-bold text-orange-600">Pending Approval</h3>
                        <p class="mt-1 text-sm text-gray-600">We will notify you once your appointment has been approved.</p>
                    </div>
                </div>
            </section>

            <section class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm">
                <h3 class="text-lg font-semibold text-gray-950">What happens next?</h3>
                <div class="mt-4 grid gap-3 md:grid-cols-3">
                    <div class="rounded-xl border border-gray-100 bg-gray-50 p-4 text-center">
                        <p class="font-semibold text-gray-900">1. Admin Verification</p>
                        <p class="mt-2 text-sm text-gray-600">Our admin will verify your payment and appointment details.</p>
                    </div>
                    <div class="rounded-xl border border-gray-100 bg-gray-50 p-4 text-center">
                        <p class="font-semibold text-gray-900">2. Approval</p>
                        <p class="mt-2 text-sm text-gray-600">Once verified, your appointment will be approved.</p>
                    </div>
                    <div class="rounded-xl border border-gray-100 bg-gray-50 p-4 text-center">
                        <p class="font-semibold text-gray-900">3. Notification</p>
                        <p class="mt-2 text-sm text-gray-600">You will receive a notification via app and email.</p>
                    </div>
                </div>
                <div class="mt-4 rounded-lg bg-blue-50 px-4 py-3 text-sm text-blue-700">
                    You can view the status of your booking anytime in Appointment History.
                </div>
            </section>

            <section class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm">
                <h3 class="text-lg font-semibold text-gray-950">Your Appointment Summary</h3>
                <div class="mt-4 grid gap-4 md:grid-cols-2">
                    <div>
                        <p class="text-xs font-semibold text-gray-500">Pet</p>
                        <p class="font-semibold text-gray-950">{{ $submittedAppointment->pet?->name }}</p>
                        <p class="text-sm text-gray-600">{{ $submittedPetDetails ?: 'Pet details not set' }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-gray-500">Service</p>
                        <p class="font-semibold text-gray-950">{{ $submittedAppointment->service?->name ?? 'General Checkup' }}</p>
                        <p class="text-sm text-gray-600">₱{{ number_format($serviceAmount, 2) }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-gray-500">Date & Time</p>
                        <p class="font-semibold text-gray-950">{{ $submittedDateTime }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-gray-500">Location</p>
                        <p class="font-semibold text-gray-950">{{ $clinicLocation }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-gray-500">Payment Method</p>
                        <p class="font-semibold text-gray-950">GCash</p>
                        <p class="text-sm text-gray-600">For verification</p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-gray-500">Payment Status</p>
                        <p class="font-semibold text-orange-600">For Verification</p>
                    </div>
                </div>
            </section>

            <section class="rounded-xl border border-orange-100 bg-orange-50 p-4 shadow-sm">
                <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h3 class="font-semibold text-orange-700">Need to make changes or cancel?</h3>
                        <p class="mt-1 text-sm text-gray-600">You can cancel your booking before it gets approved.</p>
                    </div>
                    <a href="{{ route('appointments.history') }}" class="inline-flex items-center justify-center rounded-lg border border-orange-200 bg-white px-4 py-2 text-sm font-semibold text-orange-600 transition hover:bg-orange-50">
                        Appointment History
                    </a>
                </div>
            </section>

            <div class="flex justify-center">
                <a href="{{ route('dashboard') }}" class="inline-flex items-center justify-center rounded-lg border border-gray-200 bg-white px-5 py-2.5 text-sm font-semibold text-gray-700 transition hover:bg-gray-50">
                    Back to Dashboard
                </a>
            </div>
        </div>

        <aside class="space-y-5 lg:sticky lg:top-6 lg:self-start">
            <section class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm md:p-5">
                <h3 class="mb-4 font-semibold text-gray-950">Appointment Summary</h3>
                <dl class="space-y-3 text-sm">
                    <div>
                        <dt class="text-gray-500">Pet</dt>
                        <dd class="font-medium text-gray-900">{{ $submittedAppointment->pet?->name }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500">Service</dt>
                        <dd class="font-medium text-gray-900">{{ $submittedAppointment->service?->name ?? 'General Checkup' }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500">Date & Time</dt>
                        <dd class="font-medium text-gray-900">{{ $submittedDateTime }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500">Location</dt>
                        <dd class="font-medium text-gray-900">{{ $clinicLocation }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500">Payment Method</dt>
                        <dd class="font-medium text-gray-900">GCash</dd>
                    </div>
                </dl>
            </section>

            <section class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm md:p-5">
                <h3 class="mb-4 font-semibold text-gray-950">Payment Summary</h3>
                <dl class="space-y-3 text-sm">
                    <div class="flex items-center justify-between gap-3">
                        <dt class="text-gray-600">Service Amount</dt>
                        <dd class="font-medium text-gray-900">₱{{ number_format($serviceAmount, 2) }}</dd>
                    </div>
                    <div class="flex items-center justify-between gap-3">
                        <dt class="font-medium text-green-600">Reservation Fee (20%)</dt>
                        <dd class="font-semibold text-green-600">₱{{ number_format($reservationFee, 2) }}</dd>
                    </div>
                </dl>
                <div class="mt-5 flex items-center justify-between gap-3 border-t border-gray-100 pt-4">
                    <p class="text-sm font-semibold text-gray-700">Total to Pay</p>
                    <p class="text-xl font-bold text-orange-600">₱{{ number_format($reservationFee, 2) }}</p>
                </div>
                <div class="mt-4 rounded-lg bg-blue-50 px-4 py-3 text-sm text-blue-700">
                    Your payment is now for verification. We will confirm your appointment once verified.
                </div>
            </section>
        </aside>
    </div>
    @else

    <div class="mb-4">
        <div>
            <div class="mb-2 flex items-center gap-3">
                <span id="stepBadge" class="rounded-lg bg-orange-500 px-3 py-1.5 text-xs font-bold uppercase tracking-normal text-white">Step 1</span>
                <h2 id="stepTitle" class="text-2xl font-bold text-gray-950">Select Pet</h2>
            </div>
            <p id="stepDescription" class="text-sm text-gray-600">Choose which pet you want to book an appointment for.</p>
        </div>
    </div>

    <div class="mb-4 overflow-x-auto pb-1">
        <div class="flex min-w-[680px] items-center gap-3">
            @foreach($steps as $step)
            <div class="flex min-w-0 flex-1 items-center gap-3">
                <div class="flex flex-col items-center gap-1.5">
                    <div data-step-circle="{{ $step['number'] }}" class="flex h-8 w-8 items-center justify-center rounded-full border text-xs font-bold {{ $step['active'] ? 'border-orange-500 bg-orange-500 text-white shadow-sm shadow-orange-200' : 'border-gray-200 bg-white text-gray-700' }}">
                        {{ $step['number'] }}
                    </div>
                    <span data-step-label="{{ $step['number'] }}" class="whitespace-nowrap text-xs font-medium {{ $step['active'] ? 'text-orange-600' : 'text-gray-500' }}">{{ $step['label'] }}</span>
                </div>
                @if(! $loop->last)
                <div class="h-px flex-1 bg-gray-200"></div>
                @endif
            </div>
            @endforeach
        </div>
    </div>

    <form action="{{ route('appointments.store') }}" method="POST" id="appointmentForm" enctype="multipart/form-data" class="grid gap-6 lg:grid-cols-[minmax(0,1fr)_340px]">
        @csrf

        <div class="space-y-4">
            <section id="petSelectionSection" class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm">
                <div class="mb-4 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-950">Your Pets</h3>
                        <p class="mt-1 text-sm text-gray-500">Select a pet from your list to continue.</p>
                    </div>

                    @if($pets->isNotEmpty())
                    <div class="flex flex-col-reverse gap-2 sm:flex-row sm:items-center">
                        <a href="{{ route('dashboard') }}" class="inline-flex items-center justify-center rounded-lg border border-gray-200 bg-white px-4 py-2 text-sm font-semibold text-gray-700 transition hover:bg-gray-50">
                            Back
                        </a>
                        <button type="button" id="nextServiceBtn" class="inline-flex items-center justify-center gap-2 rounded-lg bg-orange-500 px-5 py-2 text-sm font-semibold text-white shadow-sm shadow-orange-200 transition hover:bg-orange-600">
                            Next: Service
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
                            </svg>
                        </button>
                    </div>
                    @endif
                </div>

                @if($pets->isEmpty())
                <div class="rounded-xl border border-dashed border-orange-300 bg-orange-50 px-5 py-8 text-center">
                    <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-white text-orange-500 shadow-sm">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                    </div>
                    <h4 class="mt-4 text-base font-semibold text-gray-950">No pet profile yet</h4>
                    <p class="mt-2 text-sm text-gray-600">Add a pet in Settings before booking an appointment.</p>
                    <a href="{{ route('settings') }}#pet-profile" class="mt-5 inline-flex items-center justify-center rounded-lg bg-orange-500 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-orange-600">
                        Go to Settings
                    </a>
                </div>
                @else
                <div class="space-y-2">
                    @foreach($pets as $pet)
                    @php
                        $isSelected = $selectedPet?->id === $pet->id;
                        $ageLabel = $pet->date_of_birth ? $pet->date_of_birth->age.' '.\Illuminate\Support\Str::plural('year', $pet->date_of_birth->age) : 'Age not set';
                        $petMeta = collect([$pet->type, $pet->gender, $ageLabel])->filter()->implode(' • ');
                    @endphp
                    <label
                        class="group flex cursor-pointer items-center gap-3 rounded-xl border bg-white p-3 transition hover:border-orange-300 hover:bg-orange-50/40 {{ $isSelected ? 'border-orange-500 bg-orange-50/70 shadow-sm shadow-orange-100' : 'border-gray-200' }}"
                        data-pet-card
                    >
                        <input
                            type="radio"
                            name="pet_id"
                            value="{{ $pet->id }}"
                            class="h-5 w-5 border-gray-300 text-orange-500 focus:ring-orange-500"
                            data-pet-radio
                            data-pet-name="{{ $pet->name }}"
                            data-pet-meta="{{ $petMeta }}"
                            data-pet-type="{{ $pet->type }}"
                            data-pet-breed="{{ $pet->breed ?: 'Breed not set' }}"
                            @checked($isSelected)
                            required
                        >

                        <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-full bg-orange-100 text-base font-bold text-orange-600">
                            {{ strtoupper(substr($pet->name, 0, 1)) }}
                        </div>

                        <div class="min-w-0 flex-1">
                            <div class="flex flex-wrap items-center gap-2">
                                <h4 class="truncate text-base font-semibold text-gray-950">{{ $pet->name }}</h4>
                                <span class="hidden rounded-md bg-orange-500 px-2 py-1 text-xs font-semibold text-white" data-selected-badge>Selected</span>
                            </div>
                            <p class="mt-1 text-sm text-gray-600">{{ $petMeta ?: 'Pet details not set' }}</p>
                            <p class="mt-1 text-xs text-gray-500">{{ $pet->breed ?: 'Breed not set' }}</p>
                        </div>

                        <svg class="h-5 w-5 shrink-0 text-gray-400 transition group-hover:text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </label>
                    @endforeach
                </div>

                <a href="{{ route('settings') }}#pet-profile" class="mt-3 flex items-center justify-center gap-2 rounded-xl border border-dashed border-orange-300 px-4 py-2.5 text-sm font-semibold text-orange-600 transition hover:border-orange-400 hover:bg-orange-50">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Add New Pet
                </a>

                <div class="mt-3 rounded-lg bg-blue-50 px-4 py-2.5 text-sm text-blue-700">
                    Can't find your pet? Add a new pet in Settings so you can book an appointment.
                </div>
                @endif
            </section>

            @if($pets->isNotEmpty())
            <section id="serviceSelectionSection" class="hidden rounded-xl border border-gray-200 bg-white p-4 shadow-sm">
                <div class="mb-4 flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-950">Select a Service</h3>
                        <p class="mt-1 text-sm text-gray-500">Please select one service category to see available options.</p>
                    </div>

                    <div class="flex flex-col-reverse gap-2 sm:flex-row sm:items-center">
                        <button type="button" id="backToPetsBtn" class="inline-flex items-center justify-center rounded-lg border border-orange-200 bg-white px-4 py-2 text-sm font-semibold text-orange-600 transition hover:bg-orange-50">
                            Change Pet
                        </button>
                        <button type="button" id="nextScheduleBtn" class="inline-flex items-center justify-center gap-2 rounded-lg bg-orange-500 px-5 py-2 text-sm font-semibold text-white shadow-sm shadow-orange-200 transition hover:bg-orange-600">
                            Next: Schedule
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
                            </svg>
                        </button>
                    </div>
                </div>

                <div class="mb-4 hidden flex-col gap-3 border-b border-gray-100 pb-4 sm:flex-row sm:items-center sm:justify-between">
                    <div class="flex items-center gap-3">
                        <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-full bg-orange-100 text-base font-bold text-orange-600" id="selectedPetInitial">
                            {{ $selectedPet ? strtoupper(substr($selectedPet->name, 0, 1)) : '-' }}
                        </div>
                        <div class="min-w-0">
                            <p class="text-xs font-medium text-gray-500">Selected Pet</p>
                            <h3 class="truncate text-lg font-semibold text-gray-950" id="selectedPetName">{{ $selectedPet?->name ?? '-' }}</h3>
                            <p class="truncate text-sm text-gray-600" id="selectedPetDetails">
                                @if($selectedPet)
                                    {{ collect([$selectedPet->type, $selectedPet->gender, $selectedPet->breed])->filter()->implode(' • ') ?: 'Pet details not set' }}
                                @else
                                    -
                                @endif
                            </p>
                        </div>
                    </div>

                    <div class="flex flex-col-reverse gap-2 sm:flex-row sm:items-center">
                        <button type="button" id="backToPetsBtnHidden" class="inline-flex items-center justify-center rounded-lg border border-orange-200 bg-white px-4 py-2 text-sm font-semibold text-orange-600 transition hover:bg-orange-50">
                            Change Pet
                        </button>
                        <button type="button" id="nextScheduleBtnHidden" class="inline-flex items-center justify-center gap-2 rounded-lg bg-orange-500 px-5 py-2 text-sm font-semibold text-white shadow-sm shadow-orange-200 transition hover:bg-orange-600">
                            Next: Schedule
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
                            </svg>
                        </button>
                    </div>
                </div>

                <div class="hidden">
                    <h3 class="text-lg font-semibold text-gray-950">Select a Service</h3>
                    <p class="mt-1 text-sm text-gray-500">Choose the service you need for your pet.</p>
                </div>

                @if($appointmentServiceCatalog->isEmpty())
                <div class="rounded-xl border border-dashed border-gray-300 bg-gray-50 px-4 py-6 text-center text-sm text-gray-600">
                    No services are available right now. You can continue by adding a reason for visit on the schedule step.
                </div>
                @else
                <div class="grid gap-4 xl:grid-cols-[minmax(0,1fr)_minmax(320px,0.8fr)]">
                    <div class="grid gap-3 md:grid-cols-2">
                    @foreach($appointmentServiceCatalog as $service)
                    @php
                        $serviceId = (int) $service['service_id'];
                        $isServiceSelected = $selectedService?->id === $serviceId;
                    @endphp
                    <label
                        class="group flex min-h-28 cursor-pointer items-start gap-3 rounded-xl border bg-white p-4 transition hover:border-orange-300 hover:bg-orange-50/40 {{ $isServiceSelected ? 'border-orange-500 bg-orange-50/70 shadow-sm shadow-orange-100' : 'border-gray-200' }}"
                        data-service-card
                    >
                        <input
                            type="radio"
                            name="service_id"
                            value="{{ $serviceId }}"
                            class="mt-1 h-5 w-5 shrink-0 border-gray-300 text-orange-500 focus:ring-orange-500"
                            data-service-radio
                            data-service-key="{{ $service['key'] }}"
                            data-service-name="{{ $service['title'] }}"
                            data-service-description="{{ $service['description'] }}"
                            data-price="{{ $service['booking_amount'] }}"
                            data-price-label="{{ $service['booking_price'] }}"
                            data-is-grooming="{{ $service['key'] === 'grooming' ? '1' : '0' }}"
                            @checked($isServiceSelected)
                        >

                        <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-full bg-orange-50 text-orange-600">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6l4 2m5-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>

                        <div class="min-w-0 flex-1">
                            <h4 class="break-words text-sm font-semibold leading-5 text-gray-950">{{ $service['title'] }}</h4>
                            <p class="mt-1 text-base font-bold leading-5 text-gray-950">{{ $service['booking_price'] }}</p>
                            <p class="mt-2 line-clamp-2 text-xs leading-5 text-gray-600">{{ $service['description'] }}</p>
                        </div>
                    </label>
                    @endforeach
                </div>

                    <div class="rounded-xl border border-gray-200 bg-gray-50/70 p-4" id="serviceDetailPanel">
                        <div class="rounded-xl border border-dashed border-gray-300 bg-white px-4 py-8 text-center text-sm text-gray-500">
                            Select a service to view details and prices.
                        </div>
                    </div>
                </div>
                @endif
            </section>

            <section id="scheduleSection" class="hidden rounded-xl border border-gray-200 bg-white p-4 shadow-sm">
                <div class="mb-4 flex flex-col gap-3 border-b border-gray-100 pb-4 sm:flex-row sm:items-center sm:justify-between">
                    <div class="grid gap-3 sm:grid-cols-2">
                        <div class="flex min-w-0 items-center gap-3">
                            <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-full bg-orange-100 text-base font-bold text-orange-600" id="schedulePetInitial">
                                {{ $selectedPet ? strtoupper(substr($selectedPet->name, 0, 1)) : '-' }}
                            </div>
                            <div class="min-w-0">
                                <p class="text-xs font-medium text-gray-500">Pet</p>
                                <h3 class="truncate text-base font-semibold text-gray-950" id="schedulePetName">{{ $selectedPet?->name ?? '-' }}</h3>
                                <p class="truncate text-xs text-gray-600" id="schedulePetDetails">
                                    @if($selectedPet)
                                        {{ collect([$selectedPet->type, $selectedPet->gender, $selectedPet->breed])->filter()->implode(' • ') ?: 'Pet details not set' }}
                                    @else
                                        -
                                    @endif
                                </p>
                            </div>
                        </div>

                        <div class="flex min-w-0 items-center gap-3 rounded-xl border border-gray-100 bg-gray-50 px-3 py-2">
                            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-orange-50 text-orange-600">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6l4 2m5-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div class="min-w-0">
                                <p class="text-xs font-medium text-gray-500">Service</p>
                                <h3 class="truncate text-sm font-semibold text-gray-950" id="scheduleServiceName">{{ $selectedService?->name ?? '-' }}</h3>
                                <p class="text-xs font-bold text-gray-900" id="scheduleServicePrice">₱{{ number_format($serviceAmount, 2) }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="flex flex-col-reverse gap-2 sm:flex-row sm:items-center">
                        <button type="button" id="backToServicesBtn" class="inline-flex items-center justify-center rounded-lg border border-orange-200 bg-white px-4 py-2 text-sm font-semibold text-orange-600 transition hover:bg-orange-50">
                            Change Service
                        </button>
                        <button type="button" id="nextPaymentBtn" class="inline-flex items-center justify-center gap-2 rounded-lg bg-orange-500 px-5 py-2 text-sm font-semibold text-white shadow-sm shadow-orange-200 transition hover:bg-orange-600 disabled:cursor-not-allowed disabled:opacity-50" disabled>
                            Next: Payment
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
                            </svg>
                        </button>
                    </div>
                </div>

                <div class="mb-5 grid gap-4 lg:grid-cols-[minmax(0,0.95fr)_minmax(0,1.05fr)]">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-950">Select Date</h3>
                        <p class="mt-1 text-sm text-gray-500">Choose an available date.</p>
                        <div class="mt-3 rounded-xl border border-gray-200 bg-white p-4">
                            <label for="appointmentDate" class="mb-2 block text-sm font-medium text-gray-700">Appointment Date *</label>
                            <input type="date" name="appointment_date" id="appointmentDate" value="{{ old('appointment_date') }}" min="{{ date('Y-m-d') }}" required class="w-full rounded-xl border border-gray-300 px-4 py-3 text-sm text-gray-700 focus:border-orange-500 focus:ring-2 focus:ring-orange-500">
                        </div>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-gray-950">Available Time Slots</h3>
                        <p class="mt-1 text-sm text-gray-500">All times are in Philippine Standard Time.</p>
                        <select name="appointment_time" id="timeSlot" required class="sr-only">
                            <option value="">Select date first</option>
                        </select>
                        <div id="timeSlotButtons" class="mt-3 grid gap-2">
                            <div class="rounded-xl border border-dashed border-gray-300 px-4 py-6 text-center text-sm text-gray-500">
                                Select a date to view available times.
                            </div>
                        </div>
                        <p id="scheduleRequirementMessage" class="mt-3 text-xs font-medium text-orange-600">Select an appointment date and time slot to continue.</p>
                        <p class="mt-3 text-xs text-blue-600">Time slots are limited. Please choose as early as possible.</p>
                    </div>
                </div>

                <div class="mb-5" id="reasonWrapper">
                    <label for="reason" id="reasonLabel" class="mb-2 block text-sm font-medium text-gray-700">Reason for Visit *</label>
                    <textarea name="reason" id="reason" rows="3" placeholder="Please specify your reason for visit" class="w-full rounded-xl border border-gray-300 px-4 py-3 text-sm text-gray-700 focus:border-orange-500 focus:ring-2 focus:ring-orange-500">{{ old('reason') }}</textarea>
                </div>

                <div class="flex flex-col gap-3 border-t border-gray-100 pt-5 sm:flex-row sm:items-center sm:justify-between">
                    <button type="button" id="backToServicesFooterBtn" class="inline-flex items-center justify-center rounded-lg border border-gray-200 px-5 py-2.5 text-sm font-semibold text-gray-700 transition hover:bg-gray-50">
                        Back
                    </button>
                </div>
            </section>

            <section id="paymentSection" class="hidden rounded-xl border border-gray-200 bg-white p-4 shadow-sm">
                <div class="mb-4 flex flex-col gap-3 border-b border-gray-100 pb-4 sm:flex-row sm:items-start sm:justify-between">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-950">Payment</h3>
                        <p class="mt-1 text-sm text-gray-500">Please pay the 20% reservation fee to confirm your appointment.</p>
                        <p class="mt-1 text-sm text-gray-500">Your appointment is not confirmed until payment is received.</p>
                    </div>

                    <div class="flex flex-col-reverse gap-2 sm:flex-row sm:items-center">
                        <button type="button" id="backToScheduleBtn" class="inline-flex items-center justify-center rounded-lg border border-orange-200 bg-white px-4 py-2 text-sm font-semibold text-orange-600 transition hover:bg-orange-50">
                            Back
                        </button>
                        <button type="button" id="nextConfirmBtn" class="inline-flex items-center justify-center gap-2 rounded-lg bg-orange-500 px-5 py-2 text-sm font-semibold text-white shadow-sm shadow-orange-200 transition hover:bg-orange-600 disabled:cursor-not-allowed disabled:opacity-50" disabled>
                            Next: Confirm
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
                            </svg>
                        </button>
                    </div>
                </div>

                <div class="grid gap-5 lg:grid-cols-2">
                    <div>
                        <div class="mb-4 flex items-center justify-between gap-3">
                            <h4 class="text-base font-semibold text-gray-950">Pay with GCash</h4>
                            <span class="text-lg font-bold text-blue-600">GCash</span>
                        </div>

                        <div class="grid gap-4 sm:grid-cols-[150px_minmax(0,1fr)]">
                            <div class="flex aspect-square items-center justify-center rounded-xl border border-gray-200 bg-white p-3">
                                @if(file_exists(public_path('images/gcash-qr.png')))
                                <img src="{{ asset('images/gcash-qr.png') }}" alt="GCash payment QR code" class="h-full w-full object-contain">
                                @else
                                <div class="flex h-full w-full items-center justify-center rounded-lg border border-dashed border-gray-300 px-3 text-center text-xs font-medium text-gray-500">
                                    GCash QR image not found
                                </div>
                                @endif
                            </div>

                            <div class="flex flex-col justify-center">
                                <p class="text-sm text-gray-600">Scan the QR code using your GCash app or send payment to:</p>
                                <p class="mt-3 text-xl font-bold text-blue-600">0969 335 4923</p>
                                <p class="mt-1 text-sm font-semibold text-blue-600">Rushell De Leon</p>
                            </div>
                        </div>

                        <div class="mt-4 rounded-lg bg-blue-50 px-4 py-3 text-sm text-blue-700">
                            Please make sure to send the exact amount to avoid payment issues.
                        </div>
                    </div>

                    <div>
                        <h4 class="text-base font-semibold text-gray-950">Send Proof of Payment</h4>
                        <p class="mt-1 text-sm text-gray-500">After payment, send your proof to confirm your appointment.</p>

                        <label for="paymentProof" class="mt-4 flex cursor-pointer flex-col items-center justify-center rounded-xl border border-dashed border-orange-300 bg-orange-50/40 px-4 py-8 text-center transition hover:bg-orange-50">
                            <svg class="h-8 w-8 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 16V4m0 0l-4 4m4-4l4 4M4 20h16"></path>
                            </svg>
                            <span class="mt-3 text-sm font-semibold text-gray-950">Upload Screenshot</span>
                            <span class="mt-1 text-xs text-gray-500" id="paymentProofLabel">JPG, PNG or PDF (Max. 5MB)</span>
                        </label>
                        <input type="file" id="paymentProof" name="payment_proof" accept=".jpg,.jpeg,.png,.pdf" class="sr-only">

                        <div class="my-5 flex items-center gap-3">
                            <div class="h-px flex-1 bg-gray-200"></div>
                            <span class="text-xs font-semibold text-gray-500">OR</span>
                            <div class="h-px flex-1 bg-gray-200"></div>
                        </div>

                        <label for="paymentReference" class="block text-sm font-semibold text-gray-950">Enter Reference Number</label>
                        <p class="mt-1 text-xs text-gray-500">Enter the 12-digit GCash reference number.</p>
                        <input type="text" id="paymentReference" name="payment_reference" value="{{ old('payment_reference') }}" inputmode="numeric" pattern="[0-9]{12}" maxlength="12" placeholder="e.g. 612345678901" class="mt-2 w-full rounded-xl border border-gray-300 px-4 py-3 text-sm text-gray-700 focus:border-orange-500 focus:ring-2 focus:ring-orange-500">
                        <p id="paymentRequirementMessage" class="mt-2 text-xs font-medium text-orange-600">Upload a payment image or enter a reference number to continue.</p>
                    </div>
                </div>

                <div class="mt-5 flex flex-col gap-3 rounded-xl border border-orange-100 bg-orange-50 px-4 py-4 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h4 class="text-sm font-semibold text-orange-700">Reservation Fee (20%)</h4>
                        <p class="mt-1 text-xs text-gray-600">A 20% reservation fee is required to secure your slot.</p>
                        <p class="mt-1 text-xs text-gray-600">The remaining balance will be settled on the day of your appointment.</p>
                    </div>
                    <div class="text-left sm:text-right">
                        <p class="text-xs font-semibold text-gray-600">Total to Pay</p>
                        <p class="text-2xl font-bold text-orange-600" id="paymentTotal">₱{{ number_format($serviceAmount * 0.2, 2) }}</p>
                    </div>
                </div>
            </section>

            <section id="confirmSection" class="hidden rounded-xl border border-gray-200 bg-white p-4 shadow-sm">
                <div class="mb-4 flex flex-col gap-3 border-b border-gray-100 pb-4 sm:flex-row sm:items-start sm:justify-between">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-950">Review Your Appointment</h3>
                        <p class="mt-1 text-sm text-gray-500">Please confirm that all details are correct.</p>
                    </div>

                    <div class="flex flex-col-reverse gap-2 sm:flex-row sm:items-center">
                        <button type="button" id="backToPaymentBtn" class="inline-flex items-center justify-center rounded-lg border border-orange-200 bg-white px-4 py-2 text-sm font-semibold text-orange-600 transition hover:bg-orange-50">
                            Back
                        </button>
                        <button type="button" id="nextNotesBtn" class="inline-flex items-center justify-center gap-2 rounded-lg bg-orange-500 px-5 py-2 text-sm font-semibold text-white shadow-sm shadow-orange-200 transition hover:bg-orange-600 disabled:cursor-not-allowed disabled:opacity-50" disabled>
                            Next: Notes
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
                            </svg>
                        </button>
                    </div>
                </div>

                <div class="divide-y divide-gray-100">
                    <div class="flex items-center gap-4 py-4">
                        <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-full bg-orange-100 text-base font-bold text-orange-600" id="confirmPetInitial">
                            {{ $selectedPet ? strtoupper(substr($selectedPet->name, 0, 1)) : '-' }}
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="text-xs font-semibold text-gray-500">Pet Information</p>
                            <h4 class="truncate text-base font-semibold text-gray-950" id="confirmPetName">{{ $selectedPet?->name ?? '-' }}</h4>
                            <p class="truncate text-sm text-gray-600" id="confirmPetDetails">
                                @if($selectedPet)
                                    {{ collect([$selectedPet->type, $selectedPet->gender, $selectedPet->breed])->filter()->implode(' • ') ?: 'Pet details not set' }}
                                @else
                                    -
                                @endif
                            </p>
                        </div>
                        <button type="button" data-edit-step="1" class="inline-flex items-center justify-center rounded-lg border border-orange-200 px-3 py-1.5 text-xs font-semibold text-orange-600 transition hover:bg-orange-50">
                            Edit
                        </button>
                    </div>

                    <div class="flex items-center gap-4 py-4">
                        <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-full bg-orange-50 text-orange-600">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6l4 2m5-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="text-xs font-semibold text-gray-500">Service</p>
                            <h4 class="truncate text-base font-semibold text-gray-950" id="confirmServiceName">{{ $selectedService?->name ?? '-' }}</h4>
                            <p class="text-sm font-semibold text-gray-700" id="confirmServicePrice">₱{{ number_format($serviceAmount, 2) }}</p>
                        </div>
                        <button type="button" data-edit-step="2" class="inline-flex items-center justify-center rounded-lg border border-orange-200 px-3 py-1.5 text-xs font-semibold text-orange-600 transition hover:bg-orange-50">
                            Edit
                        </button>
                    </div>

                    <div class="flex items-center gap-4 py-4">
                        <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-full bg-orange-50 text-orange-600">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3M5 11h14M7 21h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="text-xs font-semibold text-gray-500">Date & Time</p>
                            <h4 class="truncate text-base font-semibold text-gray-950" id="confirmDateTime">-</h4>
                        </div>
                        <button type="button" data-edit-step="3" class="inline-flex items-center justify-center rounded-lg border border-orange-200 px-3 py-1.5 text-xs font-semibold text-orange-600 transition hover:bg-orange-50">
                            Edit
                        </button>
                    </div>

                    <div class="flex items-center gap-4 py-4">
                        <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-full bg-orange-50 text-orange-600">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a2 2 0 01-2.828 0l-4.243-4.243a8 8 0 1111.314 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="text-xs font-semibold text-gray-500">Location</p>
                            <h4 class="truncate text-base font-semibold text-gray-950">713 Earnshaw St, Sampaloc</h4>
                            <p class="truncate text-sm text-gray-600">Manila, 1008 Metro Manila</p>
                        </div>
                        <button type="button" data-edit-step="3" class="inline-flex items-center justify-center rounded-lg border border-orange-200 px-3 py-1.5 text-xs font-semibold text-orange-600 transition hover:bg-orange-50">
                            Edit
                        </button>
                    </div>

                    <div class="flex items-center gap-4 py-4">
                        <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-full bg-orange-50 text-orange-600">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1M6 19h12a2 2 0 002-2V8a2 2 0 00-2-2H6a2 2 0 00-2 2v9a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="text-xs font-semibold text-gray-500">Payment Method</p>
                            <h4 class="truncate text-base font-semibold text-gray-950">GCash</h4>
                            <p class="truncate text-sm text-gray-600" id="confirmPaymentReference">Reference No.: Not provided</p>
                        </div>
                        <button type="button" data-edit-step="4" class="inline-flex items-center justify-center rounded-lg border border-orange-200 px-3 py-1.5 text-xs font-semibold text-orange-600 transition hover:bg-orange-50">
                            Edit
                        </button>
                    </div>
                </div>

                <div class="mt-4 rounded-lg bg-blue-50 px-4 py-3 text-sm text-blue-700">
                    Once you confirm your appointment, it will be sent to the clinic for approval. You will receive a notification once your appointment is confirmed.
                </div>

                <label class="mt-4 flex items-start gap-3 rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 text-sm text-gray-700">
                    <input type="checkbox" id="termsAgreement" name="terms_agreement" value="1" required class="mt-1 h-4 w-4 rounded border-gray-300 text-orange-500 focus:ring-orange-500">
                    <span>By confirming, you agree to our <span class="font-semibold text-orange-600">Terms and Conditions</span>.</span>
                </label>
            </section>

            <section id="notesSection" class="hidden rounded-xl border border-gray-200 bg-white p-4 shadow-sm">
                <div class="mb-4 flex flex-col gap-3 border-b border-gray-100 pb-4 sm:flex-row sm:items-start sm:justify-between">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-950">Additional Notes (Optional)</h3>
                        <p class="mt-1 text-sm text-gray-500">Let us know if there is anything specific we should know about your pet.</p>
                    </div>

                    <div class="flex flex-col-reverse gap-2 sm:flex-row sm:items-center">
                        <button type="button" id="backToConfirmBtn" class="inline-flex items-center justify-center rounded-lg border border-orange-200 bg-white px-4 py-2 text-sm font-semibold text-orange-600 transition hover:bg-orange-50">
                            Back
                        </button>
                        <button type="submit" id="appointmentSubmitBtn" class="inline-flex items-center justify-center gap-2 rounded-lg bg-orange-500 px-5 py-2 text-sm font-semibold text-white shadow-sm shadow-orange-200 transition hover:bg-orange-600">
                            Confirm Booking
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                        </button>
                    </div>
                </div>

                <div>
                    <textarea name="appointment_notes" id="appointmentNotes" maxlength="500" rows="7" placeholder="e.g. My pet is a bit anxious around other animals.&#10;&#10;Please handle with care." class="w-full rounded-xl border border-gray-300 px-4 py-4 text-sm text-gray-700 focus:border-orange-500 focus:ring-2 focus:ring-orange-500">{{ old('appointment_notes') }}</textarea>
                    <p class="mt-2 text-right text-xs text-gray-500"><span id="notesCounter">0</span> / 500</p>
                </div>

                <div class="mt-4 rounded-lg bg-blue-50 px-4 py-3 text-sm text-blue-700">
                    <p class="font-semibold">Why notes are helpful?</p>
                    <p class="mt-1">Your notes help our veterinarians and staff prepare for your pet's visit and provide the best possible care.</p>
                </div>

                <div class="mt-4 rounded-lg border border-green-100 bg-green-50 px-4 py-3 text-sm text-green-700">
                    <p class="font-semibold">Almost done!</p>
                    <p class="mt-1">Click Confirm Booking to finalize your appointment. You will receive a confirmation once it is approved.</p>
                </div>
            </section>
            @endif
        </div>

        <aside class="space-y-5 lg:sticky lg:top-6 lg:self-start">
            <section class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm md:p-5">
                <div class="mb-4 flex items-center justify-between gap-3">
                    <h3 class="font-semibold text-gray-950">Appointment Summary</h3>
                    @if($pets->isNotEmpty())
                    <button type="button" id="editPetBtn" class="rounded-lg border border-orange-200 px-3 py-1.5 text-xs font-semibold text-orange-600 transition hover:bg-orange-50">
                        Edit
                    </button>
                    @endif
                </div>

                <div class="flex gap-3">
                    <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-full bg-orange-100 text-base font-bold text-orange-600" id="summaryPetInitial">
                        {{ $selectedPet ? strtoupper(substr($selectedPet->name, 0, 1)) : '-' }}
                    </div>
                    <dl class="min-w-0 flex-1 space-y-3 text-sm">
                        <div class="grid grid-cols-[72px_minmax(0,1fr)] gap-3">
                            <dt class="text-gray-500">Pet</dt>
                            <dd class="truncate font-medium text-gray-900" id="summaryPetName">{{ $selectedPet?->name ?? '-' }}</dd>
                        </div>
                        <div class="grid grid-cols-[72px_minmax(0,1fr)] gap-3">
                            <dt class="text-gray-500">Details</dt>
                            <dd class="truncate text-gray-700" id="summaryPetMeta">
                                @if($selectedPet)
                                    {{ collect([$selectedPet->type, $selectedPet->breed])->filter()->implode(' - ') ?: '-' }}
                                @else
                                    -
                                @endif
                            </dd>
                        </div>
                        <div class="grid grid-cols-[72px_minmax(0,1fr)] gap-3">
                            <dt class="text-gray-500">Service</dt>
                            <dd class="truncate text-gray-700" id="summaryServiceName">{{ $selectedService?->name ?? '-' }}</dd>
                        </div>
                        <div class="grid grid-cols-[72px_minmax(0,1fr)] gap-3">
                            <dt class="text-gray-500">Date & Time</dt>
                            <dd class="truncate text-gray-700" id="summaryDateTime">-</dd>
                        </div>
                        <div class="grid grid-cols-[72px_minmax(0,1fr)] gap-3">
                            <dt class="text-gray-500">Location</dt>
                            <dd class="truncate text-gray-700">713 Earnshaw St, Sampaloc, Manila, 1008 Metro Manila</dd>
                        </div>
                    </dl>
                </div>
            </section>

            <section id="paymentSummaryCard" class="hidden rounded-xl border border-gray-200 bg-white p-4 shadow-sm md:p-5">
                <h3 class="mb-4 font-semibold text-gray-950">Payment Summary</h3>
                <dl class="space-y-3 text-sm">
                    <div class="flex items-center justify-between gap-3">
                        <dt class="text-gray-600">Service Amount</dt>
                        <dd class="font-medium text-gray-900" id="summaryServiceAmount">₱{{ number_format($serviceAmount, 2) }}</dd>
                    </div>
                    <div class="flex items-center justify-between gap-3">
                        <dt class="font-medium text-green-600">Reservation Fee (20%)</dt>
                        <dd class="font-semibold text-green-600" id="summaryReservationFee">₱{{ number_format($serviceAmount * 0.2, 2) }}</dd>
                    </div>
                </dl>
                <div class="mt-5 flex items-center justify-between gap-3 border-t border-gray-100 pt-4">
                    <p class="text-sm font-semibold text-gray-700">Total to Pay</p>
                    <p class="text-xl font-bold text-orange-600" id="summaryTotal">₱{{ number_format($serviceAmount * 0.2, 2) }}</p>
                </div>
                <div class="mt-4 rounded-lg bg-blue-50 px-4 py-3 text-sm text-blue-700">
                    A 20% reservation fee is required to confirm your appointment.
                </div>
            </section>
        </aside>
    </form>
    @endif
</div>
@endsection

@unless($submittedAppointment)
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const petRadios = Array.from(document.querySelectorAll('[data-pet-radio]'));
    const petCards = Array.from(document.querySelectorAll('[data-pet-card]'));
    const serviceRadios = Array.from(document.querySelectorAll('[data-service-radio]'));
    const serviceCards = Array.from(document.querySelectorAll('[data-service-card]'));
    const appointmentServiceCatalog = @json($appointmentServiceCatalog);
    const appointmentServicesByKey = appointmentServiceCatalog.reduce(function (items, service) {
        items[service.key] = service;

        return items;
    }, {});
    const petSelectionSection = document.getElementById('petSelectionSection');
    const serviceSelectionSection = document.getElementById('serviceSelectionSection');
    const scheduleSection = document.getElementById('scheduleSection');
    const paymentSection = document.getElementById('paymentSection');
    const confirmSection = document.getElementById('confirmSection');
    const notesSection = document.getElementById('notesSection');
    const nextServiceBtn = document.getElementById('nextServiceBtn');
    const nextScheduleBtn = document.getElementById('nextScheduleBtn');
    const nextPaymentBtn = document.getElementById('nextPaymentBtn');
    const nextConfirmBtn = document.getElementById('nextConfirmBtn');
    const nextNotesBtn = document.getElementById('nextNotesBtn');
    const backToPetsBtn = document.getElementById('backToPetsBtn');
    const backToServicesBtn = document.getElementById('backToServicesBtn');
    const backToServicesFooterBtn = document.getElementById('backToServicesFooterBtn');
    const backToScheduleBtn = document.getElementById('backToScheduleBtn');
    const backToPaymentBtn = document.getElementById('backToPaymentBtn');
    const backToConfirmBtn = document.getElementById('backToConfirmBtn');
    const editPetBtn = document.getElementById('editPetBtn');
    const dateInput = document.getElementById('appointmentDate');
    const timeSlotSelect = document.getElementById('timeSlot');
    const timeSlotButtons = document.getElementById('timeSlotButtons');
    const scheduleRequirementMessage = document.getElementById('scheduleRequirementMessage');
    const reasonWrapper = document.getElementById('reasonWrapper');
    const serviceDetailPanel = document.getElementById('serviceDetailPanel');
    const reasonLabel = document.getElementById('reasonLabel');
    const reasonInput = document.getElementById('reason');
    const paymentProof = document.getElementById('paymentProof');
    const paymentProofLabel = document.getElementById('paymentProofLabel');
    const paymentReference = document.getElementById('paymentReference');
    const paymentRequirementMessage = document.getElementById('paymentRequirementMessage');
    const termsAgreement = document.getElementById('termsAgreement');
    const appointmentNotes = document.getElementById('appointmentNotes');
    const notesCounter = document.getElementById('notesCounter');
    const appointmentForm = document.getElementById('appointmentForm');
    const appointmentSubmitBtn = document.getElementById('appointmentSubmitBtn');
    const oldTime = @json(old('appointment_time'));
    const shouldOpenPaymentStep = @json($errors->has('payment_proof') || $errors->has('payment_reference'));
    const shouldOpenConfirmStep = @json($errors->has('terms_agreement'));
    const shouldOpenNotesStep = @json($errors->has('appointment_notes'));

    const stepBadge = document.getElementById('stepBadge');
    const stepTitle = document.getElementById('stepTitle');
    const stepDescription = document.getElementById('stepDescription');
    const selectedPetInitial = document.getElementById('selectedPetInitial');
    const selectedPetName = document.getElementById('selectedPetName');
    const selectedPetDetails = document.getElementById('selectedPetDetails');
    const schedulePetInitial = document.getElementById('schedulePetInitial');
    const schedulePetName = document.getElementById('schedulePetName');
    const schedulePetDetails = document.getElementById('schedulePetDetails');
    const scheduleServiceName = document.getElementById('scheduleServiceName');
    const scheduleServicePrice = document.getElementById('scheduleServicePrice');
    const summaryPetInitial = document.getElementById('summaryPetInitial');
    const summaryPetName = document.getElementById('summaryPetName');
    const summaryPetMeta = document.getElementById('summaryPetMeta');
    const summaryServiceName = document.getElementById('summaryServiceName');
    const summaryDateTime = document.getElementById('summaryDateTime');
    const summaryServiceAmount = document.getElementById('summaryServiceAmount');
    const summaryReservationFee = document.getElementById('summaryReservationFee');
    const summaryTotal = document.getElementById('summaryTotal');
    const paymentSummaryCard = document.getElementById('paymentSummaryCard');
    const paymentTotal = document.getElementById('paymentTotal');
    const confirmPetInitial = document.getElementById('confirmPetInitial');
    const confirmPetName = document.getElementById('confirmPetName');
    const confirmPetDetails = document.getElementById('confirmPetDetails');
    const confirmServiceName = document.getElementById('confirmServiceName');
    const confirmServicePrice = document.getElementById('confirmServicePrice');
    const confirmDateTime = document.getElementById('confirmDateTime');
    const confirmPaymentReference = document.getElementById('confirmPaymentReference');

    const stepContent = {
        1: {
            badge: 'Step 1',
            title: 'Select Pet',
            description: 'Choose which pet you want to book an appointment for.',
        },
        2: {
            badge: 'Step 2',
            title: 'Choose Service',
            description: 'Select the service you need for your pet.',
        },
        3: {
            badge: 'Step 3',
            title: 'Schedule (Date & Time)',
            description: 'Choose your preferred date and available time slot.',
        },
        4: {
            badge: 'Step 4',
            title: 'Payment',
            description: 'Please pay the 20% reservation fee to confirm your appointment.',
        },
        5: {
            badge: 'Step 5',
            title: 'Confirm Appointment',
            description: 'Please review your appointment details.',
        },
        6: {
            badge: 'Step 6',
            title: 'Notes (Optional)',
            description: 'Add any additional notes or concerns about your pet.',
        },
    };

    function formatPeso(amount) {
        return '₱' + Number(amount || 0).toLocaleString('en-PH', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2,
        });
    }

    function escapeHtml(value) {
        return String(value || '')
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    function priceRow(label, description, price) {
        return [
            '<div class="rounded-lg border border-gray-200 bg-white p-3">',
            '<div class="flex items-start justify-between gap-3">',
            '<div>',
            '<p class="text-sm font-semibold text-gray-950">' + escapeHtml(label) + '</p>',
            description ? '<p class="mt-1 text-xs leading-5 text-gray-600">' + escapeHtml(description) + '</p>' : '',
            '</div>',
            price ? '<p class="shrink-0 text-sm font-bold text-orange-600">' + escapeHtml(price) + '</p>' : '',
            '</div>',
            '</div>',
        ].join('');
    }

    function dogGroomingRow(item) {
        return [
            '<div class="rounded-lg border border-gray-200 bg-white p-3">',
            '<div class="flex items-start justify-between gap-3">',
            '<div>',
            '<p class="text-sm font-semibold text-gray-950">' + escapeHtml(item.size + ' (' + item.weight + ')') + '</p>',
            '<p class="mt-1 text-xs leading-5 text-gray-600">' + escapeHtml(item.breeds) + '</p>',
            '</div>',
            '<div class="shrink-0 space-y-1 text-right text-sm">',
            '<p><span class="text-xs font-medium text-gray-500">Basic</span> <span class="font-bold text-orange-600">' + escapeHtml(item.basic) + '</span></p>',
            '<p><span class="text-xs font-medium text-gray-500">Full</span> <span class="font-bold text-orange-600">' + escapeHtml(item.full) + '</span></p>',
            '</div>',
            '</div>',
            '</div>',
        ].join('');
    }

    function renderServiceDetails(service) {
        if (!serviceDetailPanel || !service) {
            return;
        }

        let priceMarkup = '';

        if (service.layout === 'grooming') {
            const dogRows = (service.pricing || []).map(function (item) {
                return dogGroomingRow(item);
            }).join('');
            const catRows = (service.catPricing || []).map(function (item) {
                return priceRow(item.name, item.details, item.price);
            }).join('');

            priceMarkup = [
                '<div class="mt-4">',
                '<p class="text-xs font-semibold uppercase text-gray-500">Dog Grooming</p>',
                '<div class="mt-2 space-y-2">' + dogRows + '</div>',
                '<p class="mt-4 text-xs font-semibold uppercase text-gray-500">Cat Grooming</p>',
                '<div class="mt-2 space-y-2">' + catRows + '</div>',
                '</div>',
            ].join('');
        } else if (service.pricing) {
            priceMarkup = '<div class="mt-4 space-y-2">' + service.pricing.map(function (item) {
                return priceRow(item.label || item.size, item.weight || item.breeds || '', item.price || item.basic || service.booking_price);
            }).join('') + '</div>';
        } else if (service.options) {
            priceMarkup = '<div class="mt-4 space-y-2">' + service.options.map(function (item) {
                return priceRow(item.name, item.details, item.price || '');
            }).join('') + '</div>';
        } else {
            priceMarkup = '<div class="mt-4">' + priceRow(service.sectionTitle || service.title, service.sectionSubtitle || service.description, service.price || service.booking_price) + '</div>';
        }

        serviceDetailPanel.innerHTML = [
            '<div>',
            '<p class="text-xs font-semibold uppercase text-gray-500">Service Details</p>',
            '<h3 class="mt-1 text-lg font-bold text-gray-950">' + escapeHtml(service.title) + '</h3>',
            '<p class="mt-2 text-sm leading-6 text-gray-600">' + escapeHtml(service.intro || service.description) + '</p>',
            '<div class="mt-4 rounded-lg border border-orange-100 bg-orange-50 px-3 py-2 text-sm font-semibold text-orange-700">Starting at ' + escapeHtml(service.booking_price) + '</div>',
            priceMarkup,
            service.note ? '<div class="mt-4 rounded-lg bg-blue-50 px-3 py-2 text-xs leading-5 text-blue-700">' + escapeHtml(service.note) + '</div>' : '',
            '</div>',
        ].join('');
    }

    function setStep(step) {
        const content = stepContent[step] || stepContent[1];

        if (stepBadge) {
            stepBadge.textContent = content.badge;
        }

        if (stepTitle) {
            stepTitle.textContent = content.title;
        }

        if (stepDescription) {
            stepDescription.textContent = content.description;
        }

        document.querySelectorAll('[data-step-circle]').forEach(function (circle) {
            const circleStep = Number(circle.dataset.stepCircle);
            const isActive = circleStep === step;
            const isComplete = circleStep < step;

            circle.classList.toggle('border-orange-500', isActive);
            circle.classList.toggle('bg-orange-500', isActive);
            circle.classList.toggle('text-white', isActive);
            circle.classList.toggle('shadow-sm', isActive);
            circle.classList.toggle('shadow-orange-200', isActive);
            circle.classList.toggle('border-green-500', isComplete);
            circle.classList.toggle('bg-green-500', isComplete);
            circle.classList.toggle('border-gray-200', ! isActive && ! isComplete);
            circle.classList.toggle('bg-white', ! isActive && ! isComplete);
            circle.classList.toggle('text-gray-700', ! isActive && ! isComplete);
            circle.textContent = isComplete ? '✓' : circleStep;
        });

        document.querySelectorAll('[data-step-label]').forEach(function (label) {
            const labelStep = Number(label.dataset.stepLabel);

            label.classList.toggle('text-orange-600', labelStep === step);
            label.classList.toggle('text-green-600', labelStep < step);
            label.classList.toggle('text-gray-500', labelStep > step);
        });
    }

    function showStep(step) {
        petSelectionSection?.classList.toggle('hidden', step !== 1);
        serviceSelectionSection?.classList.toggle('hidden', step !== 2);
        scheduleSection?.classList.toggle('hidden', step !== 3);
        paymentSection?.classList.toggle('hidden', step !== 4);
        confirmSection?.classList.toggle('hidden', step !== 5);
        notesSection?.classList.toggle('hidden', step !== 6);
        paymentSummaryCard?.classList.toggle('hidden', step < 4);
        setStep(step);
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    function selectedPetRadio() {
        return petRadios.find(function (radio) {
            return radio.checked;
        });
    }

    function selectedServiceRadio() {
        return serviceRadios.find(function (radio) {
            return radio.checked;
        });
    }

    function updatePetSelection() {
        petCards.forEach(function (card) {
            const radio = card.querySelector('[data-pet-radio]');
            const badge = card.querySelector('[data-selected-badge]');
            const selected = radio && radio.checked;

            card.classList.toggle('border-orange-500', selected);
            card.classList.toggle('bg-orange-50/70', selected);
            card.classList.toggle('shadow-sm', selected);
            card.classList.toggle('shadow-orange-100', selected);
            card.classList.toggle('border-gray-200', ! selected);

            if (badge) {
                badge.classList.toggle('hidden', ! selected);
            }
        });

        const selectedPet = selectedPetRadio();

        if (!selectedPet) {
            return;
        }

        const initial = selectedPet.dataset.petName.charAt(0).toUpperCase();
        const summaryDetails = [selectedPet.dataset.petType, selectedPet.dataset.petBreed].filter(Boolean).join(' - ') || '-';

        selectedPetInitial.textContent = initial;
        selectedPetName.textContent = selectedPet.dataset.petName;
        selectedPetDetails.textContent = selectedPet.dataset.petMeta || summaryDetails;
        schedulePetInitial.textContent = initial;
        schedulePetName.textContent = selectedPet.dataset.petName;
        schedulePetDetails.textContent = selectedPet.dataset.petMeta || summaryDetails;
        confirmPetInitial.textContent = initial;
        confirmPetName.textContent = selectedPet.dataset.petName;
        confirmPetDetails.textContent = selectedPet.dataset.petMeta || summaryDetails;
        summaryPetInitial.textContent = initial;
        summaryPetName.textContent = selectedPet.dataset.petName;
        summaryPetMeta.textContent = summaryDetails;
    }

    function updateServiceSelection() {
        serviceCards.forEach(function (card) {
            const radio = card.querySelector('[data-service-radio]');
            const selected = radio && radio.checked;

            card.classList.toggle('border-orange-500', selected);
            card.classList.toggle('bg-orange-50/70', selected);
            card.classList.toggle('shadow-sm', selected);
            card.classList.toggle('shadow-orange-100', selected);
            card.classList.toggle('border-gray-200', ! selected);
        });

        updateServiceSummary();
        toggleReasonField();
        renderServiceDetails(appointmentServicesByKey[selectedServiceRadio()?.dataset.serviceKey]);
    }

    function updateServiceSummary() {
        const selectedService = selectedServiceRadio();
        const selectedServiceName = selectedService ? selectedService.dataset.serviceName : '-';
        const serviceAmount = selectedService ? Number(selectedService.dataset.price || 0) : 0;
        const reservationFee = serviceAmount * 0.2;

        summaryServiceName.textContent = selectedServiceName;
        summaryServiceAmount.textContent = formatPeso(serviceAmount);
        summaryReservationFee.textContent = formatPeso(reservationFee);
        summaryTotal.textContent = formatPeso(reservationFee);
        if (paymentTotal) {
            paymentTotal.textContent = formatPeso(reservationFee);
        }

        if (scheduleServiceName) {
            scheduleServiceName.textContent = selectedServiceName;
        }

        if (scheduleServicePrice) {
            scheduleServicePrice.textContent = formatPeso(serviceAmount);
        }

        if (confirmServiceName) {
            confirmServiceName.textContent = selectedServiceName;
        }

        if (confirmServicePrice) {
            confirmServicePrice.textContent = formatPeso(serviceAmount);
        }
    }

    function updateDateTimeSummary() {
        if (!dateInput || !timeSlotSelect || !summaryDateTime) {
            return;
        }

        const selectedTime = timeSlotSelect.options[timeSlotSelect.selectedIndex];
        const timeLabel = selectedTime && selectedTime.value ? selectedTime.textContent.replace(' (Available)', '') : '';

        summaryDateTime.textContent = dateInput.value && timeLabel ? dateInput.value + ' • ' + timeLabel : '-';
        if (confirmDateTime) {
            confirmDateTime.textContent = summaryDateTime.textContent;
        }
    }

    function hasScheduleRequirement() {
        return Boolean(dateInput && dateInput.value && timeSlotSelect && timeSlotSelect.value);
    }

    function updateScheduleButtonState() {
        const canContinue = hasScheduleRequirement();

        if (nextPaymentBtn) {
            nextPaymentBtn.disabled = !canContinue;
        }

        if (scheduleRequirementMessage) {
            scheduleRequirementMessage.classList.toggle('hidden', canContinue);
        }
    }

    function updatePaymentReferenceSummary() {
        if (!paymentReference || !confirmPaymentReference) {
            return;
        }

        const reference = paymentReference.value.trim();
        confirmPaymentReference.textContent = reference ? 'Reference No.: ' + reference : 'Reference No.: Not provided';
    }

    function sanitizedPaymentReference() {
        if (!paymentReference) {
            return '';
        }

        return paymentReference.value.replace(/\D/g, '').slice(0, 12);
    }

    function syncPaymentReferenceInput() {
        if (!paymentReference) {
            return;
        }

        const reference = sanitizedPaymentReference();

        if (paymentReference.value !== reference) {
            paymentReference.value = reference;
        }
    }

    function hasPaymentRequirement() {
        const hasProof = Boolean(paymentProof && paymentProof.files && paymentProof.files.length > 0);
        const reference = sanitizedPaymentReference();
        const hasReference = reference.length === 12;
        const hasInvalidReference = Boolean(paymentReference && paymentReference.value.trim() !== '' && !hasReference);

        return (hasProof || hasReference) && !hasInvalidReference;
    }

    function updatePaymentButtonState() {
        if (!nextConfirmBtn) {
            return;
        }

        const canContinue = hasPaymentRequirement();
        nextConfirmBtn.disabled = !canContinue;

        if (paymentRequirementMessage) {
            paymentRequirementMessage.classList.toggle('hidden', canContinue);
        }
    }

    function updateConfirmButtonState() {
        if (!termsAgreement || !nextNotesBtn) {
            return;
        }

        nextNotesBtn.disabled = !termsAgreement.checked;
    }

    function updateNotesCounter() {
        if (!appointmentNotes || !notesCounter) {
            return;
        }

        notesCounter.textContent = appointmentNotes.value.length;
    }

    function toggleReasonField() {
        if (!reasonWrapper || !reasonLabel || !reasonInput) {
            return;
        }

        const hasService = Boolean(selectedServiceRadio());

        reasonWrapper.classList.toggle('hidden', hasService);
        reasonInput.required = ! hasService;

        if (hasService) {
            reasonLabel.textContent = 'Reason for Visit';
            reasonInput.value = '';
        } else {
            reasonLabel.textContent = 'Reason for Visit *';
        }
    }

    function loadTimeSlots() {
        if (!dateInput || !timeSlotSelect || !timeSlotButtons) {
            return;
        }

        const date = dateInput.value;
        const selectedService = selectedServiceRadio();
        const serviceId = selectedService ? selectedService.value : '';

        if (!date) {
            timeSlotSelect.innerHTML = '<option value="">Select date first</option>';
            timeSlotButtons.innerHTML = '<div class="rounded-xl border border-dashed border-gray-300 px-4 py-6 text-center text-sm text-gray-500">Select a date to view available times.</div>';
            updateDateTimeSummary();
            updateScheduleButtonState();
            return;
        }

        timeSlotSelect.innerHTML = '<option value="">Loading...</option>';
        timeSlotButtons.innerHTML = '<div class="rounded-xl border border-dashed border-gray-300 px-4 py-6 text-center text-sm text-gray-500">Loading time slots...</div>';
        updateScheduleButtonState();

        let url = '/appointments/slots?date=' + encodeURIComponent(date);
        if (serviceId) {
            url += '&service_id=' + encodeURIComponent(serviceId);
        }

        fetch(url)
            .then(function (response) {
                return response.json();
            })
            .then(function (result) {
                const slots = result.data || [];
                timeSlotSelect.innerHTML = '<option value="">Select a time</option>';

                if (slots.filter(function (slot) { return slot.available; }).length === 0) {
                    const option = document.createElement('option');
                    option.value = '';
                    option.textContent = 'No slots available';
                    timeSlotSelect.appendChild(option);
                    timeSlotButtons.innerHTML = '<div class="rounded-xl border border-dashed border-gray-300 px-4 py-6 text-center text-sm text-gray-500">No time slots are available for this date.</div>';
                    updateDateTimeSummary();
                    updateScheduleButtonState();
                    return;
                }

                timeSlotButtons.innerHTML = '';

                slots.forEach(function (slot) {
                    const option = document.createElement('option');
                    option.value = slot.time;

                    if (slot.past) {
                        option.textContent = slot.display + ' (Past)';
                        option.disabled = true;
                    } else if (!slot.available) {
                        option.textContent = slot.display + ' (Booked)';
                        option.disabled = true;
                    } else {
                        option.textContent = slot.display + ' (Available)';
                    }

                    if (oldTime && oldTime === slot.time && !option.disabled) {
                        option.selected = true;
                    }

                    timeSlotSelect.appendChild(option);

                    const button = document.createElement('button');
                    button.type = 'button';
                    button.textContent = slot.display;
                    button.dataset.slotTime = slot.time;
                    button.className = 'rounded-lg border px-4 py-2.5 text-sm font-semibold transition';

                    if (slot.past || !slot.available) {
                        button.disabled = true;
                        button.textContent = slot.display + (slot.past ? ' (Past)' : ' (Booked)');
                        button.classList.add('cursor-not-allowed', 'border-gray-200', 'bg-gray-50', 'text-gray-400');
                    } else {
                        button.classList.add('border-gray-200', 'bg-white', 'text-gray-800', 'hover:border-orange-300', 'hover:bg-orange-50');
                    }

                    if (option.selected) {
                        button.classList.remove('border-gray-200', 'bg-white', 'text-gray-800');
                        button.classList.add('border-orange-500', 'bg-orange-50', 'text-orange-600');
                    }

                    button.addEventListener('click', function () {
                        timeSlotSelect.value = slot.time;
                        timeSlotButtons.querySelectorAll('button').forEach(function (slotButton) {
                            slotButton.classList.remove('border-orange-500', 'bg-orange-50', 'text-orange-600');

                            if (!slotButton.disabled) {
                                slotButton.classList.add('border-gray-200', 'bg-white', 'text-gray-800');
                            }
                        });
                        button.classList.remove('border-gray-200', 'bg-white', 'text-gray-800');
                        button.classList.add('border-orange-500', 'bg-orange-50', 'text-orange-600');
                        updateDateTimeSummary();
                        updateScheduleButtonState();
                    });

                    timeSlotButtons.appendChild(button);
                });

                updateDateTimeSummary();
                updateScheduleButtonState();
            })
            .catch(function () {
                timeSlotSelect.innerHTML = '<option value="">Error loading slots</option>';
                timeSlotButtons.innerHTML = '<div class="rounded-xl border border-dashed border-red-200 bg-red-50 px-4 py-6 text-center text-sm text-red-600">Error loading time slots.</div>';
                updateDateTimeSummary();
                updateScheduleButtonState();
            });
    }

    petRadios.forEach(function (radio) {
        radio.addEventListener('change', updatePetSelection);
    });

    serviceRadios.forEach(function (radio) {
        radio.addEventListener('change', function () {
            updateServiceSelection();

            if (dateInput && dateInput.value) {
                loadTimeSlots();
            }
        });
    });

    if (nextServiceBtn && serviceSelectionSection) {
        nextServiceBtn.addEventListener('click', function () {
            updatePetSelection();
            showStep(2);
        });
    }

    if (backToPetsBtn) {
        backToPetsBtn.addEventListener('click', function () {
            showStep(1);
        });
    }

    if (nextScheduleBtn) {
        nextScheduleBtn.addEventListener('click', function () {
            updateServiceSelection();
            showStep(3);
        });
    }

    if (backToServicesBtn) {
        backToServicesBtn.addEventListener('click', function () {
            showStep(2);
        });
    }

    if (backToServicesFooterBtn) {
        backToServicesFooterBtn.addEventListener('click', function () {
            showStep(2);
        });
    }

    if (nextPaymentBtn) {
        nextPaymentBtn.addEventListener('click', function () {
            if (!hasScheduleRequirement()) {
                updateScheduleButtonState();
                return;
            }

            updateDateTimeSummary();
            showStep(4);
        });
    }

    if (backToScheduleBtn) {
        backToScheduleBtn.addEventListener('click', function () {
            showStep(3);
        });
    }

    if (backToPaymentBtn) {
        backToPaymentBtn.addEventListener('click', function () {
            showStep(4);
        });
    }

    if (nextConfirmBtn) {
        nextConfirmBtn.addEventListener('click', function () {
            if (!hasPaymentRequirement()) {
                updatePaymentButtonState();
                return;
            }

            updateDateTimeSummary();
            updatePaymentReferenceSummary();
            updateConfirmButtonState();
            showStep(5);
        });
    }

    if (nextNotesBtn) {
        nextNotesBtn.addEventListener('click', function () {
            if (!termsAgreement || !termsAgreement.checked) {
                updateConfirmButtonState();
                return;
            }

            updateNotesCounter();
            showStep(6);
        });
    }

    if (backToConfirmBtn) {
        backToConfirmBtn.addEventListener('click', function () {
            showStep(5);
        });
    }

    if (document.querySelectorAll('[data-edit-step]').length) {
        document.querySelectorAll('[data-edit-step]').forEach(function (button) {
            button.addEventListener('click', function () {
                showStep(Number(button.dataset.editStep || 1));
            });
        });
    }

    if (editPetBtn) {
        editPetBtn.addEventListener('click', function () {
            showStep(1);
            const selectedPet = selectedPetRadio();
            const selectedPetCard = selectedPet ? selectedPet.closest('[data-pet-card]') : null;

            if (selectedPetCard) {
                selectedPetCard.scrollIntoView({ behavior: 'smooth', block: 'center' });
                selectedPet.focus({ preventScroll: true });
                return;
            }

            window.scrollTo({ top: 0, behavior: 'smooth' });
        });
    }

    if (dateInput) {
        dateInput.addEventListener('change', loadTimeSlots);
    }

    if (timeSlotSelect) {
        timeSlotSelect.addEventListener('change', function () {
            updateDateTimeSummary();
            updateScheduleButtonState();
        });
    }

    if (paymentProof && paymentProofLabel) {
        paymentProof.addEventListener('change', function () {
            const file = paymentProof.files && paymentProof.files[0];
            paymentProofLabel.textContent = file ? file.name : 'JPG, PNG or PDF (Max. 5MB)';
            updatePaymentButtonState();
        });
    }

    if (paymentReference) {
        paymentReference.addEventListener('input', function () {
            syncPaymentReferenceInput();
            updatePaymentReferenceSummary();
            updatePaymentButtonState();
        });
    }

    if (termsAgreement) {
        termsAgreement.addEventListener('change', updateConfirmButtonState);
    }

    if (appointmentNotes) {
        appointmentNotes.addEventListener('input', updateNotesCounter);
    }

    if (appointmentForm && appointmentSubmitBtn) {
        appointmentForm.addEventListener('submit', function () {
            appointmentSubmitBtn.disabled = true;
            appointmentSubmitBtn.textContent = 'Booking...';
            appointmentSubmitBtn.classList.add('cursor-not-allowed', 'opacity-50');
        });
    }

    setStep(1);
    updatePetSelection();
    updateServiceSelection();
    toggleReasonField();
    updateServiceSummary();
    updateDateTimeSummary();
    updateScheduleButtonState();
    syncPaymentReferenceInput();
    updatePaymentReferenceSummary();
    updatePaymentButtonState();
    updateConfirmButtonState();
    updateNotesCounter();

    if (shouldOpenPaymentStep) {
        showStep(4);
    } else if (shouldOpenConfirmStep) {
        showStep(5);
    } else if (shouldOpenNotesStep) {
        showStep(6);
    } else if (dateInput && dateInput.value) {
        showStep(3);
        loadTimeSlots();
    }
});
</script>
@endpush
@endunless
