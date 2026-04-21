@extends('layouts.dashboard')
@section('title', 'Book Appointment - VetCare')
@section('header-title', 'Book New Appointment')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="bg-white rounded-xl md:rounded-2xl shadow-sm p-4 md:p-8">
        @if(session('error'))
        <div class="mb-4 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl">
            {{ session('error') }}
        </div>
        @endif

        @if($errors->any())
        <div class="mb-4 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl">
            <ul class="list-disc list-inside text-sm space-y-1">
                @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif
        
        <form action="{{ route('appointments.store') }}" method="POST" id="appointmentForm">
            @csrf
            
            <div class="mb-4 md:mb-6 p-3 md:p-4 bg-white border border-gray-200 rounded-xl">
                <h3 class="font-semibold text-gray-900 mb-3 md:mb-4 text-sm md:text-base">Pet Information</h3>
                @if($pets->isEmpty())
                <div class="rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-800">
                    Add a pet profile in <a href="{{ route('settings') }}" class="font-semibold text-orange-600 hover:text-orange-700">Settings</a> before booking an appointment.
                </div>
                @else
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3 md:gap-4">
                    <div class="md:col-span-2">
                        <label for="pet_id" class="block text-sm font-medium text-gray-700 mb-1 md:mb-2">Select Pet *</label>
                        <select name="pet_id" id="pet_id" required class="w-full px-3 md:px-4 py-2 md:py-3 rounded-xl border border-gray-300 text-sm md:text-base">
                            <option value="">Choose your pet</option>
                            @foreach($pets as $pet)
                            <option
                                value="{{ $pet->id }}"
                                data-name="{{ $pet->name }}"
                                data-type="{{ $pet->type }}"
                                data-breed="{{ $pet->breed ?? '' }}"
                                @selected(old('pet_id') == $pet->id)
                            >
                                {{ $pet->name }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="selected_pet_name" class="block text-sm font-medium text-gray-700 mb-1 md:mb-2">Pet Name</label>
                        <input type="text" id="selected_pet_name" readonly class="w-full px-3 md:px-4 py-2 md:py-3 rounded-xl border border-gray-200 bg-gray-50 text-sm md:text-base text-gray-700">
                    </div>
                    <div>
                        <label for="selected_pet_type" class="block text-sm font-medium text-gray-700 mb-1 md:mb-2">Species</label>
                        <input type="text" id="selected_pet_type" readonly class="w-full px-3 md:px-4 py-2 md:py-3 rounded-xl border border-gray-200 bg-gray-50 text-sm md:text-base text-gray-700">
                    </div>
                    <div class="md:col-span-2">
                        <label for="selected_pet_breed" class="block text-sm font-medium text-gray-700 mb-1 md:mb-2">Breed</label>
                        <input type="text" id="selected_pet_breed" readonly class="w-full px-3 md:px-4 py-2 md:py-3 rounded-xl border border-gray-200 bg-gray-50 text-sm md:text-base text-gray-700" placeholder="No breed saved">
                    </div>
                </div>
                @endif
            </div>

            <div class="mb-4 md:mb-6">
                <label for="serviceSelect" class="block text-sm font-medium text-gray-700 mb-1 md:mb-2">Service (Optional)</label>
                <select name="service_id" id="serviceSelect" class="w-full px-3 md:px-4 py-2 md:py-3 rounded-xl border border-gray-300 text-sm md:text-base" @disabled($pets->isEmpty())>
                    <option value="">Select a service</option>
                    @foreach($services as $service)
                    <option value="{{ $service->id }}" data-is-grooming="{{ strtolower($service->name) === 'grooming' ? '1' : '0' }}" @selected(old('service_id') == $service->id)>
                        {{ $service->name }}
                    </option>
                    @endforeach
                </select>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-3 md:gap-6 mb-4 md:mb-6">
                <div>
                    <label for="appointmentDate" class="block text-sm font-medium text-gray-700 mb-1 md:mb-2">Date *</label>
                    <input type="date" name="appointment_date" id="appointmentDate" value="{{ old('appointment_date') }}" min="{{ date('Y-m-d') }}" required class="w-full px-3 md:px-4 py-2 md:py-3 rounded-xl border border-gray-300 text-sm md:text-base" @disabled($pets->isEmpty())>
                </div>
                <div>
                    <label for="timeSlot" class="block text-sm font-medium text-gray-700 mb-1 md:mb-2">Time *</label>
                    <select name="appointment_time" id="timeSlot" required class="w-full px-3 md:px-4 py-2 md:py-3 rounded-xl border border-gray-300 text-sm md:text-base" @disabled($pets->isEmpty())>
                        <option value="">Select date first</option>
                    </select>
                </div>
            </div>

            <div class="mb-4 md:mb-6" id="reasonWrapper">
                <label for="reason" id="reasonLabel" class="block text-sm font-medium text-gray-700 mb-1 md:mb-2">Reason for Visit *</label>
                <textarea name="reason" id="reason" rows="3" placeholder="Please specify your reason for visit" class="w-full px-3 md:px-4 py-2 md:py-3 rounded-xl border border-gray-300 text-sm md:text-base" @disabled($pets->isEmpty())>{{ old('reason') }}</textarea>
            </div>

            <div class="flex flex-col md:flex-row items-center justify-between pt-4 md:pt-6 border-t gap-3 md:gap-0">
                <a href="{{ route('dashboard') }}" class="text-gray-600 hover:text-gray-800 text-sm w-full md:w-auto text-center md:text-left py-2 md:py-0">Cancel</a>
                <button type="submit" id="appointmentSubmitBtn" class="px-6 md:px-8 py-2 md:py-3 bg-orange-500 text-white font-semibold rounded-xl hover:bg-orange-600 transition text-sm md:text-base w-full md:w-auto" @disabled($pets->isEmpty())>
                    Book Appointment
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const petSelect = document.getElementById('pet_id');
    const petNameInput = document.getElementById('selected_pet_name');
    const petTypeInput = document.getElementById('selected_pet_type');
    const petBreedInput = document.getElementById('selected_pet_breed');
    const dateInput = document.getElementById('appointmentDate');
    const serviceSelect = document.getElementById('serviceSelect');
    const timeSlotSelect = document.getElementById('timeSlot');
    const reasonWrapper = document.getElementById('reasonWrapper');
    const reasonLabel = document.getElementById('reasonLabel');
    const reasonInput = document.getElementById('reason');
    const oldTime = @json(old('appointment_time'));
    const appointmentForm = document.getElementById('appointmentForm');
    const appointmentSubmitBtn = document.getElementById('appointmentSubmitBtn');

    function fillSelectedPetDetails() {
        if (!petSelect || !petNameInput || !petTypeInput || !petBreedInput) {
            return;
        }

        const selectedOption = petSelect.options[petSelect.selectedIndex];

        if (!selectedOption || !selectedOption.value) {
            petNameInput.value = '';
            petTypeInput.value = '';
            petBreedInput.value = '';
            return;
        }

        petNameInput.value = selectedOption.dataset.name || '';
        petTypeInput.value = selectedOption.dataset.type || '';
        petBreedInput.value = selectedOption.dataset.breed || '';
    }

    if (petSelect) {
        petSelect.addEventListener('change', fillSelectedPetDetails);
        fillSelectedPetDetails();
    }

    if (!dateInput || !serviceSelect || !timeSlotSelect || !reasonWrapper || !reasonLabel || !reasonInput || !appointmentForm || !appointmentSubmitBtn) {
        return;
    }
    
    dateInput.addEventListener('change', function() { loadTimeSlots(); });
    serviceSelect.addEventListener('change', function() {
        toggleReasonField();
        if (dateInput.value) loadTimeSlots();
    });

    function toggleReasonField() {
        const hasService = serviceSelect.value !== '';

        reasonWrapper.classList.toggle('hidden', hasService);
        reasonInput.required = !hasService;

        if (hasService) {
            reasonLabel.textContent = 'Reason for Visit';
            reasonInput.value = '';
        } else {
            reasonLabel.textContent = 'Reason for Visit *';
        }
    }

    function loadTimeSlots() {
        const date = dateInput.value;
        const serviceId = serviceSelect.value;
        
        if (!date) { timeSlotSelect.innerHTML = '<option value="">Select date first</option>'; return; }
        
        timeSlotSelect.innerHTML = '<option value="">Loading...</option>';
        
        let url = '/api/appointments/slots?date=' + encodeURIComponent(date);
        if (serviceId) url += '&service_id=' + encodeURIComponent(serviceId);
        
        fetch(url).then(r => r.json()).then(function(result) {
            timeSlotSelect.innerHTML = '<option value="">Select a time</option>';
            const data = result.data || [];
            
            if (data.filter(function(slot) { return slot.available; }).length === 0) {
                var opt = document.createElement('option'); opt.value = ''; opt.textContent = 'No slots available';
                timeSlotSelect.appendChild(opt);
            } else {
                data.forEach(function(slot) {
                    var opt = document.createElement('option');
                    opt.value = slot.time;
                    if (slot.past) { opt.textContent = slot.display + ' (Past)'; opt.disabled = true; }
                    else if (!slot.available) { opt.textContent = slot.display + ' (Booked)'; opt.disabled = true; }
                    else { opt.textContent = slot.display + ' (Available)'; }
                    if (oldTime && oldTime === slot.time && !opt.disabled) {
                        opt.selected = true;
                    }
                    timeSlotSelect.appendChild(opt);
                });
            }
        }).catch(function() { timeSlotSelect.innerHTML = '<option value="">Error loading slots</option>'; });
    }

    toggleReasonField();

    if (dateInput.value) {
        loadTimeSlots();
    }

    appointmentForm.addEventListener('submit', function() {
        appointmentSubmitBtn.disabled = true;
        appointmentSubmitBtn.textContent = 'Booking...';
        appointmentSubmitBtn.classList.add('opacity-50', 'cursor-not-allowed');
    });
});
</script>
@endpush
