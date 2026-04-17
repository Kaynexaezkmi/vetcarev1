@extends('layouts.dashboard')
@section('title', 'Medical Records - VetCare')
@section('header-title', 'Medical Records')

@section('content')
@if(Session::has('success'))
<div id="successMessage" class="mb-4 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl">
    {{ Session::get('success') }}
</div>
@endif

@if(isset($pets) && $pets->count() > 1)
<div class="mb-4 flex justify-end">
    <form method="GET" action="{{ route('medical-records') }}" class="flex items-center gap-2">
        <label for="petFilter" class="text-sm text-gray-500">Select Pet:</label>
        <select id="petFilter" name="pet_id" onchange="this.form.submit()" class="px-3 py-2 rounded-lg border border-gray-300 text-sm focus:ring-2 focus:ring-orange-500 focus:border-orange-500">
            <option value="">All Pets</option>
            @foreach($pets as $pet)
            <option value="{{ $pet->id }}" {{ (int) $selectedPetId === $pet->id ? 'selected' : '' }}>{{ $pet->name }} ({{ $pet->type }})</option>
            @endforeach
        </select>
    </form>
</div>
@endif

@if($medicalRecords->isNotEmpty())
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3 md:gap-4">
        @foreach($medicalRecords as $record)
        <button type="button" class="record-card text-left bg-white rounded-xl shadow-sm p-4 md:p-5 hover:shadow-md transition-shadow cursor-pointer border border-gray-100 w-full" 
             data-record-id="{{ $record->id }}"
             data-seen-url="{{ route('medical-records.seen', $record) }}"
             data-pet="{{ $record->pet->name ?? 'Unknown Pet' }}"
             data-gender="{{ $record->pet->gender ?? '' }}"
             data-species="{{ $record->pet->type ?? '' }}"
             data-breed="{{ $record->pet->breed ?? '' }}"
             data-dob="{{ $record->pet->date_of_birth ? Carbon\Carbon::parse($record->pet->date_of_birth)->format('M d, Y') : '' }}"
             data-date="{{ $record->record_date ? Carbon\Carbon::parse($record->record_date)->format('M d, Y') : '' }}"
             data-diagnosis="{{ $record->diagnosis ?? '' }}"
             data-treatment="{{ $record->treatment ?? '' }}"
             data-notes="{{ addslashes($record->notes ?? '') }}"
             data-nextcall="{{ addslashes($record->next_call ?? '') }}"
             onclick="openUserRecordModal(this)">
            <div class="flex items-center gap-3 mb-3">
                <div class="w-10 h-10 bg-orange-100 rounded-xl flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                    </svg>
                </div>
                <div class="min-w-0">
                    <h3 class="font-semibold text-gray-900 text-sm md:text-base truncate">{{ $record->pet->name ?? 'Unknown Pet' }}</h3>
                    <p class="text-xs text-gray-500">{{ $record->pet->type ?? '' }}</p>
                </div>
            </div>
            <div class="text-xs text-gray-500 space-y-1">
                <p>Record: {{ $record->record_date ? Carbon\Carbon::parse($record->record_date)->format('M d, Y') : '' }}</p>
                @if($record->diagnosis)
                <p><span class="font-medium text-gray-600">Diagnosis:</span> {{ $record->diagnosis }}</p>
                @endif
                @if($record->treatment)
                <p><span class="font-medium text-gray-600">Treatment:</span> {{ $record->treatment }}</p>
                @endif
                @if($record->next_call)
                <p class="text-orange-600 font-medium">Next Call: {{ $record->next_call }}</p>
                @endif
            </div>
        </button>
        @endforeach
    </div>
    <div class="mt-4 md:mt-6">
        {{ $medicalRecords->links() }}
    </div>
@else
    <div class="bg-white rounded-xl shadow-sm p-6 md:p-12 text-center">
        <svg class="w-12 h-12 md:w-16 md:h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
        </svg>
        <p class="text-gray-500 text-base md:text-lg">No medical records found.</p>
        <p class="text-gray-400 text-sm mt-2">Medical records will appear here after your pet's appointments.</p>
    </div>
@endif

