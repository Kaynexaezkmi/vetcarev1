@extends('layouts.dashboard')
@section('title', 'Patient Records - VetCare Admin')
@section('header-title', $user->name . "'s Pets")

@section('content')
@if(session('success'))
<div class="mb-6 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl">
    {{ session('success') }}
</div>
@endif

<div class="mb-6 flex items-center justify-between">
    <a href="{{ route('admin.patients.index') }}" class="text-sm text-gray-500 hover:text-gray-700">← Back to Owners</a>
    <button onclick="openRecordModal()" class="inline-flex items-center px-4 py-2 bg-orange-500 text-white text-sm font-medium rounded-lg hover:bg-orange-600 transition">
        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
        </svg>
        Add Record
    </button>
</div>

<div class="bg-white rounded-2xl shadow-sm p-6 mb-6">
    <div class="flex items-center justify-between mb-4">
        <div class="flex items-center">
            <div class="w-12 h-12 bg-orange-100 rounded-full flex items-center justify-center mr-4">
                <span class="text-orange-500 font-semibold text-lg">{{ substr($user->name, 0, 1) }}</span>
            </div>
            <div>
                <p class="text-sm text-gray-500">Owner</p>
                <p class="font-semibold text-gray-900 text-lg">{{ $user->name }}</p>
                <p class="text-sm text-gray-500">{{ $user->email }} | {{ $user->phone ?? 'No phone' }}</p>
            </div>
        </div>
        <div class="flex items-center gap-2">
            <label class="text-sm text-gray-500">Select Pet:</label>
            <select id="petSelector" onchange="changePet(this.value)" class="px-3 py-2 rounded-lg border border-gray-300 text-sm focus:ring-2 focus:ring-orange-500 focus:border-orange-500">
                @foreach($allPets as $p)
                    <option value="{{ $p->id }}" {{ $p->id == $pet->id ? 'selected' : '' }}>{{ $p->name }} ({{ $p->type }})</option>
                @endforeach
            </select>
        </div>
    </div>
    <div class="flex items-center gap-6 pt-4 border-t border-gray-100">
        <div class="flex items-center gap-2 flex-1">
            <div class="w-8 h-8 bg-orange-100 rounded-full flex items-center justify-center shrink-0">
                <svg class="w-4 h-4 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                </svg>
            </div>
            <div>
                <p class="text-xs text-gray-500">Pet</p>
                <p class="text-sm font-semibold text-gray-900">{{ $pet->name }} ({{ $pet->type }})</p>
            </div>
        </div>
        <div class="flex items-center gap-2 flex-1">
            <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center shrink-0">
                <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                </svg>
            </div>
            <div>
                <p class="text-xs text-gray-500">Appointments</p>
                <p class="text-sm font-semibold text-gray-900">{{ $appointments->count() }}</p>
            </div>
        </div>
        <div class="flex items-center gap-2 flex-1">
            <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center shrink-0">
                <svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
            </div>
            <div>
                <p class="text-xs text-gray-500">Medical Records</p>
                <p class="text-sm font-semibold text-gray-900">{{ $records->count() }}</p>
            </div>
        </div>
    </div>
</div>

<div class="bg-white rounded-2xl shadow-sm">
    <div class="p-6 border-b border-gray-200">
        <h3 class="text-lg font-semibold text-gray-900">Medical History</h3>
    </div>
    
    @if($records->count() > 0)
    <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3 p-6" id="recordsGrid">
        @foreach($records as $record)
        <div class="bg-gray-50 rounded-xl p-4 border border-gray-100 hover:shadow-md transition-shadow cursor-pointer relative"
             id="recordCard{{ $record->id }}"
             data-pet="{{ $record->pet->name ?? '' }}"
             data-gender="{{ $record->pet->gender ?? '' }}"
             data-type="{{ $record->pet->type ?? '' }}"
             data-breed="{{ $record->pet->breed ?? '' }}"
             data-dob="{{ $record->pet->date_of_birth ? Carbon\Carbon::parse($record->pet->date_of_birth)->format('M d, Y') : '' }}"
             data-date="{{ $record->record_date ? Carbon\Carbon::parse($record->record_date)->format('M d, Y') : '' }}"
             data-diagnosis="{{ $record->diagnosis ?? '' }}"
             data-treatment="{{ $record->treatment ?? '' }}"
             data-notes="{{ addslashes($record->notes ?? '') }}"
             data-nextcall="{{ addslashes($record->next_call ?? '') }}"
             onclick="openViewRecordModal(this); event.stopPropagation()">
            <div class="flex items-center justify-between mb-3">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-orange-100 rounded-xl flex items-center justify-center">
                        <svg class="w-5 h-5 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                        </svg>
                    </div>
                    <div>
                        <h4 class="font-semibold text-gray-900">{{ $record->pet->name }}</h4>
                        <p class="text-xs text-gray-500">{{ $record->pet->type }}</p>
                    </div>
                </div>
                <form action="{{ route('admin.records.delete', $record) }}" method="POST" id="deleteForm{{ $record->id }}">
                    @csrf
                    @method('delete')
                </form>
                <button type="button" onclick="event.stopPropagation(); openDeleteModal({{ $record->id }})" class="text-gray-400 hover:text-red-500 p-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                    </svg>
                </button>
            </div>
            <div class="text-xs text-gray-500 space-y-1">
                <p>Record: {{ Carbon\Carbon::parse($record->record_date)->format('M d, Y') }}</p>
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
        </div>
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

