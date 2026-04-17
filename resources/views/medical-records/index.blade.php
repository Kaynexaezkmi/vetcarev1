@extends('layouts.dashboard')
@section('title', 'Medical Records - VetCare')
@section('header-title', $selectedPet ? $selectedPet->name . "'s Medical Records" : 'Medical Records')

@php
    $recordTypes = [
        'Vaccination' => [
            'badge' => 'bg-blue-50 text-blue-700',
            'icon' => 'text-blue-600 bg-blue-50',
            'mark' => 'M15.59 14.37a6 6 0 0 1-5.84 7.38v-4.8m5.84-2.58a14.98 14.98 0 0 0 6.16-12.12A14.98 14.98 0 0 0 9.63 8.41m5.96 5.96a14.93 14.93 0 0 1-5.96-5.96m0 0A14.98 14.98 0 0 0 2.25 14.25a14.98 14.98 0 0 0 7.5 2.7',
        ],
        'Check-up' => [
            'badge' => 'bg-green-50 text-green-700',
            'icon' => 'text-green-600 bg-green-50',
            'mark' => 'M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z',
        ],
        'Treatment' => [
            'badge' => 'bg-orange-50 text-orange-700',
            'icon' => 'text-orange-600 bg-orange-50',
            'mark' => 'M10.5 20.25h3m-6.75-3h10.5m-8.25-3h6m-4.5-11.25h3A2.25 2.25 0 0 1 15.75 5.25v13.5A2.25 2.25 0 0 1 13.5 21h-3A2.25 2.25 0 0 1 8.25 18.75V5.25A2.25 2.25 0 0 1 10.5 3Z',
        ],
        'Deworming' => [
            'badge' => 'bg-purple-50 text-purple-700',
            'icon' => 'text-purple-600 bg-purple-50',
            'mark' => 'm18.364 5.636-12.728 12.728m0-12.728 12.728 12.728',
        ],
        'General' => [
            'badge' => 'bg-gray-100 text-gray-700',
            'icon' => 'text-gray-600 bg-gray-100',
            'mark' => 'M9 12h6m-6 4h6m2.25 5H6.75A2.25 2.25 0 0 1 4.5 18.75V5.25A2.25 2.25 0 0 1 6.75 3h7.5L19.5 8.25v10.5A2.25 2.25 0 0 1 17.25 21Z',
        ],
    ];

    $resolveRecordType = function ($record): string {
        $text = strtolower(($record->title ?? '').' '.($record->diagnosis ?? '').' '.($record->treatment ?? '').' '.($record->notes ?? ''));

        return match (true) {
            str_contains($text, 'vaccine'), str_contains($text, 'vaccination'), str_contains($text, 'rabies'), str_contains($text, 'distemper') => 'Vaccination',
            str_contains($text, 'check'), str_contains($text, 'healthy'), str_contains($text, 'wellness') => 'Check-up',
            str_contains($text, 'deworm') => 'Deworming',
            str_contains($text, 'treatment'), str_contains($text, 'medication'), str_contains($text, 'infection') => 'Treatment',
            default => $record->treatment ?: 'General',
        };
    };

    $selectedPetAge = $selectedPet?->date_of_birth
        ? $selectedPet->date_of_birth->age.' '.\Illuminate\Support\Str::plural('year', $selectedPet->date_of_birth->age).' old'
        : 'Age not set';
@endphp

@section('content')
<style>
    .owner-records-shell {
        display: grid;
        gap: 20px;
        grid-template-columns: minmax(0, 1fr) 340px;
        margin: 0 auto;
        max-width: 1280px;
    }

    .owner-records-panel {
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        box-shadow: 0 1px 4px rgba(15, 23, 42, 0.05);
    }

    .owner-records-table {
        min-width: 820px;
    }

    .owner-records-table td {
        vertical-align: middle;
    }

    .owner-record-modal {
        align-items: center;
        background: rgba(15, 23, 42, 0.55);
        display: flex;
        inset: 0;
        justify-content: center;
        padding: 16px;
        position: fixed;
        z-index: 80;
    }

    .owner-record-modal.hidden {
        display: none;
    }

    @media (max-width: 1100px) {
        .owner-records-shell {
            grid-template-columns: 1fr;
        }
    }