<div id="recordModal" class="fixed inset-0 bg-black/50 hidden items-center justify-center p-4" style="z-index: 70">
    <div class="bg-white rounded-2xl p-4 md:p-6 w-full max-w-3xl max-h-[80vh] overflow-y-auto">
        <div class="flex justify-between items-center mb-4 md:mb-5">
            <h3 class="text-lg font-semibold text-gray-900">Medical Record</h3>
            <button onclick="window.closeRecordModal()" class="text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
        
        <div class="bg-orange-50 rounded-xl p-3 md:p-4 mb-3 md:mb-4">
            <div class="flex items-center gap-2 mb-2 md:mb-3">
                <svg class="w-4 md:w-5 h-4 md:h-5 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                </svg>
                <span class="text-sm font-semibold text-gray-700">Pet Details</span>
            </div>
            <div class="flex flex-col md:flex-row md:items-center gap-2 text-xs md:text-sm">
                <div class="flex items-center gap-2">
                    <span class="text-gray-500">Name:</span>
                    <span id="modalPetName" class="font-medium text-gray-900"></span>
                </div>
                <span class="text-gray-300 hidden md:inline">|</span>
                <div class="flex items-center gap-2">
                    <span class="text-gray-500">Gender:</span>
                    <span id="modalGender" class="text-gray-700"></span>
                </div>
                <span class="text-gray-300 hidden md:inline">|</span>
                <div class="flex items-center gap-2">
                    <span class="text-gray-500">Species:</span>
                    <span id="modalSpecies" class="text-gray-700"></span>
                </div>
                <span class="text-gray-300 hidden md:inline">|</span>
                <div class="flex items-center gap-2">
                    <span class="text-gray-500">Breed:</span>
                    <span id="modalBreed" class="text-gray-700"></span>
                </div>
                <span class="text-gray-300 hidden md:inline">|</span>
                <div class="flex items-center gap-2">
                    <span class="text-gray-500">DOB:</span>
                    <span id="modalDob" class="text-gray-700"></span>
                </div>
            </div>
        </div>

        <div class="bg-gray-50 rounded-xl p-3 md:p-4 mb-4 md:mb-5">
            <div class="flex flex-col md:flex-row md:items-center gap-2 md:gap-4 text-xs md:text-sm mb-3">
                <div class="flex items-center gap-2">
                    <span class="text-gray-500">Record Date:</span>
                    <span id="modalRecordDate" class="text-gray-700 font-medium"></span>
                </div>
            </div>
            <div class="space-y-3 mb-3">
                <div>
                    <label class="text-gray-500 text-xs md:text-sm block mb-1">Diagnosis</label>
                    <p id="modalDiagnosis" class="text-gray-700 text-xs md:text-sm bg-white p-2 rounded border border-gray-200 min-h-[60px]"></p>
                </div>
                <div>
                    <label class="text-gray-500 text-xs md:text-sm block mb-1">Treatment</label>
                    <p id="modalTreatment" class="text-gray-700 text-xs md:text-sm bg-white p-2 rounded border border-gray-200 min-h-[60px]"></p>
                </div>
                <div>
                    <label class="text-gray-500 text-xs md:text-sm block mb-1">Notes</label>
                    <p id="modalNotes" class="text-gray-700 text-xs md:text-sm bg-white p-2 rounded border border-gray-200 min-h-[60px]"></p>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <span class="text-gray-500 text-xs md:text-sm">Next Call:</span>
                <span id="modalNextCall" class="text-orange-600 font-medium text-xs md:text-sm"></span>
            </div>
        </div>

        <div class="flex justify-end">
            <button onclick="window.closeRecordModal()" class="px-6 py-2.5 bg-orange-500 text-white text-sm font-medium rounded-lg hover:bg-orange-600 w-full md:w-auto">Close</button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function openUserRecordModal(element) {
    document.getElementById('modalPetName').textContent = element.dataset.pet || 'Unknown Pet';
    document.getElementById('modalGender').textContent = element.dataset.gender || '-';
    document.getElementById('modalSpecies').textContent = element.dataset.species || '-';
    document.getElementById('modalBreed').textContent = element.dataset.breed || '-';
    document.getElementById('modalDob').textContent = element.dataset.dob || '-';
    document.getElementById('modalRecordDate').textContent = element.dataset.date || '-';
    document.getElementById('modalDiagnosis').textContent = element.dataset.diagnosis || '-';
    document.getElementById('modalTreatment').textContent = element.dataset.treatment || '-';
    document.getElementById('modalNotes').textContent = element.dataset.notes || '-';
    document.getElementById('modalNextCall').textContent = element.dataset.nextcall || '-';
    const modal = document.getElementById('recordModal');
    modal.classList.remove('hidden');
    modal.classList.add('flex');

    markMedicalRecordAsSeen(element);
}

window.closeRecordModal = function() {
    const modal = document.getElementById('recordModal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
}

var recordModal = document.getElementById('recordModal');
if (recordModal) {
    recordModal.addEventListener('click', function(e) {
        if (e.target === this) window.closeRecordModal();
    });
}

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') window.closeRecordModal();
});

function updateMedicalRecordsBadges(count) {
    document.querySelectorAll('.medical-records-badge').forEach(function(badge) {
        if (count > 0) {
            badge.textContent = count > 9 ? '9+' : String(count);
            badge.classList.remove('hidden');
            badge.classList.add('flex');
        } else {
            badge.remove();
        }
    });
}

function markMedicalRecordAsSeen(element) {
    if (element.dataset.seen === '1') {
        return;
    }

    const seenUrl = element.dataset.seenUrl;
    if (!seenUrl) {
        return;
    }

    const tokenMeta = document.querySelector('meta[name=\"csrf-token\"]');
    if (!tokenMeta) {
        return;
    }

    fetch(seenUrl, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': tokenMeta.getAttribute('content'),
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify({})
    })
    .then(function(response) {
        if (!response.ok) {
            throw new Error('Failed to mark medical record as seen.');
        }

        return response.json();
    })
    .then(function(data) {
        element.dataset.seen = '1';
        if (typeof data.remaining_unread === 'number') {
            updateMedicalRecordsBadges(data.remaining_unread);
        }
    })
    .catch(function(error) {
        console.error(error);
    });
}

// Add click handler for record cards
var recordCards = document.querySelectorAll('.record-card');
recordCards.forEach(function(card) {
    card.addEventListener('click', function() {
        openUserRecordModal(this);
    });
});
</script>
@endpush