<div id="recordModal" class="fixed inset-0 bg-black/50 items-center justify-center p-4" style="z-index: 70; display: none">
    <div class="bg-white rounded-2xl p-4 md:p-5 w-full max-w-4xl max-h-[88vh] overflow-y-auto">
        <div class="flex justify-between items-center mb-6">
            <h3 class="text-lg font-semibold text-gray-900">Add Medical Record</h3>
            <button onclick="window.closeRecordModal()" class="text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
        <form action="{{ route('admin.patients.records.store', $pet) }}" method="POST" id="medicalRecordForm">
            @csrf
            <input type="hidden" name="submission_token" value="{{ old('submission_token', $recordSubmissionToken) }}">
            <div class="grid md:grid-cols-2 gap-3 mb-4">
                <div class="bg-orange-50 border border-orange-100 rounded-xl p-4">
                    <div class="flex items-center gap-2 mb-3">
                        <svg class="w-5 h-5 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                        </svg>
                        <span class="text-sm font-semibold text-gray-700">Paws Details</span>
                    </div>
                    <div class="space-y-3">
                        <div>
                            <label class="block text-xs text-gray-500 mb-1">Pet Name</label>
                            <input type="text" name="pet_name" value="{{ $pet->name }}" class="w-full px-3 py-2.5 rounded-lg border border-orange-200 bg-white focus:ring-2 focus:ring-orange-500 focus:border-orange-500 text-sm">
                        </div>
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs text-gray-500 mb-1">Gender</label>
                                <select name="pet_gender" class="w-full px-3 py-2.5 rounded-lg border border-orange-200 bg-white focus:ring-2 focus:ring-orange-500 focus:border-orange-500 text-sm">
                                    <option value="">-</option>
                                    <option value="Male" {{ $pet->gender == 'Male' ? 'selected' : '' }}>Male</option>
                                    <option value="Female" {{ $pet->gender == 'Female' ? 'selected' : '' }}>Female</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs text-gray-500 mb-1">Species</label>
                                <select name="pet_type" class="w-full px-3 py-2.5 rounded-lg border border-orange-200 bg-white focus:ring-2 focus:ring-orange-500 focus:border-orange-500 text-sm">
                                    <option value="">-</option>
                                    <option value="Dog" {{ $pet->type == 'Dog' ? 'selected' : '' }}>Dog</option>
                                    <option value="Cat" {{ $pet->type == 'Cat' ? 'selected' : '' }}>Cat</option>
                                    <option value="Bird" {{ $pet->type == 'Bird' ? 'selected' : '' }}>Bird</option>
                                    <option value="Rabbit" {{ $pet->type == 'Rabbit' ? 'selected' : '' }}>Rabbit</option>
                                    <option value="Hamster" {{ $pet->type == 'Hamster' ? 'selected' : '' }}>Hamster</option>
                                    <option value="Fish" {{ $pet->type == 'Fish' ? 'selected' : '' }}>Fish</option>
                                    <option value="Reptile" {{ $pet->type == 'Reptile' ? 'selected' : '' }}>Reptile</option>
                                    <option value="Other" {{ $pet->type == 'Other' ? 'selected' : '' }}>Other</option>
                                </select>
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs text-gray-500 mb-1">Breed</label>
                                <input type="text" name="pet_breed" value="{{ $pet->breed ?? '' }}" placeholder="N/A" class="w-full px-3 py-2.5 rounded-lg border border-orange-200 bg-white focus:ring-2 focus:ring-orange-500 focus:border-orange-500 text-sm">
                            </div>
                            <div>
                                <label class="block text-xs text-gray-500 mb-1">DOB</label>
                                <input type="date" name="pet_dob" value="{{ $pet->date_of_birth ? Carbon\Carbon::parse($pet->date_of_birth)->format('Y-m-d') : '' }}" class="w-full px-3 py-2.5 rounded-lg border border-orange-200 bg-white focus:ring-2 focus:ring-orange-500 focus:border-orange-500 text-sm">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-blue-50 border border-blue-100 rounded-xl p-4">
                    <div class="flex items-center gap-2 mb-3">
                        <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                        <span class="text-sm font-semibold text-gray-700">Owner's Information</span>
                    </div>
                    <div class="space-y-3">
                        <div>
                            <label class="block text-xs text-gray-500 mb-1">Owner's Name</label>
                            <input type="text" value="{{ $pet->user->name }}" readonly class="w-full px-3 py-2.5 rounded-lg bg-white/80 border border-blue-100 text-gray-700 text-sm">
                        </div>
                        <div>
                            <label class="block text-xs text-gray-500 mb-1">Phone</label>
                            <div class="flex rounded-lg border border-blue-100 overflow-hidden bg-white/80">
                                <div class="flex-shrink-0 px-2 py-2.5 bg-blue-50 border-r border-blue-100">
                                    <span class="text-gray-500 text-xs">+63</span>
                                </div>
                                <input type="text" value="{{ ltrim($pet->user->phone ?? '', '0') }}" readonly class="flex-1 px-3 py-2.5 bg-transparent text-gray-700 text-sm min-w-0">
                            </div>
                        </div>
                        <div>
                            <label class="block text-xs text-gray-500 mb-1">Address</label>
                            <input type="text" value="{{ $pet->user->address ?? '-' }}" readonly class="w-full px-3 py-2.5 rounded-lg bg-white/80 border border-blue-100 text-gray-700 text-sm">
                        </div>
                        <div>
                            <label class="block text-xs text-gray-500 mb-1">Email Address</label>
                            <input type="text" value="{{ $pet->user->email }}" readonly class="w-full px-3 py-2.5 rounded-lg bg-white/80 border border-blue-100 text-gray-700 text-sm">
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid md:grid-cols-2 gap-2.5 mb-3">
                <div class="bg-gray-50 border border-gray-100 rounded-lg p-2.5">
                    <label class="block text-xs font-medium text-gray-700 mb-1">Record Date <span class="text-red-500">*</span></label>
                    <input type="date" name="record_date" value="{{ date('Y-m-d') }}" required class="w-full px-3 py-2 rounded-lg border border-gray-300 bg-white focus:ring-2 focus:ring-orange-500 focus:border-orange-500 text-sm">
                </div>
                <div class="bg-gray-50 border border-gray-100 rounded-lg p-2.5">
                    <label class="block text-xs font-medium text-gray-700 mb-1">Next Call</label>
                    <input type="text" name="next_call" placeholder="Enter next call note" class="w-full px-3 py-2 rounded-lg border border-gray-300 bg-white focus:ring-2 focus:ring-orange-500 focus:border-orange-500 text-sm">
                </div>
            </div>

            <div class="grid md:grid-cols-2 gap-2.5 mb-3">
                <div class="bg-white border border-gray-200 rounded-lg p-2.5">
                    <label class="block text-xs font-medium text-gray-700 mb-1.5">Diagnosis</label>
                    <textarea name="diagnosis" rows="2" placeholder="Enter diagnosis..." class="w-full px-3 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-orange-500 focus:border-orange-500 text-sm resize-none">{{ old('diagnosis') }}</textarea>
                </div>
                <div class="bg-white border border-gray-200 rounded-lg p-2.5">
                    <label class="block text-xs font-medium text-gray-700 mb-1.5">Treatment</label>
                    <textarea name="treatment" rows="2" placeholder="Enter treatment..." class="w-full px-3 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-orange-500 focus:border-orange-500 text-sm resize-none">{{ old('treatment') }}</textarea>
                </div>
            </div>

            <div class="mb-3 bg-white border border-gray-200 rounded-lg p-2.5">
                <label class="block text-xs font-medium text-gray-700 mb-1.5">Notes</label>
                <textarea name="notes" rows="2" placeholder="Enter notes..." class="w-full px-3 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-orange-500 focus:border-orange-500 text-sm resize-none">{{ old('notes') }}</textarea>
            </div>

            <div class="flex flex-col-reverse sm:flex-row sm:justify-end gap-2">
                <button type="button" onclick="window.closeRecordModal()" class="px-5 py-2.5 text-gray-600 hover:text-gray-800 hover:bg-gray-100 rounded-lg transition-colors">Cancel</button>
                <button type="submit" id="medicalRecordSubmitBtn" class="px-6 py-2.5 bg-orange-500 text-white font-medium rounded-lg hover:bg-orange-600 focus:ring-4 focus:ring-orange-200 transition-colors">Save Record</button>
            </div>
        </form>
    </div>
