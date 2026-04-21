@extends('layouts.dashboard')
@section('title', 'Medical Records - VetCare')
@section('header-title', $pet->name . "'s Medical Records")

@section('content')
@php($speciesOptions = ['Dog', 'Cat', 'Bird', 'Rabbit', 'Hamster', 'Fish', 'Reptile', 'Other'])
<div class="max-w-4xl mx-auto">
    @if(session('success'))
    <div class="mb-4 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl">
        {{ session('success') }}
    </div>
    @endif

    <div class="mb-6">
        <a href="{{ route('dashboard') }}" class="text-sm text-gray-500 hover:text-gray-700">&larr; Back to Dashboard</a>
    </div>

    <div class="bg-white rounded-2xl shadow-sm p-6 mb-6">
        <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
            <div class="flex items-center">
                <div class="w-16 h-16 bg-orange-100 rounded-full flex items-center justify-center mr-4">
                    <svg class="w-8 h-8 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                    </svg>
                </div>
                <div>
                    <h3 class="text-xl font-bold text-gray-900">{{ $pet->name }}</h3>
                    <p class="text-gray-500">{{ $pet->type }} {{ $pet->breed ? '- ' . $pet->breed : '' }}</p>
                    <p class="text-sm text-gray-400">{{ $pet->gender ?: 'No gender' }} | {{ $pet->age }}</p>
                </div>
            </div>

            <div class="flex flex-col gap-3 sm:flex-row sm:items-center">
                @if(isset($allPets) && $allPets->count() > 1)
                <div class="flex items-center gap-2">
                    <label for="petSelector" class="text-sm text-gray-500">Select Pet:</label>
                    <select id="petSelector" onchange="changePet(this.value)" class="px-3 py-2 rounded-lg border border-gray-300 text-sm focus:ring-2 focus:ring-orange-500 focus:border-orange-500">
                        @foreach($allPets as $availablePet)
                        <option value="{{ $availablePet->id }}" {{ $availablePet->id == $pet->id ? 'selected' : '' }}>{{ $availablePet->name }} ({{ $availablePet->type }})</option>
                        @endforeach
                    </select>
                </div>
                @endif

                <button type="button" onclick="openPetUpdateModal()" class="inline-flex items-center justify-center px-4 py-2 bg-orange-500 text-white text-sm font-medium rounded-lg hover:bg-orange-600 transition">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5h2m-1 0v14m-7-7h14"></path>
                    </svg>
                    Update Pet
                </button>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-sm">
        <div class="p-6 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Medical History</h3>
        </div>

        @if($records->count() > 0)
        <div class="grid gap-4 md:grid-cols-2 p-6">
            @foreach($records as $record)
            @php
                $fileExtension = $record->file_path ? pathinfo($record->file_path, PATHINFO_EXTENSION) : null;
                $isImage = $fileExtension && in_array(strtolower($fileExtension), ['jpg', 'jpeg', 'png', 'gif', 'webp']);
                $attachmentUrl = $record->file_path ? Storage::url($record->file_path) : '';
            @endphp
            <button
                type="button"
                class="text-left bg-gray-50 rounded-xl p-4 border border-gray-100 hover:shadow-md transition-shadow cursor-pointer"
                data-record-title="{{ $record->title ?? 'Medical Record' }}"
                data-record-date="{{ $record->record_date ? Carbon\Carbon::parse($record->record_date)->format('M d, Y') : '-' }}"
                data-diagnosis="{{ $record->diagnosis ?? '' }}"
                data-treatment="{{ $record->treatment ?? '' }}"
                data-notes="{{ $record->notes ?? '' }}"
                data-attachment-url="{{ $attachmentUrl }}"
                data-attachment-image="{{ $isImage ? '1' : '0' }}"
                onclick="openPetRecordModal(this)"
            >
                <div class="flex items-start justify-between gap-4 mb-3">
                    <div class="min-w-0">
                        <h4 class="font-semibold text-gray-900 truncate">{{ $record->title ?? 'Medical Record' }}</h4>
                        <p class="text-sm text-gray-500">{{ $record->record_date ? Carbon\Carbon::parse($record->record_date)->format('M d, Y') : '-' }}</p>
                    </div>
                    <span class="text-xs text-orange-500 font-medium shrink-0">View details</span>
                </div>
                <div class="space-y-2 text-sm">
                    @if($record->diagnosis)
                    <div>
                        <span class="text-xs font-medium text-gray-500 uppercase">Diagnosis</span>
                        <p class="text-gray-700 line-clamp-1">{{ $record->diagnosis }}</p>
                    </div>
                    @endif
                    @if($record->treatment)
                    <div>
                        <span class="text-xs font-medium text-gray-500 uppercase">Treatment</span>
                        <p class="text-gray-700 line-clamp-1">{{ $record->treatment }}</p>
                    </div>
                    @endif
                    @if($record->notes)
                    <div>
                        <span class="text-xs font-medium text-gray-500 uppercase">Notes</span>
                        <p class="text-gray-700 line-clamp-1">{{ $record->notes }}</p>
                    </div>
                    @endif
                </div>
            </button>
            @endforeach
        </div>
        @else
        <div class="p-12 text-center">
            <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
            <p class="text-gray-500">No medical records found</p>
        </div>
        @endif
    </div>
