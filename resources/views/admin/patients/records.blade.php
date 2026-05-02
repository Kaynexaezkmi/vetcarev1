@extends('layouts.dashboard')
@section('title', 'Patient Records - VetCare Admin')
@section('header-title', 'Pet Medical Records')

@php
    $ownerInitial = strtoupper(substr($user->name, 0, 1));
    $selectedPetAge = $pet->date_of_birth ? $pet->date_of_birth->age.' '.\Illuminate\Support\Str::plural('year', $pet->date_of_birth->age).' old' : 'Age not set';
    $recordTypes = [
        'Vaccination' => 'bg-blue-50 text-blue-700',
        'Check-up' => 'bg-green-50 text-green-700',
        'Treatment' => 'bg-orange-50 text-orange-700',
        'Deworming' => 'bg-purple-50 text-purple-700',
        'General' => 'bg-gray-100 text-gray-700',
    ];
@endphp

@section('content')
<style>
    .records-shell {
        align-items: start;
        display: grid;
        gap: 12px;
        grid-template-columns: 220px minmax(0, 1fr) 360px;
        margin: 0 auto;
        max-width: 1280px;
    }

    .records-panel {
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        box-shadow: 0 1px 4px rgba(15, 23, 42, 0.05);
    }

    .records-panel.p-4 {
        padding: 12px !important;
    }

    .records-panel .p-4 {
        padding: 12px !important;
    }

    .records-scroll {
        max-height: none;
        overflow: visible;
    }

    .records-field {
        border: 1px solid #d1d5db;
        border-radius: 8px;
        font-size: 13px;
        padding: 10px 12px;
        width: 100%;
    }

    .records-toolbar {
        display: grid;
        gap: 8px;
        grid-template-columns: minmax(0, 1fr) 38px;
    }

    .records-table {
        min-width: 0;
        table-layout: fixed;
    }

    .records-table th {
        color: #64748b !important;
        font-size: 11px !important;
        font-weight: 700 !important;
        line-height: 1.2 !important;
        padding: 10px 12px !important;
        vertical-align: middle !important;
    }

    .records-table td {
        font-size: 12px !important;
        line-height: 1.35 !important;
        padding: 12px !important;
        vertical-align: middle !important;
    }

    .records-table th:nth-child(1),
    .records-table td:nth-child(1) {
        width: 120px;
    }

    .records-table th:nth-child(2),
    .records-table td:nth-child(2) {
        width: 145px;
    }

    .records-table th:nth-child(4),
    .records-table td:nth-child(4) {
        width: 140px;
    }

    .records-table th:nth-child(5),
    .records-table td:nth-child(5) {
        width: 96px;
    }

    .record-pill {
        display: inline-flex;
        font-size: 12px !important;
        line-height: 1.1;
        max-width: 100%;
        white-space: nowrap;
    }

    .record-editor-actions {
        align-items: center;
        display: flex;
        gap: 8px;
        justify-content: flex-end;
        margin-top: 14px;
    }

    .record-editor-actions .danger-action {
        margin-right: auto;
    }

    .record-editor-action {
        align-items: center;
        border-radius: 8px;
        display: inline-flex;
        font-size: 13px;
        font-weight: 700;
        justify-content: center;
        line-height: 1.15;
        min-height: 38px;
        min-width: 92px;
        padding: 8px 14px;
        text-align: center;
        white-space: nowrap;
    }

    .pet-card-main {
        display: grid;
        gap: 3px;
        min-width: 0;
    }

    .pet-card-title {
        align-items: center;
        display: grid;
        gap: 6px;
        grid-template-columns: minmax(0, 1fr) auto;
    }

    .pet-card-selected {
        border-radius: 999px;
        font-size: 10px;
        font-weight: 700;
        line-height: 1;
        padding: 4px 7px;
        white-space: nowrap;
    }

    .activity-panel {
        grid-column: 3;
    }

    .record-modal {
        align-items: center;
        background: rgba(15, 23, 42, 0.55);
        display: flex;
        inset: 0;
        justify-content: center;
        padding: 16px;
        position: fixed;
        z-index: 80;
    }

    .record-modal.hidden {
        display: none;
    }

    .record-modal-card {
        background: #fff;
        border-radius: 10px;
        box-shadow: 0 20px 45px rgba(15, 23, 42, 0.2);
        max-width: 390px;
        padding: 20px;
        width: 100%;
    }

    @media (max-width: 1280px) {
        .records-shell {
            grid-template-columns: 210px minmax(0, 1fr) 330px;
        }
    }

    @media (max-width: 1024px) {
        .records-shell {
            grid-template-columns: 1fr;
        }

        .records-scroll {
            max-height: none;
        }

        .activity-panel {
            grid-column: auto;
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
<div class="mb-4 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">{{ $errors->first() }}</div>
@endif

<div class="mb-4 flex flex-wrap items-center gap-2 text-xs text-gray-500">
    <a href="{{ route('dashboard') }}" class="hover:text-gray-800">Dashboard</a>
    <span>/</span>
    <a href="{{ route('admin.patients.index') }}" class="hover:text-gray-800">Pet Records</a>
    <span>/</span>
    <span>{{ $pet->name }}</span>
    <span>/</span>
    <span class="font-semibold text-gray-900">Medical Records</span>
</div>

<div class="records-shell">
    <aside class="records-panel records-scroll p-4">
        <h2 class="text-sm font-bold text-gray-950">Owner Information</h2>
        <div class="mt-5 flex items-center gap-3">
            <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-full bg-orange-100 text-lg font-bold text-orange-600">{{ $ownerInitial }}</div>
            <div class="min-w-0">
                <p class="truncate text-sm font-bold text-gray-950">{{ $user->name }}</p>
                <p class="truncate text-xs text-gray-600">{{ $user->phone ?: 'No phone number' }}</p>
                <p class="truncate text-xs text-gray-600">{{ $user->email }}</p>
            </div>
        </div>
        <div class="mt-4 space-y-2 border-t border-gray-100 pt-4 text-xs text-gray-600">
            <div>
                <p class="font-semibold text-gray-500">Address</p>
                <p class="mt-1 text-gray-800">{{ $user->address ?: 'No address provided' }}</p>
            </div>
            <div>
                <p class="font-semibold text-gray-500">Email Verified</p>
                <p class="mt-1 text-gray-800">{{ $user->email_verified_at ? $user->email_verified_at->format('M d, Y') : 'Not verified' }}</p>
            </div>
        </div>

        <div class="mt-6 border-t border-gray-100 pt-5">
            <div class="flex items-center justify-between">
                <h3 class="text-sm font-bold text-gray-950">Pets ({{ $allPets->count() }})</h3>
            </div>
            <input type="search" id="petSearch" placeholder="Search pet..." class="mt-3 w-full rounded-lg border border-gray-200 px-3 py-2 text-sm">

            <div class="mt-3 space-y-3" id="petList">
                @foreach($allPets as $ownerPet)
                @php
                    $petAge = $ownerPet->date_of_birth ? $ownerPet->date_of_birth->age.' '.\Illuminate\Support\Str::plural('year', $ownerPet->date_of_birth->age).' old' : 'Age not set';
                    $isSelectedPet = $ownerPet->id === $pet->id;
                @endphp
                <a href="{{ route('admin.patients.records', $ownerPet) }}" data-pet-card data-pet-name="{{ strtolower($ownerPet->name) }}" class="block rounded-lg border p-3 transition {{ $isSelectedPet ? 'border-orange-300 bg-orange-50' : 'border-gray-200 hover:border-orange-200 hover:bg-orange-50/40' }}">
                    <div class="flex items-center gap-3">
                        <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-full bg-orange-100 text-base font-bold text-orange-600">{{ strtoupper(substr($ownerPet->name, 0, 1)) }}</div>
                        <div class="pet-card-main flex-1">
                            <div class="pet-card-title">
                                <p class="truncate text-sm font-bold text-gray-950">{{ $ownerPet->name }}</p>
                                @if($isSelectedPet)
                                <span class="pet-card-selected bg-orange-500 text-white">Selected</span>
                                @endif
                            </div>
                            <p class="truncate text-xs text-gray-500">
                                {{ $ownerPet->breed ?: $ownerPet->type }}
                                @if($ownerPet->gender)
                                    &bull; {{ $ownerPet->gender }}
                                @endif
                                &bull; {{ $petAge }}
                            </p>
                            <p class="truncate text-xs text-gray-500">Pet ID: PET-{{ str_pad((string) $ownerPet->id, 5, '0', STR_PAD_LEFT) }}</p>
                        </div>
                    </div>
                </a>
                @endforeach
            </div>
        </div>
    </aside>

    <main class="records-panel records-scroll">
        <div class="border-b border-gray-100 p-4">
            <a href="{{ route('admin.patients.index') }}" class="text-sm font-semibold text-gray-600 hover:text-orange-600">&larr; Back to Pets</a>
            <div class="mt-5 flex flex-wrap items-center justify-between gap-3">
                <div class="flex items-center gap-4">
                    <div class="flex h-16 w-16 shrink-0 items-center justify-center rounded-full bg-orange-100 text-2xl font-bold text-orange-600">{{ strtoupper(substr($pet->name, 0, 1)) }}</div>
                    <div>
                        <h1 class="text-xl font-bold text-gray-950">{{ $pet->name }} ({{ $pet->breed ?: $pet->type }})</h1>
                        <p class="mt-1 text-xs text-gray-500">Pet ID: PET-{{ str_pad((string) $pet->id, 5, '0', STR_PAD_LEFT) }} &bull; {{ $pet->gender ?: 'Gender not set' }} &bull; {{ $selectedPetAge }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="p-4">
            <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
                <div>
                    <h2 class="text-base font-bold text-gray-950">Medical Records</h2>
                    <p class="text-xs text-gray-500">Showing {{ $records->count() }} {{ \Illuminate\Support\Str::plural('record', $records->count()) }} for this pet.</p>
                </div>
                <button type="button" data-new-record onclick="return window.resetMedicalRecordEditor()" class="inline-flex items-center justify-center rounded-lg bg-orange-500 px-4 py-2 text-sm font-bold text-white hover:bg-orange-600">
                    + Add New Record
                </button>
            </div>

            <div class="records-toolbar mb-4">
                <input type="search" id="recordSearch" placeholder="Search records..." class="rounded-lg border border-gray-200 px-3 py-2 text-sm">
                <button type="button" class="rounded-lg border border-gray-200 text-gray-500" title="Filter records">
                    <svg class="mx-auto h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4h18M6 12h12M10 20h4"></path></svg>
                </button>
            </div>

            <div class="overflow-x-auto">
                <table class="records-table w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-3 py-3 text-left text-[11px] font-bold uppercase text-gray-500">Date</th>
                            <th class="px-3 py-3 text-left text-[11px] font-bold uppercase text-gray-500">Record Type</th>
                            <th class="px-3 py-3 text-left text-[11px] font-bold uppercase text-gray-500">Diagnosis / Notes</th>
                            <th class="px-3 py-3 text-left text-[11px] font-bold uppercase text-gray-500">Added By</th>
                            <th class="px-3 py-3 text-left text-[11px] font-bold uppercase text-gray-500">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100" id="recordsTableBody">
                        @forelse($records as $record)
                        @php
                            $type = $record->treatment ?: 'General';
                            $badgeClass = $recordTypes[$type] ?? $recordTypes['General'];
                            $recordSearch = strtolower(($record->diagnosis ?? '').' '.($record->treatment ?? '').' '.($record->notes ?? '').' '.$type);
                            $recordPayload = [
                                'id' => $record->id,
                                'date' => $record->record_date?->format('Y-m-d'),
                                'dateLabel' => $record->record_date?->format('M d, Y'),
                                'type' => $type,
                                'diagnosis' => $record->diagnosis ?? '',
                                'treatment' => $record->treatment ?? '',
                                'notes' => $record->notes ?? '',
                                'nextCall' => $record->next_call ?? '',
                                'updateUrl' => url('/admin/records/'.$record->id),
                                'deleteFormId' => 'deleteForm'.$record->id,
                            ];
                        @endphp
                        <tr data-record-row data-search="{{ $recordSearch }}" data-record="{{ e(json_encode($recordPayload)) }}" class="hover:bg-orange-50/30">
                            <td class="px-3 py-3 text-xs font-medium text-gray-700">{{ $record->record_date?->format('M d, Y') }}</td>
                            <td class="px-3 py-3"><span class="record-pill rounded-full px-2 py-1 font-bold {{ $badgeClass }}">{{ $type }}</span></td>
                            <td class="px-3 py-3">
                                <p class="text-xs font-bold text-gray-900">{{ $record->diagnosis ?: 'No diagnosis entered' }}</p>
                                <p class="mt-1 line-clamp-1 text-xs text-gray-500">{{ $record->notes ?: 'No notes entered.' }}</p>
                            </td>
                            <td class="px-3 py-3 text-xs text-gray-600">
                                <p class="font-bold text-gray-800">{{ $record->creator?->name ?? 'Admin User' }}</p>
                                <p>{{ $record->created_at?->format('M d, Y') }}</p>
                                <p>{{ $record->created_at?->format('h:i A') }}</p>
                            </td>
                            <td class="px-3 py-3">
                                <div class="flex gap-1">
                                    <a href="{{ route('admin.patients.records', $pet) }}?edit={{ $record->id }}#record-editor" data-edit-record class="rounded-md border border-gray-200 p-1.5 text-gray-500 hover:bg-gray-100" title="Edit record">
                                        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Z"></path></svg>
                                    </a>
                                    <button type="button" data-delete-form="deleteForm{{ $record->id }}" data-record-label="{{ e($record->diagnosis ?: $record->treatment ?: 'this medical record') }}" onclick="return window.openMedicalRecordDeleteModal(this.dataset.deleteForm, this.dataset.recordLabel)" class="rounded-md border border-gray-200 p-1.5 text-red-500 hover:bg-red-50" title="Delete record">
                                        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166M19.228 5.79 18.16 19.673A2.25 2.25 0 0 1 15.916 21.75H8.084A2.25 2.25 0 0 1 5.84 19.673L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .397c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916"></path></svg>
                                    </button>
                                </div>
                                <form action="{{ route('admin.records.delete', $record) }}" method="POST" id="deleteForm{{ $record->id }}" class="hidden">
                                    @csrf
                                    @method('DELETE')
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-3 py-10 text-center text-sm text-gray-500">No medical records found.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    <section id="record-editor" class="records-panel records-scroll p-4">
        <h2 id="recordFormTitle" class="text-base font-bold text-gray-950">{{ $editingRecord ? 'Edit Medical Record' : 'Add Medical Record' }}</h2>
        <p id="recordFormSubtitle" class="mt-1 text-xs text-gray-500">
            {{ $editingRecord ? 'Update the details of this medical record.' : 'Create a new record for '.$pet->name.'.' }}
        </p>

        <form action="{{ $editingRecord ? route('admin.records.update', $editingRecord) : route('admin.patients.records.store', $pet) }}" method="POST" id="recordEditorForm" class="mt-5">
            @csrf
            <input type="hidden" name="_method" id="recordEditorMethod" value="PUT" @disabled(! $editingRecord)>
            <input type="hidden" name="submission_token" value="{{ old('submission_token', $recordSubmissionToken) }}">

            <div class="grid gap-3 sm:grid-cols-2">
                <div>
                    <label class="mb-1 block text-xs font-bold text-gray-700">Record Type *</label>
                    <select name="treatment" id="recordTreatment" class="records-field" required>
                        @foreach(array_keys($recordTypes) as $type)
                        <option value="{{ $type }}" @selected(old('treatment', $editingRecord?->treatment ?? 'Vaccination') === $type)>{{ $type }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="mb-1 block text-xs font-bold text-gray-700">Date *</label>
                    <input type="date" name="record_date" id="recordDate" value="{{ old('record_date', $editingRecord?->record_date?->format('Y-m-d') ?? now()->toDateString()) }}" class="records-field" required>
                </div>
            </div>

            <div class="mt-3">
                <label class="mb-1 block text-xs font-bold text-gray-700">Diagnosis / Notes *</label>
                <textarea name="diagnosis" id="recordDiagnosis" rows="5" class="records-field" required placeholder="Enter diagnosis or record notes...">{{ old('diagnosis', $editingRecord?->diagnosis) }}</textarea>
            </div>

            <div class="mt-3 grid gap-3 sm:grid-cols-2">
                <div>
                    <label class="mb-1 block text-xs font-bold text-gray-700">Treatment Given</label>
                    <input type="text" id="recordTreatmentGiven" class="records-field" placeholder="Optional treatment details">
                </div>
                <div>
                    <label class="mb-1 block text-xs font-bold text-gray-700">Next Visit / Due Date</label>
                    <input type="text" name="next_call" id="recordNextCall" value="{{ old('next_call', $editingRecord?->next_call) }}" class="records-field" placeholder="e.g. May 25, 2027">
                </div>
            </div>

            <div class="mt-3">
                <label class="mb-1 block text-xs font-bold text-gray-700">Additional Notes</label>
                <textarea name="notes" id="recordNotes" rows="5" class="records-field" placeholder="Optional notes...">{{ old('notes', $editingRecord?->notes) }}</textarea>
            </div>

            <div class="mt-4 rounded-lg bg-blue-50 px-3 py-2 text-xs font-semibold text-blue-700">
                Keeping medical records up to date helps ensure the best care for your patients.
            </div>

            <div class="record-editor-actions">
                @if($editingRecord)
                <button type="button" onclick="return window.openMedicalRecordDeleteModal('deleteEditorForm', @js($editingRecord->diagnosis ?: $editingRecord->treatment ?: 'this medical record'))" id="deleteSelectedRecordBtn" class="record-editor-action danger-action border border-red-200 text-red-600 hover:bg-red-50">Delete</button>
                @else
                <button type="button" id="deleteSelectedRecordBtn" class="record-editor-action danger-action hidden border border-red-200 text-red-600 hover:bg-red-50">Delete</button>
                @endif
                <a href="{{ route('admin.patients.records', $pet) }}#record-editor" class="record-editor-action border border-gray-200 text-gray-700 hover:bg-gray-50">Cancel</a>
                <button type="submit" id="recordSubmitBtn" class="record-editor-action bg-orange-500 text-white hover:bg-orange-600">{{ $editingRecord ? 'Update' : 'Save' }}</button>
            </div>
        </form>

        @if($editingRecord)
        <form action="{{ route('admin.records.delete', $editingRecord) }}" method="POST" id="deleteEditorForm" class="hidden">
            @csrf
            @method('DELETE')
        </form>
        @endif
    </section>

    <aside class="records-panel activity-panel records-scroll p-4">
        <div class="flex items-center justify-between">
            <h2 class="text-base font-bold text-gray-950">Activity Logs</h2>
            <button type="button" class="rounded-lg border border-gray-200 p-2 text-gray-500" title="Refresh">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99"></path></svg>
            </button>
        </div>

        <div class="mt-5 space-y-5">
            @forelse($activityLogs as $log)
            @php
                $actionColor = match ($log->action) {
                    'medical_record_added' => 'bg-green-50 text-green-600',
                    'medical_record_updated' => 'bg-blue-50 text-blue-600',
                    'medical_record_deleted' => 'bg-red-50 text-red-600',
                    'pet_profile_updated' => 'bg-orange-50 text-orange-600',
                    default => 'bg-green-50 text-green-600',
                };
                $actionMark = match ($log->action) {
                    'medical_record_deleted' => '-',
                    'medical_record_updated' => '/',
                    default => '+',
                };
            @endphp
            <div class="flex gap-3">
                <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full {{ $actionColor }}">
                    <span class="text-sm font-bold leading-none">{{ $actionMark }}</span>
                </div>
                <div class="min-w-0 flex-1">
                    <div class="flex items-start justify-between gap-2">
                        <p class="text-xs font-bold text-gray-950">{{ ucwords(str_replace('_', ' ', $log->action)) }}</p>
                        <span class="shrink-0 text-[11px] text-gray-500">{{ $log->created_at->format('h:i A') }}</span>
                    </div>
                    <p class="mt-1 text-xs leading-5 text-gray-600">{{ $log->description }}</p>
                    <p class="mt-1 text-xs font-bold text-blue-600">{{ $log->actor?->name ?? 'System' }}</p>
                </div>
            </div>
            @empty
            @if($records->isNotEmpty())
                @foreach($records->take(5) as $record)
                <div class="flex gap-3">
                    <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-green-50 text-green-600">
                        <span class="text-sm font-bold leading-none">+</span>
                    </div>
                    <div class="min-w-0 flex-1">
                        <div class="flex items-start justify-between gap-2">
                            <p class="text-xs font-bold text-gray-950">Record Added</p>
                            <span class="shrink-0 text-[11px] text-gray-500">{{ $record->created_at?->format('h:i A') }}</span>
                        </div>
                        <p class="mt-1 text-xs leading-5 text-gray-600">
                            Medical record "{{ $record->diagnosis ?: $record->treatment ?: 'Untitled record' }}" was added.
                        </p>
                        <p class="mt-1 text-xs font-bold text-blue-600">{{ $record->creator?->name ?? 'Admin User' }}</p>
                    </div>
                </div>
                @endforeach
            @else
            <p class="rounded-lg bg-gray-50 px-3 py-6 text-center text-sm text-gray-500">No activity logs yet.</p>
            @endif
            @endforelse
        </div>
    </aside>
</div>

<div id="deleteRecordModal" class="record-modal hidden" role="dialog" aria-modal="true" aria-labelledby="deleteRecordModalTitle">
    <div class="record-modal-card">
        <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-full bg-red-50 text-red-500">
            <svg class="h-7 w-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166M19.228 5.79 18.16 19.673A2.25 2.25 0 0 1 15.916 21.75H8.084A2.25 2.25 0 0 1 5.84 19.673L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .397c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916"></path></svg>
        </div>
        <div class="mt-4 text-center">
            <h3 id="deleteRecordModalTitle" class="text-lg font-bold text-gray-950">Delete Medical Record</h3>
            <p class="mt-2 text-sm text-gray-600">
                Are you sure you want to delete <span id="deleteRecordLabel" class="font-semibold text-gray-900">this medical record</span>? This action cannot be undone.
            </p>
        </div>
        <div class="mt-6 flex justify-center gap-3">
            <button type="button" onclick="return window.closeMedicalRecordDeleteModal()" class="rounded-lg border border-gray-200 px-5 py-2 text-sm font-bold text-gray-700 hover:bg-gray-50">Cancel</button>
            <button type="button" onclick="return window.submitMedicalRecordDelete()" class="rounded-lg bg-red-500 px-5 py-2 text-sm font-bold text-white hover:bg-red-600">Delete Record</button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
const storeRecordUrl = @json(route('admin.patients.records.store', $pet));
const newSubmissionToken = @json($recordSubmissionToken);
let selectedRecord = null;
let selectedDeleteFormId = null;

function getRecordFromAction(element) {
    const row = element.closest('[data-record-row]');

    if (!row || !row.dataset.record) {
        return null;
    }

    return JSON.parse(row.dataset.record);
}

function setFormMode(record) {
    const form = document.getElementById('recordEditorForm');
    const method = document.getElementById('recordEditorMethod');
    const title = document.getElementById('recordFormTitle');
    const subtitle = document.getElementById('recordFormSubtitle');
    const submit = document.getElementById('recordSubmitBtn');
    const deleteButton = document.getElementById('deleteSelectedRecordBtn');

    if (!record) {
        selectedRecord = null;
        form.action = storeRecordUrl;
        method.disabled = true;
        title.textContent = 'Add Medical Record';
        subtitle.textContent = 'Create a new record for {{ addslashes($pet->name) }}.';
        submit.textContent = 'Save Record';
        deleteButton.classList.add('hidden');
        form.reset();
        document.querySelector('input[name="submission_token"]').value = newSubmissionToken;
        document.getElementById('recordDate').value = @json(now()->toDateString());
        return;
    }

    selectedRecord = record;
    form.action = record.updateUrl;
    method.disabled = false;
    method.value = 'PUT';
    title.textContent = 'Edit Medical Record';
    subtitle.textContent = 'Update the details of this medical record.';
    submit.textContent = 'Update Record';
    deleteButton.classList.remove('hidden');
    document.getElementById('recordTreatment').value = record.type || 'General';
    document.getElementById('recordDate').value = record.date || '';
    document.getElementById('recordDiagnosis').value = record.diagnosis || '';
    document.getElementById('recordTreatmentGiven').value = '';
    document.getElementById('recordNextCall').value = record.nextCall || '';
    document.getElementById('recordNotes').value = record.notes || '';

    document.getElementById('recordFormTitle')?.scrollIntoView({
        behavior: 'smooth',
        block: 'start',
    });
}

window.resetMedicalRecordEditor = function () {
    setFormMode(null);
    document.getElementById('recordFormTitle')?.scrollIntoView({
        behavior: 'smooth',
        block: 'start',
    });

    return false;
};

window.editMedicalRecordRow = function (button) {
    const record = getRecordFromAction(button);

    if (!record) {
        return false;
    }

    setFormMode(record);

    return false;
};

window.deleteMedicalRecordRow = function (button) {
    const record = getRecordFromAction(button);

    if (!record) {
        return false;
    }

    window.openMedicalRecordDeleteModal(record.deleteFormId, record.diagnosis || record.treatment || 'this medical record');

    return false;
};

window.openMedicalRecordDeleteModal = function (formId, recordLabel) {
    selectedDeleteFormId = formId;
    document.getElementById('deleteRecordLabel').textContent = recordLabel || 'this medical record';
    document.getElementById('deleteRecordModal').classList.remove('hidden');

    return false;
};

window.closeMedicalRecordDeleteModal = function () {
    selectedDeleteFormId = null;
    document.getElementById('deleteRecordModal').classList.add('hidden');

    return false;
};

window.submitMedicalRecordDelete = function () {
    if (!selectedDeleteFormId) {
        return false;
    }

    document.getElementById(selectedDeleteFormId)?.submit();

    return false;
};

document.addEventListener('click', function (event) {
    if (event.defaultPrevented) {
        return;
    }

    const editButton = event.target.closest('[data-edit-record]');

    if (editButton) {
        window.editMedicalRecordRow(editButton);
        return;
    }

    const deleteButton = event.target.closest('[data-delete-record]');

    if (deleteButton) {
        window.deleteMedicalRecordRow(deleteButton);
        return;
    }

    if (event.target.closest('[data-new-record]')) {
        window.resetMedicalRecordEditor();
        return;
    }

    if (event.target.closest('#deleteSelectedRecordBtn') && selectedRecord) {
        window.openMedicalRecordDeleteModal(selectedRecord.deleteFormId, selectedRecord.diagnosis || selectedRecord.treatment || 'this medical record');
    }
});

document.addEventListener('keydown', function (event) {
    if (event.key === 'Escape') {
        window.closeMedicalRecordDeleteModal();
    }
});

document.addEventListener('input', function (event) {
    if (event.target.matches('#petSearch')) {
        const query = event.target.value.trim().toLowerCase();
        document.querySelectorAll('[data-pet-card]').forEach(function (card) {
            card.classList.toggle('hidden', query && !card.dataset.petName.includes(query));
        });
    }

    if (event.target.matches('#recordSearch')) {
        const query = event.target.value.trim().toLowerCase();
        document.querySelectorAll('[data-record-row]').forEach(function (row) {
            row.classList.toggle('hidden', query && !row.dataset.search.includes(query));
        });
    }
});

document.addEventListener('submit', function (event) {
    if (!event.target.matches('#recordEditorForm')) {
        return;
    }

    const submit = document.getElementById('recordSubmitBtn');
    submit.disabled = true;
    submit.textContent = submit.textContent === 'Update Record' ? 'Updating...' : 'Saving...';
    submit.classList.add('opacity-50', 'cursor-not-allowed');
});
</script>
@endpush