</div>

<div id="deleteModal" class="fixed inset-0 bg-black/50 items-center justify-center" style="z-index: 70; display: none">
    <div class="bg-white rounded-2xl p-6 w-full max-w-md mx-4">
        <div class="text-center">
            <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-6 h-6 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                </svg>
            </div>
            <h3 class="text-lg font-semibold text-gray-900 mb-2">Delete Record</h3>
            <p class="text-gray-500 mb-6">Are you sure you want to delete this medical record? This action cannot be undone.</p>
            <div class="flex justify-center gap-3">
                <button type="button" onclick="window.closeDeleteModal()" class="px-5 py-2.5 bg-gray-100 text-gray-700 font-medium rounded-lg hover:bg-gray-200 transition">Cancel</button>
                <button type="button" onclick="window.confirmDelete()" class="px-5 py-2.5 bg-red-500 text-white font-medium rounded-lg hover:bg-red-600 transition">Delete</button>
            </div>
        </div>
    </div>
</div>

<div id="viewRecordModal" class="fixed inset-0 bg-black/50 items-center justify-center" style="z-index: 70; display: none">
    <div class="bg-white rounded-2xl p-6 w-full max-w-3xl mx-4">
        <div class="flex justify-between items-center mb-5">
            <h3 class="text-lg font-semibold text-gray-900">Medical Record Details</h3>
            <button onclick="window.closeViewRecordModal()" class="text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
        
        <div class="bg-orange-50 rounded-xl p-4 mb-4">
            <div class="flex items-center gap-2 mb-3">
                <svg class="w-5 h-5 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                </svg>
                <span class="text-sm font-semibold text-gray-700">Pet Details</span>
            </div>
            <div class="flex flex-wrap gap-3 text-sm">
                <span class="text-gray-500">Pet Name:</span>
                <span id="viewPetName" class="font-medium text-gray-900"></span>
                <span class="text-gray-300">|</span>
                <span class="text-gray-500">Gender:</span>
                <span id="viewPetGender" class="text-gray-700"></span>
                <span class="text-gray-300">|</span>
                <span class="text-gray-500">Species:</span>
                <span id="viewPetType" class="text-gray-700"></span>
                <span class="text-gray-300">|</span>
                <span class="text-gray-500">Breed:</span>
                <span id="viewPetBreed" class="text-gray-700"></span>
                <span class="text-gray-300">|</span>
                <span class="text-gray-500">DOB:</span>
                <span id="viewPetDob" class="text-gray-700"></span>
            </div>
        </div>

        <div class="bg-gray-50 rounded-xl p-4 mb-5">
            <div class="flex flex-wrap gap-4 text-sm">
                <div class="flex items-center gap-2">
                    <span class="text-gray-500">Record Date:</span>
                    <span id="viewRecordDate" class="text-gray-700"></span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="text-gray-500">Next Call:</span>
                    <span id="viewNextCall" class="text-orange-600 font-medium"></span>
                </div>
            </div>
            <div class="mt-3 pt-3 border-t border-gray-200 space-y-3">
                <div>
                    <label class="text-xs font-medium text-gray-500 uppercase block mb-1">Diagnosis</label>
                    <p id="viewDiagnosis" class="text-gray-700 text-sm bg-white p-3 rounded-lg border border-gray-200 min-h-[56px]"></p>
                </div>
                <div>
                    <label class="text-xs font-medium text-gray-500 uppercase block mb-1">Treatment</label>
                    <p id="viewTreatment" class="text-gray-700 text-sm bg-white p-3 rounded-lg border border-gray-200 min-h-[56px]"></p>
                </div>
                <div>
                    <label class="text-xs font-medium text-gray-500 uppercase block mb-1">Notes</label>
                    <p id="viewNotes" class="text-gray-700 text-sm bg-white p-3 rounded-lg border border-gray-200 min-h-[56px]"></p>
                </div>
            </div>
        </div>

        <div class="flex justify-end">
            <button onclick="window.closeViewRecordModal()" class="px-6 py-2.5 bg-orange-500 text-white text-sm font-medium rounded-lg hover:bg-orange-600">Close</button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function openRecordModal() {
    document.getElementById('recordModal').style.display = 'flex';
}