</style>

@if(Session::has('success'))
<div id="successMessage" class="mx-auto mb-4 max-w-7xl rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">
    {{ Session::get('success') }}
</div>
@endif

<div class="mx-auto mb-4 flex max-w-7xl flex-wrap items-center gap-2 text-xs text-gray-500">
    <a href="{{ route('dashboard') }}" class="hover:text-gray-800">Dashboard</a>
    <span>/</span>
    <span>My Pets</span>
    @if($selectedPet)
    <span>/</span>
    <span>{{ $selectedPet->name }}</span>
    @endif
    <span>/</span>
    <span class="font-semibold text-gray-900">Medical Records</span>
</div>

<div class="owner-records-shell">
    <div class="space-y-5">
        <section class="owner-records-panel p-4 md:p-5">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                <div class="flex items-center gap-4">
                    <div class="flex h-20 w-20 shrink-0 items-center justify-center rounded-full bg-orange-100 text-3xl font-bold text-orange-600">
                        {{ $selectedPet ? strtoupper(substr($selectedPet->name, 0, 1)) : strtoupper(substr(Auth::user()->name, 0, 1)) }}
                    </div>
                    <div class="min-w-0">
                        @if($selectedPet)
                        <h2 class="truncate text-xl font-bold text-gray-950">{{ $selectedPet->name }} ({{ $selectedPet->breed ?: $selectedPet->type }})</h2>
                        <div class="mt-2 flex flex-wrap items-center gap-2 text-sm text-gray-500">
                            <span class="font-medium text-pink-500">{{ $selectedPet->gender ?: 'Gender not set' }}</span>
                            <span>&bull;</span>
                            <span>{{ $selectedPetAge }}</span>
                            <span>&bull;</span>
                            <span>Pet ID: PET-{{ str_pad((string) $selectedPet->id, 5, '0', STR_PAD_LEFT) }}</span>
                        </div>
                        @else
                        <h2 class="truncate text-xl font-bold text-gray-950">All Pets Medical Records</h2>
                        <div class="mt-2 flex flex-wrap items-center gap-2 text-sm text-gray-500">
                            <span>{{ $pets->count() }} {{ \Illuminate\Support\Str::plural('pet', $pets->count()) }}</span>
                            <span>&bull;</span>
                            <span>{{ $medicalRecords->total() }} {{ \Illuminate\Support\Str::plural('record', $medicalRecords->total()) }}</span>
                            <span>&bull;</span>
                            <span>Pet Owner: {{ Auth::user()->name }}</span>
                        </div>
                        @endif
                    </div>
                </div>

                @if(isset($pets) && $pets->count() > 1)
                <form method="GET" action="{{ route('medical-records') }}" class="flex items-center gap-2">
                    <label for="petFilter" class="text-sm font-medium text-gray-500">Select Pet:</label>
                    <select id="petFilter" name="pet_id" onchange="this.form.submit()" class="min-w-40 rounded-lg border border-gray-200 bg-white px-3 py-2.5 text-sm text-gray-700 focus:border-orange-500 focus:ring-orange-500">
                        <option value="">All Pets</option>
                        @foreach($pets as $pet)
                        <option value="{{ $pet->id }}" @selected((int) $selectedPetId === $pet->id)>{{ $pet->name }} ({{ $pet->type }})</option>
                        @endforeach
                    </select>
                </form>
                @endif
            </div>
        </section>

        <section class="owner-records-panel">
            <div class="flex flex-col gap-3 border-b border-gray-100 p-4 md:flex-row md:items-center md:justify-between md:p-5">
                <h2 class="text-lg font-bold text-gray-950">Medical Records</h2>
            </div>

            <div class="grid gap-3 border-b border-gray-100 p-4 md:grid-cols-[minmax(0,1fr)_220px_220px_42px] md:p-5">
                <div class="relative">
                    <svg class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z"></path>
                    </svg>
                    <input type="search" id="recordSearch" placeholder="Search records..." class="w-full rounded-lg border border-gray-200 py-2.5 pl-10 pr-3 text-sm focus:border-orange-500 focus:ring-orange-500">
                </div>
                <select id="recordTypeFilter" class="rounded-lg border border-gray-200 px-3 py-2.5 text-sm text-gray-700 focus:border-orange-500 focus:ring-orange-500">
                    <option value="">All Record Types</option>
                    @foreach(array_keys($recordTypes) as $type)
                    <option value="{{ $type }}">{{ $type }}</option>
                    @endforeach
                </select>
                <select id="recordSort" class="rounded-lg border border-gray-200 px-3 py-2.5 text-sm text-gray-700 focus:border-orange-500 focus:ring-orange-500">
                    <option value="newest">Sort by: Newest</option>
                    <option value="oldest">Sort by: Oldest</option>
                </select>
                <button type="button" class="flex h-11 w-11 items-center justify-center rounded-lg border border-gray-200 text-gray-500" title="Filter records">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4h18M6 12h12M10 20h4"></path>
                    </svg>
                </button>
            </div>

            @if($medicalRecords->isNotEmpty())
            <div class="overflow-x-auto">
                <table class="owner-records-table w-full">
                    <tbody id="recordsTableBody" class="divide-y divide-gray-100">
                        @foreach($medicalRecords as $record)
                        @php
                            $recordType = $resolveRecordType($record);
                            $typeStyle = $recordTypes[$recordType] ?? $recordTypes['General'];
                            $recordDate = $record->record_date ? Carbon\Carbon::parse($record->record_date) : null;
                            $nextDue = $record->next_call ?: null;
                            $petName = $record->pet->name ?? 'Unknown Pet';
                            $searchText = strtolower($petName.' '.($record->title ?? '').' '.($record->diagnosis ?? '').' '.($record->treatment ?? '').' '.($record->notes ?? '').' '.$recordType.' '.$nextDue);
                        @endphp
                        <tr
                            data-record-row
                            data-date="{{ $recordDate?->format('Y-m-d') }}"
                            data-type="{{ $recordType }}"
                            data-search="{{ $searchText }}"
                            class="hover:bg-orange-50/30"
                        >
                            <td class="w-32 px-5 py-4">
                                <p class="text-sm font-semibold text-gray-900">{{ $recordDate?->format('M d, Y') ?? '-' }}</p>
                                <p class="mt-1 text-xs text-gray-500">{{ $record->created_at?->format('h:i A') ?? '' }}</p>
                            </td>
                            <td class="w-16 px-2 py-4">
                                <span class="flex h-10 w-10 items-center justify-center rounded-full {{ $typeStyle['icon'] }}">
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $typeStyle['mark'] }}"></path>
                                    </svg>
                                </span>
                            </td>
                            <td class="w-32 px-2 py-4">
                                <span class="inline-flex rounded-md px-2.5 py-1 text-xs font-semibold {{ $typeStyle['badge'] }}">{{ $recordType }}</span>
                            </td>
                            <td class="px-4 py-4">
                                <p class="text-sm font-bold text-gray-950">{{ $record->title ?: ($record->diagnosis ?: 'Medical Record') }}</p>
                                <p class="mt-1 text-sm text-gray-600">{{ $record->notes ?: ($record->treatment ?: 'No notes provided.') }}</p>
                                <p class="mt-1 text-xs font-semibold text-orange-600">{{ $petName }}</p>
                            </td>
                            <td class="w-36 px-4 py-4">
                                <p class="text-xs text-gray-500">Next Due</p>
                                <p class="mt-1 text-sm text-gray-700">{{ $nextDue ?: '-' }}</p>
                            </td>
                            <td class="w-32 px-5 py-4 text-right">
                                <button
                                    type="button"
                                    class="rounded-lg border border-gray-200 px-3 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50"
                                    data-record-id="{{ $record->id }}"
                                    data-seen="{{ $record->seen_by_user_at ? '1' : '0' }}"
                                    data-seen-url="{{ route('medical-records.seen', $record) }}"
                                    data-record-title="{{ $record->title ?? 'Medical Record' }}"
                                    data-record-type="{{ $recordType }}"
                                    data-pet="{{ $petName }}"
                                    data-gender="{{ $record->pet->gender ?? '' }}"
                                    data-species="{{ $record->pet->type ?? '' }}"
                                    data-breed="{{ $record->pet->breed ?? '' }}"
                                    data-dob="{{ $record->pet->date_of_birth ? Carbon\Carbon::parse($record->pet->date_of_birth)->format('M d, Y') : '' }}"
                                    data-record-date="{{ $recordDate?->format('M d, Y') ?? '-' }}"
                                    data-diagnosis="{{ $record->diagnosis ?? '' }}"
                                    data-treatment="{{ $record->treatment ?? '' }}"
                                    data-notes="{{ $record->notes ?? '' }}"
                                    data-nextcall="{{ $record->next_call ?? '' }}"
                                    onclick="openUserRecordModal(this)"
                                >
                                    View Details
                                </button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="flex flex-col gap-3 px-5 py-4 text-sm text-gray-500 md:flex-row md:items-center md:justify-between">
                <div>
                    <span id="recordShowingCount">Showing {{ $medicalRecords->firstItem() }} to {{ $medicalRecords->lastItem() }} of {{ $medicalRecords->total() }} records</span>
                    <span id="recordEmptyMessage" class="hidden text-orange-600">No matching records found.</span>
                </div>
                {{ $medicalRecords->links() }}
            </div>
            @else
            <div class="px-5 py-14 text-center">
                <svg class="mx-auto mb-4 h-14 w-14 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5.586a1 1 0 0 1 .707.293l5.414 5.414a1 1 0 0 1 .293.707V19a2 2 0 0 1-2 2Z"></path>
                </svg>
                <p class="text-base font-semibold text-gray-700">No medical records found.</p>
                <p class="mt-1 text-sm text-gray-400">Medical records will appear here after clinic staff add them.</p>
            </div>
            @endif
        </section>

        <div class="rounded-lg border border-blue-100 bg-blue-50 px-5 py-4 text-sm text-blue-700">
            Keep your pet records up to date for better care and accurate treatment. For questions, contact our clinic.
        </div>
    </div>

    <aside class="owner-records-panel p-5">
        <div class="flex items-center justify-between">
            <h2 class="text-base font-bold text-gray-950">Activity Logs</h2>
            <span class="text-xs font-semibold text-orange-500">{{ $activityLogs->count() ? 'Recent' : '' }}</span>
        </div>

        <div class="mt-6 space-y-7">
            @forelse($activityLogs as $log)
            @php
                $actionColor = match ($log->action) {
                    'medical_record_added' => 'bg-green-50 text-green-600',
                    'medical_record_updated' => 'bg-blue-50 text-blue-600',
                    'medical_record_deleted' => 'bg-red-50 text-red-600',
                    'pet_profile_updated' => 'bg-orange-50 text-orange-600',
                    default => 'bg-green-50 text-green-600',
                };
                $actionIcon = match ($log->action) {
                    'medical_record_updated', 'pet_profile_updated' => 'M16.862 4.487 18.55 2.8a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Z',
                    'medical_record_deleted' => 'm14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166M19.228 5.79 18.16 19.673A2.25 2.25 0 0 1 15.916 21.75H8.084A2.25 2.25 0 0 1 5.84 19.673L4.772 5.79',
                    default => 'M12 4.5v15m7.5-7.5h-15',
                };
            @endphp
            <div class="flex gap-4">
                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full {{ $actionColor }}">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $actionIcon }}"></path>
                    </svg>
                </div>
                <div class="min-w-0 flex-1">
                    <div class="flex items-start justify-between gap-3">
                        <p class="text-sm font-bold text-gray-950">{{ ucwords(str_replace('_', ' ', $log->action)) }}</p>
                        <span class="shrink-0 text-xs text-gray-500">{{ $log->created_at->format('M d, Y h:i A') }}</span>
                    </div>
                    <p class="mt-2 text-sm leading-6 text-gray-600">{{ $log->description }}</p>
                    <p class="mt-2 text-xs font-semibold text-gray-500">By {{ $log->actor?->name ?? 'Vet Clinic Staff' }}</p>
                </div>
            </div>
            @empty
            @if($medicalRecords->isNotEmpty())
                @foreach($medicalRecords->take(5) as $record)
                <div class="flex gap-4">
                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-green-50 text-green-600">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.5v15m7.5-7.5h-15"></path>
                        </svg>
                    </div>
                    <div class="min-w-0 flex-1">
                        <div class="flex items-start justify-between gap-3">
                            <p class="text-sm font-bold text-gray-950">Record Added</p>
                            <span class="shrink-0 text-xs text-gray-500">{{ $record->created_at?->format('M d, Y h:i A') }}</span>
                        </div>
                        <p class="mt-2 text-sm leading-6 text-gray-600">{{ $record->title ?: 'Medical record' }} was added for {{ $record->pet->name ?? 'your pet' }}.</p>
                        <p class="mt-2 text-xs font-semibold text-gray-500">By Vet Clinic Staff</p>
                    </div>
                </div>
                @endforeach
            @else
            <p class="rounded-lg bg-gray-50 px-3 py-8 text-center text-sm text-gray-500">No activity logs yet.</p>
            @endif
            @endforelse
        </div>
    </aside>