</div>

<div id="petUpdateModal" class="fixed inset-0 bg-black/50 hidden items-center justify-center p-4" style="z-index: 70">
    <div class="bg-white rounded-2xl p-4 md:p-6 w-full max-w-2xl">
        <div class="flex justify-between items-center mb-5">
            <h3 class="text-lg font-semibold text-gray-900">Update Pet Profile</h3>
            <button type="button" onclick="closePetUpdateModal()" class="text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>

        <form action="{{ route('pets.update', $pet) }}" method="POST">
            @csrf
            @method('PUT')
            <input type="hidden" name="activity_context" value="medical_history">

            <div class="grid gap-4 md:grid-cols-2">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Name</label>
                    <input type="text" name="name" value="{{ $pet->name }}" required class="w-full px-3 py-2.5 rounded-lg border border-gray-300 text-sm focus:ring-2 focus:ring-orange-500 focus:border-orange-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Gender</label>
                    <select name="gender" class="w-full px-3 py-2.5 rounded-lg border border-gray-300 text-sm focus:ring-2 focus:ring-orange-500 focus:border-orange-500">
                        <option value="">Select gender</option>
                        <option value="Male" @selected($pet->gender === 'Male')>Male</option>
                        <option value="Female" @selected($pet->gender === 'Female')>Female</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Species</label>
                    <select name="type" required class="w-full px-3 py-2.5 rounded-lg border border-gray-300 text-sm focus:ring-2 focus:ring-orange-500 focus:border-orange-500">
                        <option value="">Select species</option>
                        @foreach($speciesOptions as $species)
                        <option value="{{ $species }}" @selected($pet->type === $species)>{{ $species }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Breed</label>
                    <input type="text" name="breed" value="{{ $pet->breed }}" class="w-full px-3 py-2.5 rounded-lg border border-gray-300 text-sm focus:ring-2 focus:ring-orange-500 focus:border-orange-500">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">DOB</label>
                    <input type="date" name="date_of_birth" value="{{ optional($pet->date_of_birth)->format('Y-m-d') }}" class="w-full px-3 py-2.5 rounded-lg border border-gray-300 text-sm focus:ring-2 focus:ring-orange-500 focus:border-orange-500">
                </div>
            </div>

            <div class="mt-6 flex flex-col-reverse gap-2 sm:flex-row sm:justify-end">
                <button type="button" onclick="closePetUpdateModal()" class="px-5 py-2.5 text-gray-600 hover:text-gray-800 hover:bg-gray-100 rounded-lg transition-colors">Cancel</button>
                <button type="submit" class="px-6 py-2.5 bg-orange-500 text-white font-medium rounded-lg hover:bg-orange-600 transition-colors">Update Pet</button>
            </div>
        </form>
    </div>
</div>

<div id="petRecordModal" class="fixed inset-0 bg-black/50 hidden items-center justify-center p-4" style="z-index: 70">
    <div class="bg-white rounded-2xl p-4 md:p-6 w-full max-w-3xl max-h-[80vh] overflow-y-auto">
        <div class="flex justify-between items-center mb-4">
            <h3 id="petRecordModalTitle" class="text-lg font-semibold text-gray-900">Medical Record</h3>
            <button type="button" onclick="closePetRecordModal()" class="text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>

        <div class="bg-gray-50 rounded-xl p-4 mb-4">
            <div class="flex items-center gap-2 text-sm mb-3">
                <span class="text-gray-500">Record Date:</span>
                <span id="petRecordModalDate" class="text-gray-800 font-medium">-</span>
            </div>

            <div class="space-y-3 text-sm">
                <div>
                    <label class="text-xs font-medium text-gray-500 uppercase block mb-1">Diagnosis</label>
                    <p id="petRecordModalDiagnosis" class="text-gray-700 bg-white p-3 rounded-lg border border-gray-200 min-h-[56px]">-</p>
                </div>
                <div>
                    <label class="text-xs font-medium text-gray-500 uppercase block mb-1">Treatment</label>
                    <p id="petRecordModalTreatment" class="text-gray-700 bg-white p-3 rounded-lg border border-gray-200 min-h-[56px]">-</p>
                </div>
                <div>
                    <label class="text-xs font-medium text-gray-500 uppercase block mb-1">Notes</label>
                    <p id="petRecordModalNotes" class="text-gray-700 bg-white p-3 rounded-lg border border-gray-200 min-h-[56px]">-</p>
                </div>
            </div>
        </div>

        <div id="petRecordModalAttachmentWrapper" class="hidden mb-4">
            <label class="text-xs font-medium text-gray-500 uppercase block mb-2">Attachment</label>
            <a id="petRecordModalAttachmentLink" href="#" target="_blank" rel="noopener noreferrer" class="block">
                <img id="petRecordModalAttachmentImage" src="" alt="Medical record attachment" class="hidden max-w-full max-h-72 object-contain rounded-lg border border-gray-200 bg-gray-50">
                <div id="petRecordModalAttachmentFile" class="hidden px-4 py-3 rounded-lg border border-gray-200 bg-gray-50 text-sm text-orange-600 font-medium">
                    Open attachment
                </div>
            </a>
        </div>

        <div class="flex justify-end">
            <button type="button" onclick="closePetRecordModal()" class="px-6 py-2.5 bg-orange-500 text-white text-sm font-medium rounded-lg hover:bg-orange-600">
                Close
            </button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function openPetUpdateModal() {
    const modal = document.getElementById('petUpdateModal');
    modal.classList.remove('hidden');
    modal.classList.add('flex');
}

function closePetUpdateModal() {
    const modal = document.getElementById('petUpdateModal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
}

function openPetRecordModal(element) {
    document.getElementById('petRecordModalTitle').textContent = element.dataset.recordTitle || 'Medical Record';
    document.getElementById('petRecordModalDate').textContent = element.dataset.recordDate || '-';
    document.getElementById('petRecordModalDiagnosis').textContent = element.dataset.diagnosis || '-';
    document.getElementById('petRecordModalTreatment').textContent = element.dataset.treatment || '-';
    document.getElementById('petRecordModalNotes').textContent = element.dataset.notes || '-';

    const attachmentWrapper = document.getElementById('petRecordModalAttachmentWrapper');
    const attachmentLink = document.getElementById('petRecordModalAttachmentLink');
    const attachmentImage = document.getElementById('petRecordModalAttachmentImage');
    const attachmentFile = document.getElementById('petRecordModalAttachmentFile');
    const attachmentUrl = element.dataset.attachmentUrl || '';
    const isImage = element.dataset.attachmentImage === '1';

    if (attachmentUrl) {
        attachmentWrapper.classList.remove('hidden');
        attachmentLink.href = attachmentUrl;
        attachmentImage.classList.toggle('hidden', !isImage);
        attachmentFile.classList.toggle('hidden', isImage);

        if (isImage) {
            attachmentImage.src = attachmentUrl;
        } else {
            attachmentImage.src = '';
        }
    } else {
        attachmentWrapper.classList.add('hidden');
        attachmentLink.href = '#';
        attachmentImage.src = '';
        attachmentImage.classList.add('hidden');
        attachmentFile.classList.add('hidden');
    }

    const modal = document.getElementById('petRecordModal');
    modal.classList.remove('hidden');
    modal.classList.add('flex');
}

function closePetRecordModal() {
    const modal = document.getElementById('petRecordModal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
}

function changePet(petId) {
    if (!petId) {
        return;
    }

    window.location.href = '{{ url('/pets') }}/' + petId + '/records';
}

const petRecordModal = document.getElementById('petRecordModal');
if (petRecordModal) {
    petRecordModal.addEventListener('click', function(event) {
        if (event.target === petRecordModal) {
            closePetRecordModal();
        }
    });
}

const petUpdateModal = document.getElementById('petUpdateModal');
if (petUpdateModal) {
    petUpdateModal.addEventListener('click', function(event) {
        if (event.target === petUpdateModal) {
            closePetUpdateModal();
        }
    });
}

document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        closePetRecordModal();
        closePetUpdateModal();
    }
});
</script>
@endpush