function closeRecordModal() {
    document.getElementById('recordModal').style.display = 'none';
}

function openDeleteModal(recordId) {
    recordToDelete = recordId;
    document.getElementById('deleteModal').style.display = 'flex';
}

function closeDeleteModal() {
    document.getElementById('deleteModal').style.display = 'none';
    recordToDelete = null;
}

var recordToDelete = null;

function openViewRecordModal(element) {
    document.getElementById('viewPetName').textContent = element.getAttribute('data-pet') || '-';
    document.getElementById('viewPetGender').textContent = element.getAttribute('data-gender') || '-';
    document.getElementById('viewPetType').textContent = element.getAttribute('data-type') || '-';
    document.getElementById('viewPetBreed').textContent = element.getAttribute('data-breed') || '-';
    document.getElementById('viewPetDob').textContent = element.getAttribute('data-dob') || '-';
    document.getElementById('viewRecordDate').textContent = element.getAttribute('data-date') || '-';
    document.getElementById('viewDiagnosis').textContent = element.getAttribute('data-diagnosis') || '-';
    document.getElementById('viewTreatment').textContent = element.getAttribute('data-treatment') || '-';
    document.getElementById('viewNotes').textContent = element.getAttribute('data-notes') || '-';
    document.getElementById('viewNextCall').textContent = element.getAttribute('data-nextcall') || '-';
    document.getElementById('viewRecordModal').style.display = 'flex';
}

