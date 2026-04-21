@extends('layouts.dashboard')
@section('title', 'Settings - VetCare')
@section('header-title', 'Settings')

@section('content')
@php
    $fieldClass = 'w-full rounded-lg border border-gray-300 bg-white px-3 py-2.5 text-sm text-gray-700 focus:border-orange-500 focus:ring-2 focus:ring-orange-500';
    $fieldMutedClass = 'w-full rounded-lg border border-gray-200 bg-gray-50 px-3 py-2.5 text-sm text-gray-500';
    $buttonClass = 'inline-flex items-center justify-center rounded-lg bg-orange-500 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-orange-600';
    $speciesOptions = ['Dog', 'Cat', 'Bird', 'Rabbit', 'Hamster', 'Fish', 'Reptile', 'Other'];
@endphp

<div class="max-w-5xl mx-auto space-y-5">
    @if(session('success'))
    <div class="rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">
        {{ session('success') }}
    </div>
    @endif

    @if($errors->any())
    <div class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-red-700">
        <ul class="list-disc list-inside text-sm space-y-1">
            @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm md:p-5">
        <div class="mb-4 flex items-start justify-between gap-3">
            <div>
                <h3 class="text-base font-semibold text-gray-900">Profile Settings</h3>
                <p class="mt-1 text-sm text-gray-500">Keep your account details up to date.</p>
            </div>
        </div>

        <form action="{{ route('settings.update') }}" method="POST">
            @csrf
            @method('PUT')

            <div class="grid gap-4 md:grid-cols-2">
                <div>
                    <label class="mb-1.5 block text-xs font-medium uppercase tracking-wide text-gray-500">Full Name</label>
                    <input type="text" value="{{ Auth::user()->name }}" readonly class="{{ $fieldMutedClass }}">
                    <p class="text-xs text-gray-500 mt-1">Full name cannot be changed</p>
                </div>

                <div>
                    <label class="mb-1.5 block text-xs font-medium uppercase tracking-wide text-gray-500">Email Address</label>
                    <input type="email" value="{{ Auth::user()->email }}" disabled class="{{ $fieldMutedClass }}">
                    <p class="text-xs text-gray-500 mt-1">Email cannot be changed</p>
                </div>

                <div>
                    <label class="mb-1.5 block text-xs font-medium uppercase tracking-wide text-gray-500">Phone Number</label>
                    <div class="flex items-center">
                        <div class="flex shrink-0 items-center rounded-l-lg border border-r-0 border-gray-300 bg-gray-100 px-3 py-2.5 text-sm font-medium text-gray-500">
                            +63
                        </div>
                        <input type="tel" name="phone" value="{{ old('phone', isset(Auth::user()->phone) ? (str_starts_with(Auth::user()->phone, '+63') ? substr(Auth::user()->phone, 3) : Auth::user()->phone) : '') }}" placeholder="9XXXXXXXXX" pattern="[0-9]{11}" maxlength="11" class="min-w-0 flex-1 rounded-r-lg border border-gray-300 px-3 py-2.5 text-sm text-gray-700 focus:border-orange-500 focus:ring-2 focus:ring-orange-500">
                    </div>
                    <p class="text-xs text-gray-500 mt-1">Enter 11-digit mobile number</p>
                </div>

                <div class="md:col-span-2">
                    <label class="mb-1.5 block text-xs font-medium uppercase tracking-wide text-gray-500">Address</label>
                    <textarea name="address" rows="2" placeholder="Enter your address" class="{{ $fieldClass }}">{{ old('address', Auth::user()->address) }}</textarea>
                </div>
            </div>

            <div class="mt-5 flex justify-end border-t border-gray-100 pt-4">
                <button type="submit" class="{{ $buttonClass }}">
                    Update Profile
                </button>
            </div>
        </form>
    </div>

    <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm md:p-5">
        <div class="mb-4">
            <h3 class="text-base font-semibold text-gray-900">Pet Profile</h3>
            <p class="text-sm text-gray-500 mt-1">Manage your pet details here.</p>
        </div>

        <form action="{{ route('pets.store') }}" method="POST">
            @csrf

            <div class="mb-4 flex items-start justify-between gap-3">
                <div>
                    <h4 class="text-sm font-semibold text-gray-900">Add New Pet</h4>
                    <p class="mt-1 text-xs text-gray-500">You can add more than one pet profile here.</p>
                </div>
                <span class="rounded-full bg-orange-100 px-2.5 py-1 text-xs font-medium text-orange-700">New</span>
            </div>

            <div class="grid gap-4 md:grid-cols-2">
                <div>
                    <label class="mb-1.5 block text-xs font-medium uppercase tracking-wide text-gray-500">Name</label>
                    <input type="text" name="name" value="{{ old('name') }}" required class="{{ $fieldClass }}">
                </div>

                <div>
                    <label class="mb-1.5 block text-xs font-medium uppercase tracking-wide text-gray-500">Gender</label>
                    <select name="gender" class="{{ $fieldClass }}">
                        <option value="">Select gender</option>
                        <option value="Male" @selected(old('gender') === 'Male')>Male</option>
                        <option value="Female" @selected(old('gender') === 'Female')>Female</option>
                    </select>
                </div>

                <div>
                    <label class="mb-1.5 block text-xs font-medium uppercase tracking-wide text-gray-500">Species</label>
                    <select name="type" required class="{{ $fieldClass }}">
                        <option value="">Select species</option>
                        @foreach($speciesOptions as $species)
                        <option value="{{ $species }}" @selected(old('type') === $species)>{{ $species }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="mb-1.5 block text-xs font-medium uppercase tracking-wide text-gray-500">Breed</label>
                    <input type="text" name="breed" value="{{ old('breed') }}" class="{{ $fieldClass }}">
                </div>

                <div>
                    <label class="mb-1.5 block text-xs font-medium uppercase tracking-wide text-gray-500">DOB</label>
                    <input type="date" name="date_of_birth" value="{{ old('date_of_birth') }}" class="{{ $fieldClass }}">
                </div>
            </div>

            <div class="mt-5 flex justify-end border-t border-gray-100 pt-4">
                <button type="submit" class="{{ $buttonClass }}">
                    Add Pet Profile
                </button>
            </div>
        </form>

        @if($pets->isNotEmpty())
        <div class="mt-5 border-t border-gray-100 pt-5">
            <div class="mb-4">
                <h4 class="text-sm font-semibold text-gray-900">Existing Pets</h4>
                <p class="mt-1 text-xs text-gray-500">Select any saved pet below to update its profile.</p>
            </div>
        <div class="grid gap-4 xl:grid-cols-2">
            @foreach($pets as $pet)
            <div class="rounded-xl border border-gray-200 bg-gray-50/70 p-4">
                <div class="mb-4 flex items-start justify-between gap-3">
                    <div>
                        <h4 class="text-sm font-semibold text-gray-900">{{ $pet->name }}</h4>
                        <p class="mt-1 text-xs text-gray-500">Update your pet's information.</p>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="rounded-full bg-orange-100 px-2.5 py-1 text-xs font-medium text-orange-700">{{ $pet->type ?: 'Pet' }}</span>
                        <button
                            type="button"
                            class="open-delete-pet-modal inline-flex h-8 w-8 items-center justify-center rounded-lg border border-red-200 bg-white text-red-500 transition hover:bg-red-50 hover:text-red-600"
                            data-pet-id="{{ $pet->id }}"
                            data-pet-name="{{ $pet->name }}"
                            aria-label="Delete {{ $pet->name }}"
                        >
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6M9 7V4a1 1 0 011-1h4a1 1 0 011 1v3M4 7h16"></path>
                            </svg>
                        </button>
                    </div>
                </div>

                <form action="{{ route('pets.update', $pet) }}" method="POST">
                    @csrf
                    @method('PUT')

                <div class="grid gap-3 md:grid-cols-2">
                    <div>
                        <label class="mb-1.5 block text-xs font-medium uppercase tracking-wide text-gray-500">Name</label>
                        <input type="text" name="name" value="{{ $pet->name }}" required class="{{ $fieldClass }}">
                    </div>

                    <div>
                        <label class="mb-1.5 block text-xs font-medium uppercase tracking-wide text-gray-500">Gender</label>
                        <select name="gender" class="{{ $fieldClass }}">
                            <option value="">Select gender</option>
                            <option value="Male" @selected($pet->gender === 'Male')>Male</option>
                            <option value="Female" @selected($pet->gender === 'Female')>Female</option>
                        </select>
                    </div>

                    <div>
                        <label class="mb-1.5 block text-xs font-medium uppercase tracking-wide text-gray-500">Species</label>
                        <select name="type" required class="{{ $fieldClass }}">
                            <option value="">Select species</option>
                            @foreach($speciesOptions as $species)
                            <option value="{{ $species }}" @selected($pet->type === $species)>{{ $species }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="mb-1.5 block text-xs font-medium uppercase tracking-wide text-gray-500">Breed</label>
                        <input type="text" name="breed" value="{{ $pet->breed }}" class="{{ $fieldClass }}">
                    </div>

                    <div class="md:col-span-2">
                        <label class="mb-1.5 block text-xs font-medium uppercase tracking-wide text-gray-500">DOB</label>
                        <input type="date" name="date_of_birth" value="{{ optional($pet->date_of_birth)->format('Y-m-d') }}" class="{{ $fieldClass }}">
                    </div>
                </div>

                <div class="mt-4 flex justify-end border-t border-gray-200 pt-3">
                    <button type="submit" class="{{ $buttonClass }}">
                        Update Pet Profile
                    </button>
                </div>
                </form>
            </div>
            @endforeach
        </div>
        </div>
        @endif
    </div>
</div>

@if($pets->isNotEmpty())
<div id="deletePetModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-gray-900/60 px-4">
    <div class="w-full max-w-md rounded-2xl bg-white p-6 shadow-2xl">
        <div class="flex items-start justify-between gap-4">
            <div>
                <h3 class="text-lg font-semibold text-gray-900">Delete Pet Profile</h3>
                <p class="mt-2 text-sm text-gray-600">
                    Are you sure you want to delete <span id="deletePetName" class="font-semibold text-gray-900"></span>?
                    This will also remove related appointments and records for this pet.
                </p>
            </div>
            <button type="button" id="closeDeletePetModal" class="rounded-lg p-2 text-gray-400 transition hover:bg-gray-100 hover:text-gray-600" aria-label="Close delete pet modal">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>

        <div class="mt-6 flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">
            <button type="button" id="cancelDeletePetModal" class="inline-flex items-center justify-center rounded-lg border border-gray-200 px-5 py-2.5 text-sm font-medium text-gray-700 transition hover:bg-gray-50">
                Cancel
            </button>
            <form id="deletePetForm" method="POST">
                @csrf
                @method('DELETE')
                <button type="submit" class="inline-flex items-center justify-center rounded-lg bg-red-500 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-red-600">
                    Delete Pet
                </button>
            </form>
        </div>
    </div>
</div>
@endif
@endsection

@if($pets->isNotEmpty())
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const modal = document.getElementById('deletePetModal');
    const closeButton = document.getElementById('closeDeletePetModal');
    const cancelButton = document.getElementById('cancelDeletePetModal');
    const deleteForm = document.getElementById('deletePetForm');
    const deletePetName = document.getElementById('deletePetName');
    const triggers = document.querySelectorAll('.open-delete-pet-modal');
    const deleteRouteTemplate = @json(route('pets.destroy', ['pet' => '__PET_ID__']));

    if (!modal || !closeButton || !cancelButton || !deleteForm || !deletePetName || !triggers.length) {
        return;
    }

    function openModal(petId, petName) {
        deleteForm.action = deleteRouteTemplate.replace('__PET_ID__', petId);
        deletePetName.textContent = petName;
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        document.body.classList.add('overflow-hidden');
    }

    function closeModal() {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        document.body.classList.remove('overflow-hidden');
    }

    triggers.forEach(function (trigger) {
        trigger.addEventListener('click', function () {
            openModal(trigger.dataset.petId, trigger.dataset.petName);
        });
    });

    closeButton.addEventListener('click', closeModal);
    cancelButton.addEventListener('click', closeModal);
    modal.addEventListener('click', function (event) {
        if (event.target === modal) {
            closeModal();
        }
    });
    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape' && !modal.classList.contains('hidden')) {
            closeModal();
        }
    });
});
</script>
@endpush
@endif
