@extends('layouts.dashboard')
@section('title', 'Reschedule Appointment - VetCare')
@section('header-title', 'Reschedule Appointment')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-2xl shadow-sm p-8">
        <div class="mb-6 p-4 bg-blue-50 rounded-xl">
            <p class="text-sm text-blue-700">
                <strong>Note:</strong> You can only reschedule an appointment once. Please choose your new date and time carefully.
            </p>
        </div>
        
        <div class="mb-6 p-4 bg-gray-50 rounded-xl">
            <h3 class="font-semibold text-gray-900 mb-2">Current Appointment</h3>
            <p class="text-gray-600">Pet: <span class="font-medium">{{ $appointment->pet->name }}</span></p>
            <p class="text-gray-600">Date: <span class="font-medium">{{ Carbon\Carbon::parse($appointment->appointment_date)->format('M d, Y') }}</span></p>
            <p class="text-gray-600">Time: <span class="font-medium">{{ Carbon\Carbon::parse($appointment->appointment_time)->format('h:i A') }}</span></p>
        </div>

        <form action="{{ route('appointments.reschedule.update', $appointment) }}" method="POST">
            @csrf
            @method('PUT')
            
            @if($appointment->service)
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">Service</label>
                <input type="text" value="{{ $appointment->service->name }}" disabled class="w-full px-4 py-3 rounded-xl border border-gray-200 bg-gray-50">
                <input type="hidden" name="service_id" value="{{ $appointment->service_id }}">
            </div>
            @endif

            <div class="grid md:grid-cols-2 gap-6 mb-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">New Date</label>
                    <input type="date" name="appointment_date" id="appointmentDate" min="{{ date('Y-m-d') }}" required class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:ring-2 focus:ring-orange-500 focus:border-orange-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">New Time (30 minutes)</label>
                    <select name="appointment_time" id="timeSlot" required class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:ring-2 focus:ring-orange-500 focus:border-orange-500">
                        <option value="">Select date first</option>
                    </select>
                </div>
            </div>

            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">Additional Notes (Optional)</label>
                <textarea name="reason" rows="2" class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:ring-2 focus:ring-orange-500 focus:border-orange-500" placeholder="Any additional information...">{{ $appointment->reason }}</textarea>
            </div>

            <div class="flex items-center justify-between pt-6 border-t">
                <a href="{{ route('appointments.history') }}" class="text-gray-600 hover:text-gray-800">Cancel</a>
                <button type="submit" class="px-8 py-3 bg-orange-500 text-white font-semibold rounded-xl hover:bg-orange-600 transition">
                    Confirm Reschedule
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const dateInput = document.getElementById('appointmentDate');
    console.log('Date input found:', dateInput);
    
    dateInput.addEventListener('change', function(e) {
        console.log('Date changed to:', e.target.value);
        loadTimeSlots();
    });
});

function loadTimeSlots() {
    const date = document.getElementById('appointmentDate').value;
    const timeSlotSelect = document.getElementById('timeSlot');
    const serviceInput = document.querySelector('input[name="service_id"]');
    const serviceId = serviceInput ? serviceInput.value : '';
    
    console.log('loadTimeSlots called, date:', date, 'serviceId:', serviceId);
    
    if (!date) return;
    
    timeSlotSelect.innerHTML = '<option value="">Loading...</option>';
    
    let url = '/appointments/slots?date=' + encodeURIComponent(date);
    if (serviceId) {
        url += '&service_id=' + encodeURIComponent(serviceId);
    }
    
    fetch(url)
        .then(response => response.json())
        .then(result => {
            console.log('API result:', result);
            const data = result.data || [];
            
            if (data.length === 0) {
                timeSlotSelect.innerHTML = '<option value="">No slots available</option>';
                return;
            }
            
            timeSlotSelect.innerHTML = '<option value="">Select a time</option>';
            
            data.forEach(slot => {
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
                
                timeSlotSelect.appendChild(option);
            });
        })
        .catch(error => {
            console.error('Error:', error);
            timeSlotSelect.innerHTML = '<option value="">Error loading slots</option>';
        });
}
</script>
@endpush