function closeViewRecordModal() {
    document.getElementById('viewRecordModal').style.display = 'none';
}

function changePet(petId) {
    if (!petId) {
        return;
    }

    window.location.href = '{{ url('/admin/patients') }}/' + petId + '/records';
}

window.previewRecordFile = function(input) {
    const file = input.files[0];
    const preview = document.getElementById('filePreview');
    const imgPreview = document.getElementById('previewImage');
    const fileName = document.getElementById('previewFileName');
    
    if (file) {
        if (file.type.startsWith('image/')) {
            const reader = new FileReader();
            reader.onload = function(e) {
                imgPreview.src = e.target.result;
                imgPreview.classList.remove('hidden');
            };
            reader.readAsDataURL(file);
            fileName.textContent = file.name;
        } else {
            imgPreview.src = '';
            imgPreview.classList.add('hidden');
            fileName.textContent = 'PDF: ' + file.name;
        }
        preview.classList.remove('hidden');
    } else {
        preview.classList.add('hidden');
    }
};

window.confirmDelete = function() {
    if (recordToDelete) {
        document.getElementById('deleteForm' + recordToDelete).submit();
    }
};

// Modal backdrop click
var viewRecordModal = document.getElementById('viewRecordModal');
if (viewRecordModal) {
    viewRecordModal.addEventListener('click', function(e) {
        if (e.target === viewRecordModal) {
            window.closeViewRecordModal();
        }
    });
}

var deleteModal = document.getElementById('deleteModal');
if (deleteModal) {
    deleteModal.addEventListener('click', function(e) {
        if (e.target === deleteModal) {
            window.closeDeleteModal();
        }
    });
}

// Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        window.closeViewRecordModal();
        window.closeRecordModal();
        window.closeDeleteModal();
    }
});

var medicalRecordForm = document.getElementById('medicalRecordForm');
var medicalRecordSubmitBtn = document.getElementById('medicalRecordSubmitBtn');

if (medicalRecordForm && medicalRecordSubmitBtn) {
    medicalRecordForm.addEventListener('submit', function() {
        medicalRecordSubmitBtn.disabled = true;
        medicalRecordSubmitBtn.textContent = 'Saving...';
        medicalRecordSubmitBtn.classList.add('opacity-50', 'cursor-not-allowed');
    });
}
</script>
@endpush
