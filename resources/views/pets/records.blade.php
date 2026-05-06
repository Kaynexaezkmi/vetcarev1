@extends('layouts.dashboard')
@section('title', 'Medical Records - VetCare')
@section('header-title', $pet->name . "'s Medical Records")

@php
    $selectedPetAge = $pet->date_of_birth ? $pet->date_of_birth->age.' '.\Illuminate\Support\Str::plural('year', $pet->date_of_birth->age).' old' : 'Age not set';
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
        min-width: 760px;
    }

    .owner-records-table td,
    .owner-records-table th {
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

@if(session('success'))
<div class="mx-auto mb-4 max-w-7xl rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">
    {{ session('success') }}
</div>
@endif

<div class="mx-auto mb-4 flex max-w-7xl flex-wrap items-center gap-2 text-xs text-gray-500">
    <a href="{{ route('dashboard') }}" class="hover:text-gray-800">Dashboard</a>
    <span>/</span>
    <a href="{{ route('medical-records') }}" class="hover:text-gray-800">My Pets</a>
    <span>/</span>
    <span>{{ $pet->name }}</span>
    <span>/</span>
    <span class="font-semibold text-gray-900">Medical Records</span>
</div>

<div class="owner-records-shell">
    <div class="space-y-5">
        <section class="owner-records-panel p-4 md:p-5">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div class="flex items-center gap-4">
                    <div class="flex h-20 w-20 shrink-0 items-center justify-center rounded-full bg-orange-100 text-3xl font-bold text-orange-600">
                        {{ strtoupper(substr($pet->name, 0, 1)) }}
                    </div>
                    <div class="min-w-0">
                        <h2 class="truncate text-xl font-bold text-gray-950">{{ $pet->name }} ({{ $pet->breed ?: $pet->type }})</h2>
                        <div class="mt-2 flex flex-wrap items-center gap-2 text-sm text-gray-500">
                            <span class="font-medium text-pink-500">{{ $pet->gender ?: 'Gender not set' }}</span>
                            <span>&bull;</span>
                            <span>{{ $selectedPetAge }}</span>
                            <span>&bull;</span>
                            <span>Pet ID: PET-{{ str_pad((string) $pet->id, 5, '0', STR_PAD_LEFT) }}</span>
                        </div>
                    </div>
                </div>

                @if(isset($allPets) && $allPets->count() > 1)
                <label class="inline-flex items-center gap-2 rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm font-semibold text-gray-700 shadow-sm">
                    <svg class="h-4 w-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7.5 21 3 16.5m0 0L7.5 12M3 16.5h18m-4.5-13.5L21 7.5m0 0L16.5 12M21 7.5H3"></path>
                    </svg>
                    <select id="petSelector" onchange="changePet(this.value)" class="border-0 bg-transparent p-0 text-sm font-semibold text-gray-700 focus:ring-0">
                        @foreach($allPets as $availablePet)
                        <option value="{{ $availablePet->id }}" @selected($availablePet->id === $pet->id)>Select {{ $availablePet->name }}</option>
                        @endforeach
                    </select>
                </label>
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

            @if($records->count() > 0)
            <div class="overflow-x-auto">
                <table class="owner-records-table w-full">
                    <tbody id="recordsTableBody" class="divide-y divide-gray-100">
                        @foreach($records as $record)
                        @php
                            $recordType = $resolveRecordType($record);
                            $typeStyle = $recordTypes[$recordType] ?? $recordTypes['General'];
                            $recordDate = $record->record_date ? Carbon\Carbon::parse($record->record_date) : null;
                            $nextDue = $record->next_call ?: null;
                            $searchText = strtolower(($record->title ?? '').' '.($record->diagnosis ?? '').' '.($record->treatment ?? '').' '.($record->notes ?? '').' '.$recordType.' '.$nextDue);
                            $fileExtension = $record->file_path ? pathinfo($record->file_path, PATHINFO_EXTENSION) : null;
                            $isImage = $fileExtension && in_array(strtolower($fileExtension), ['jpg', 'jpeg', 'png', 'gif', 'webp'], true);
                            $attachmentUrl = $record->file_path ? Storage::url($record->file_path) : '';
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
                            </td>
                            <td class="w-36 px-4 py-4">
                                <p class="text-xs text-gray-500">Next Due</p>
                                <p class="mt-1 text-sm text-gray-700">{{ $nextDue ?: '-' }}</p>
                            </td>
                            <td class="w-32 px-5 py-4 text-right">
                                <button
                                    type="button"
                                    class="rounded-lg border border-gray-200 px-3 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50"
                                    data-record-title="{{ $record->title ?? 'Medical Record' }}"
                                    data-record-type="{{ $recordType }}"
                                    data-record-date="{{ $recordDate?->format('M d, Y') ?? '-' }}"
                                    data-diagnosis="{{ $record->diagnosis ?? '' }}"
                                    data-treatment="{{ $record->treatment ?? '' }}"
                                    data-notes="{{ $record->notes ?? '' }}"
                                    data-nextcall="{{ $record->next_call ?? '' }}"
                                    data-attachment-url="{{ $attachmentUrl }}"
                                    data-attachment-image="{{ $isImage ? '1' : '0' }}"
                                    onclick="openPetRecordModal(this)"
                                >
                                    View Details
                                </button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="flex items-center justify-between px-5 py-4 text-sm text-gray-500">
                <span id="recordShowingCount">Showing 1 to {{ $records->count() }} of {{ $records->count() }} records</span>
                <span id="recordEmptyMessage" class="hidden text-orange-600">No matching records found.</span>
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
            Keep {{ $pet->name }}'s records up to date for better care and accurate treatment. For questions, contact our clinic.
        </div>
    </div>

    <aside class="owner-records-panel p-5">
        <div class="flex items-center justify-between">
            <h2 class="text-base font-bold text-gray-950">Activity Logs</h2>
            <span class="text-xs font-semibold text-orange-500">{{ $activityLogs->count() ? 'Recent' : '' }}</span>
        </div>

        <div class="mt-6 space-y-7">
            @forelse($activityLogs->take(6) as $log)
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
            @if($records->isNotEmpty())
                @foreach($records->take(5) as $record)
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
                        <p class="mt-2 text-sm leading-6 text-gray-600">{{ $record->title ?: 'Medical record' }} was added.</p>
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

<div id="petRecordModal" class="owner-record-modal hidden" role="dialog" aria-modal="true" aria-labelledby="petRecordModalTitle">
    <div class="max-h-[86vh] w-full max-w-3xl overflow-y-auto rounded-xl bg-white p-5 shadow-2xl md:p-6">
        <div class="flex items-start justify-between gap-4">
            <div>
                <p id="petRecordModalType" class="text-xs font-semibold uppercase tracking-wide text-orange-500">Medical Record</p>
                <h3 id="petRecordModalTitle" class="mt-1 text-xl font-bold text-gray-950">Medical Record</h3>
            </div>
            <button type="button" onclick="closePetRecordModal()" class="rounded-lg p-2 text-gray-400 hover:bg-gray-100 hover:text-gray-600">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18 18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>

        <div class="mt-5 rounded-lg bg-gray-50 p-4">
            <div class="flex items-center gap-2 text-sm">
                <span class="text-gray-500">Record Date:</span>
                <span id="petRecordModalDate" class="font-semibold text-gray-800">-</span>
            </div>

            <div class="mt-4 grid gap-3">
                <div>
                    <label class="mb-1 block text-xs font-semibold uppercase text-gray-500">Diagnosis</label>
                    <p id="petRecordModalDiagnosis" class="min-h-14 rounded-lg border border-gray-200 bg-white p-3 text-sm text-gray-700">-</p>
                </div>
                <div>
                    <label class="mb-1 block text-xs font-semibold uppercase text-gray-500">Treatment</label>
                    <p id="petRecordModalTreatment" class="min-h-14 rounded-lg border border-gray-200 bg-white p-3 text-sm text-gray-700">-</p>
                </div>
                <div>
                    <label class="mb-1 block text-xs font-semibold uppercase text-gray-500">Notes</label>
                    <p id="petRecordModalNotes" class="min-h-14 rounded-lg border border-gray-200 bg-white p-3 text-sm text-gray-700">-</p>
                </div>
                <div>
                    <label class="mb-1 block text-xs font-semibold uppercase text-gray-500">Next Due</label>
                    <p id="petRecordModalNextCall" class="rounded-lg border border-gray-200 bg-white p-3 text-sm font-semibold text-orange-600">-</p>
                </div>
            </div>
        </div>

        <div id="petRecordModalAttachmentWrapper" class="mt-4 hidden">
            <label class="mb-2 block text-xs font-semibold uppercase text-gray-500">Attachment Preview</label>
            <img id="petRecordModalAttachmentImage" src="" alt="Medical record attachment" class="hidden max-h-72 max-w-full rounded-lg border border-gray-200 bg-gray-50 object-contain">
            <div id="petRecordModalAttachmentFile" class="hidden rounded-lg border border-gray-200 bg-gray-50 px-4 py-3 text-sm font-semibold text-gray-700">
                Attachment available from the clinic record.
            </div>
        </div>

        <div class="mt-6 flex justify-end">
            <button type="button" onclick="closePetRecordModal()" class="rounded-lg bg-orange-500 px-6 py-2.5 text-sm font-semibold text-white hover:bg-orange-600">
                Close
            </button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function changePet(petId) {
    if (!petId) {
        return;
    }

    window.location.href = '{{ url('/pets') }}/' + petId + '/records';
}

function openPetRecordModal(element) {
    document.getElementById('petRecordModalTitle').textContent = element.dataset.recordTitle || 'Medical Record';
    document.getElementById('petRecordModalType').textContent = element.dataset.recordType || 'Medical Record';
    document.getElementById('petRecordModalDate').textContent = element.dataset.recordDate || '-';
    document.getElementById('petRecordModalDiagnosis').textContent = element.dataset.diagnosis || '-';
    document.getElementById('petRecordModalTreatment').textContent = element.dataset.treatment || '-';
    document.getElementById('petRecordModalNotes').textContent = element.dataset.notes || '-';
    document.getElementById('petRecordModalNextCall').textContent = element.dataset.nextcall || '-';

    const attachmentWrapper = document.getElementById('petRecordModalAttachmentWrapper');
    const attachmentImage = document.getElementById('petRecordModalAttachmentImage');
    const attachmentFile = document.getElementById('petRecordModalAttachmentFile');
    const attachmentUrl = element.dataset.attachmentUrl || '';
    const isImage = element.dataset.attachmentImage === '1';

    if (attachmentUrl) {
        attachmentWrapper.classList.remove('hidden');
        attachmentImage.classList.toggle('hidden', !isImage);
        attachmentFile.classList.toggle('hidden', isImage);
        attachmentImage.src = isImage ? attachmentUrl : '';
    } else {
        attachmentWrapper.classList.add('hidden');
        attachmentImage.src = '';
        attachmentImage.classList.add('hidden');
        attachmentFile.classList.add('hidden');
    }

    const modal = document.getElementById('petRecordModal');
    modal.classList.remove('hidden');
}

function closePetRecordModal() {
    const modal = document.getElementById('petRecordModal');
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
            ? 'Showing 1 to ' + visibleCount + ' of ' + rows.length + ' records'
            : 'Showing 0 of ' + rows.length + ' records';
    }

    if (emptyMessage) {
        emptyMessage.classList.toggle('hidden', visibleCount > 0);
    }
}

const recordSearch = document.getElementById('recordSearch');
const recordTypeFilter = document.getElementById('recordTypeFilter');
const recordSort = document.getElementById('recordSort');

if (recordSearch) {
    recordSearch.addEventListener('input', filterRecords);
}

if (recordTypeFilter) {
    recordTypeFilter.addEventListener('change', filterRecords);
}

if (recordSort) {
    recordSort.addEventListener('change', filterRecords);
}

const petRecordModal = document.getElementById('petRecordModal');
if (petRecordModal) {
    petRecordModal.addEventListener('click', function(event) {
        if (event.target === petRecordModal) {
            closePetRecordModal();
        }
    });
}

document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        closePetRecordModal();
    }
});

filterRecords();
</script>
@endpush