</div>

<div id="recordModal" class="owner-record-modal hidden" role="dialog" aria-modal="true" aria-labelledby="modalRecordTitle">
    <div class="max-h-[86vh] w-full max-w-3xl overflow-y-auto rounded-xl bg-white p-5 shadow-2xl md:p-6">
        <div class="flex items-start justify-between gap-4">
            <div>
                <p id="modalRecordType" class="text-xs font-semibold uppercase tracking-wide text-orange-500">Medical Record</p>
                <h3 id="modalRecordTitle" class="mt-1 text-xl font-bold text-gray-950">Medical Record</h3>
            </div>
            <button type="button" onclick="window.closeRecordModal()" class="rounded-lg p-2 text-gray-400 hover:bg-gray-100 hover:text-gray-600">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18 18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>

        <div class="mt-5 rounded-lg bg-orange-50 p-4">
            <div class="flex items-center gap-2 text-sm font-semibold text-gray-700">
                <svg class="h-5 w-5 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 0 0 0 6.364L12 20.364l7.682-7.682a4.5 4.5 0 0 0-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 0 0-6.364 0Z"></path>
                </svg>
                Pet Details
            </div>
            <div class="mt-3 flex flex-wrap gap-x-4 gap-y-2 text-sm">
                <span><span class="text-gray-500">Name:</span> <span id="modalPetName" class="font-semibold text-gray-900"></span></span>
                <span><span class="text-gray-500">Gender:</span> <span id="modalGender" class="text-gray-700"></span></span>
                <span><span class="text-gray-500">Species:</span> <span id="modalSpecies" class="text-gray-700"></span></span>
                <span><span class="text-gray-500">Breed:</span> <span id="modalBreed" class="text-gray-700"></span></span>
                <span><span class="text-gray-500">DOB:</span> <span id="modalDob" class="text-gray-700"></span></span>
            </div>
        </div>

        <div class="mt-4 rounded-lg bg-gray-50 p-4">
            <div class="flex items-center gap-2 text-sm">
                <span class="text-gray-500">Record Date:</span>
                <span id="modalRecordDate" class="font-semibold text-gray-800"></span>
            </div>

            <div class="mt-4 grid gap-3">
                <div>
                    <label class="mb-1 block text-xs font-semibold uppercase text-gray-500">Diagnosis</label>
                    <p id="modalDiagnosis" class="min-h-14 rounded-lg border border-gray-200 bg-white p-3 text-sm text-gray-700"></p>
                </div>
                <div>
                    <label class="mb-1 block text-xs font-semibold uppercase text-gray-500">Treatment</label>
                    <p id="modalTreatment" class="min-h-14 rounded-lg border border-gray-200 bg-white p-3 text-sm text-gray-700"></p>
                </div>
                <div>
                    <label class="mb-1 block text-xs font-semibold uppercase text-gray-500">Notes</label>
                    <p id="modalNotes" class="min-h-14 rounded-lg border border-gray-200 bg-white p-3 text-sm text-gray-700"></p>
                </div>
                <div>
                    <label class="mb-1 block text-xs font-semibold uppercase text-gray-500">Next Due</label>
                    <p id="modalNextCall" class="rounded-lg border border-gray-200 bg-white p-3 text-sm font-semibold text-orange-600"></p>
                </div>
            </div>
        </div>

        <div class="mt-6 flex justify-end">
            <button type="button" onclick="window.closeRecordModal()" class="rounded-lg bg-orange-500 px-6 py-2.5 text-sm font-semibold text-white hover:bg-orange-600">
                Close
            </button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function openUserRecordModal(element) {
    document.getElementById('modalRecordTitle').textContent = element.dataset.recordTitle || 'Medical Record';
    document.getElementById('modalRecordType').textContent = element.dataset.recordType || 'Medical Record';
    document.getElementById('modalPetName').textContent = element.dataset.pet || 'Unknown Pet';
    document.getElementById('modalGender').textContent = element.dataset.gender || '-';
    document.getElementById('modalSpecies').textContent = element.dataset.species || '-';
    document.getElementById('modalBreed').textContent = element.dataset.breed || '-';
    document.getElementById('modalDob').textContent = element.dataset.dob || '-';
    document.getElementById('modalRecordDate').textContent = element.dataset.recordDate || '-';
    document.getElementById('modalDiagnosis').textContent = element.dataset.diagnosis || '-';
    document.getElementById('modalTreatment').textContent = element.dataset.treatment || '-';
    document.getElementById('modalNotes').textContent = element.dataset.notes || '-';
    document.getElementById('modalNextCall').textContent = element.dataset.nextcall || '-';

    const modal = document.getElementById('recordModal');
    modal.classList.remove('hidden');

    markMedicalRecordAsSeen(element);
}

window.closeRecordModal = function() {
    const modal = document.getElementById('recordModal');
    modal.classList.add('hidden');
}

function filterRecords() {
    const searchInput = document.getElementById('recordSearch');
    const typeFilter = document.getElementById('recordTypeFilter');
    const sortSelect = document.getElementById('recordSort');
    const tableBody = document.getElementById('recordsTableBody');
    const showingCount = document.getElementById('recordShowingCount');
    const emptyMessage = document.getElementById('recordEmptyMessage');

    if (!tableBody) {
        return;
    }

    const rows = Array.from(tableBody.querySelectorAll('[data-record-row]'));
    const search = searchInput ? searchInput.value.trim().toLowerCase() : '';
    const type = typeFilter ? typeFilter.value : '';
    const sort = sortSelect ? sortSelect.value : 'newest';

    rows.sort(function(first, second) {
        const firstDate = first.dataset.date || '';
        const secondDate = second.dataset.date || '';

        return sort === 'oldest' ? firstDate.localeCompare(secondDate) : secondDate.localeCompare(firstDate);
    });

    rows.forEach(function(row) {
        tableBody.appendChild(row);

        const matchesSearch = !search || (row.dataset.search || '').includes(search);
        const matchesType = !type || row.dataset.type === type;
        row.classList.toggle('hidden', !(matchesSearch && matchesType));
    });

    const visibleCount = rows.filter(function(row) {
        return !row.classList.contains('hidden');
    }).length;

    if (showingCount) {
        showingCount.textContent = visibleCount > 0
            ? 'Showing 1 to ' + visibleCount + ' of ' + rows.length + ' records on this page'
            : 'Showing 0 of ' + rows.length + ' records on this page';
    }

    if (emptyMessage) {
        emptyMessage.classList.toggle('hidden', visibleCount > 0);
    }
}

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

    const tokenMeta = document.querySelector('meta[name="csrf-token"]');
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

const recordSearch = document.getElementById('recordSearch');
const recordTypeFilter = document.getElementById('recordTypeFilter');
const recordSort = document.getElementById('recordSort');
const recordModal = document.getElementById('recordModal');

if (recordSearch) {
    recordSearch.addEventListener('input', filterRecords);
}

if (recordTypeFilter) {
    recordTypeFilter.addEventListener('change', filterRecords);
}

if (recordSort) {
    recordSort.addEventListener('change', filterRecords);
}

if (recordModal) {
    recordModal.addEventListener('click', function(event) {
        if (event.target === recordModal) {
            window.closeRecordModal();
        }
    });
}

document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        window.closeRecordModal();
    }
});

filterRecords();
</script>
@endpush
