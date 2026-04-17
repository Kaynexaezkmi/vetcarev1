@extends('layouts.dashboard')
@section('title', 'Services - VetCare Admin')
@section('header-title', 'Manage Services')

@section('content')
@if(session('success'))
<div class="mb-4 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl">
    {{ session('success') }}
</div>
@endif

<div class="mb-4">
    <button onclick="openServiceModal()" class="inline-flex items-center px-4 py-2 bg-orange-500 text-white text-sm font-medium rounded-lg hover:bg-orange-600 transition">
        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
        </svg>
        Add New Service
    </button>
</div>

<div class="bg-white rounded-xl shadow-sm overflow-hidden">
    <div class="md:hidden p-4 space-y-3">
        @forelse($services as $service)
        <div class="border border-gray-200 rounded-xl p-4">
            <div class="flex items-start gap-3">
                @if($service->image && file_exists(public_path($service->image)))
                    <img src="{{ asset($service->image) }}" alt="{{ $service->name }}" class="w-12 h-12 rounded-lg object-cover flex-shrink-0">
                @else
                    <div class="w-12 h-12 rounded-lg bg-orange-100 flex items-center justify-center flex-shrink-0">
                        <svg class="w-6 h-6 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                        </svg>
                    </div>
                @endif
                <div class="flex-1 min-w-0">
                    <p class="font-medium text-gray-900">{{ $service->name }}</p>
                    <p class="text-xs text-gray-500">{{ $service->description ? Str::limit($service->description, 40) : 'No description' }}</p>
                    <div class="flex items-center gap-2 mt-2">
                        <span class="text-xs text-gray-500">{{ $service->duration }}</span>
                        <span class="inline-flex px-2 py-0.5 text-xs font-medium rounded-full {{ $service->is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-700' }}">
                            {{ $service->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </div>
                </div>
            </div>
            <div class="flex gap-2 mt-3">
                <button onclick="openEditServiceModal({{ $service->id }}, '{{ addslashes($service->name) }}', '{{ addslashes($service->description ?? '') }}', '{{ addslashes($service->duration ?? '') }}', {{ $service->is_active ? 'true' : 'false' }})" class="flex-1 text-xs bg-blue-100 text-blue-700 px-3 py-2 rounded-lg hover:bg-blue-200">Edit</button>
                <form action="{{ route('admin.services.delete', $service) }}" method="POST" class="flex-1" id="deleteServiceFormMobile{{ $service->id }}">
                    @csrf @method('delete')
                    <button type="button" class="w-full text-xs bg-red-100 text-red-700 px-3 py-2 rounded-lg hover:bg-red-200" onclick="openDeleteServiceModal('deleteServiceFormMobile{{ $service->id }}', '{{ addslashes($service->name) }}')">Delete</button>
                </form>
            </div>
        </div>
        @empty
        <p class="text-center text-gray-500 py-8">No services found</p>
        @endforelse
    </div>
    
    <div class="hidden md:block overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Image</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Service</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Description</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Duration</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($services as $service)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4">
                        @if($service->image && file_exists(public_path($service->image)))
                            <img src="{{ asset($service->image) }}" alt="{{ $service->name }}" class="w-12 h-12 rounded-lg object-cover">
                        @else
                            <div class="w-12 h-12 rounded-lg bg-orange-100 flex items-center justify-center">
                                <svg class="w-6 h-6 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                                </svg>
                            </div>
                        @endif
                    </td>
                    <td class="px-6 py-4">
                        <p class="font-medium text-gray-900">{{ $service->name }}</p>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-600">
                        {{ $service->description ? Str::limit($service->description, 50) : 'No description' }}
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-600">{{ $service->duration }}</td>
                    <td class="px-6 py-4">
                        <span class="inline-flex px-3 py-1 text-xs font-medium rounded-full {{ $service->is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-700' }}">
                            {{ $service->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        <button onclick="openEditServiceModal({{ $service->id }}, '{{ addslashes($service->name) }}', '{{ addslashes($service->description ?? '') }}', '{{ addslashes($service->duration ?? '') }}', {{ $service->is_active ? 'true' : 'false' }})" class="text-xs bg-blue-100 text-blue-700 px-3 py-1 rounded-lg hover:bg-blue-200 mr-2">Edit</button>
                        <form action="{{ route('admin.services.delete', $service) }}" method="POST" class="inline" id="deleteServiceFormDesktop{{ $service->id }}">
                            @csrf @method('delete')
                            <button type="button" class="text-xs bg-red-100 text-red-700 px-3 py-1 rounded-lg hover:bg-red-200" onclick="openDeleteServiceModal('deleteServiceFormDesktop{{ $service->id }}', '{{ addslashes($service->name) }}')">Delete</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-12 text-center">
                        <p class="text-gray-500">No services found</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div id="serviceModal" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50 p-4">
    <div class="bg-white rounded-2xl p-4 md:p-6 w-full max-w-md">
        <div class="flex justify-between items-center mb-4">
            <h3 id="modalTitle" class="text-lg font-semibold text-gray-900">Add New Service</h3>
            <button onclick="closeServiceModal()" class="text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
        <form id="serviceForm" method="POST" action="" enctype="multipart/form-data">
            @csrf
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Service Image</label>
                    <input type="file" name="image" id="serviceImage" accept="image/*" class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Service Name</label>
                    <input type="text" name="name" id="serviceName" required class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                    <textarea name="description" id="serviceDesc" rows="2" class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm"></textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Duration</label>
                    <input type="text" name="duration" id="serviceDuration" class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm">
                </div>
                <div>
                    <label class="flex items-center">
                        <input type="checkbox" name="is_active" id="serviceActive" value="1" checked class="w-4 h-4 text-orange-500 rounded">
                        <span class="ml-2 text-sm text-gray-700">Active</span>
                    </label>
                </div>
            </div>
            <div class="flex justify-end space-x-3 mt-6">
                <button type="button" onclick="closeServiceModal()" class="px-4 py-2 text-gray-600 hover:text-gray-800 text-sm">Cancel</button>
                <button type="submit" id="saveBtn" class="px-6 py-2 bg-orange-500 text-white font-medium rounded-lg hover:bg-orange-600 text-sm">Save</button>
            </div>
        </form>
    </div>
</div>

<div id="deleteServiceModal" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50 p-4">
    <div class="bg-white rounded-2xl p-4 md:p-6 w-full max-w-md">
        <div class="flex items-center gap-3 mb-4">
            <div class="w-12 h-12 rounded-full bg-red-100 flex items-center justify-center">
                <svg class="w-6 h-6 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                </svg>
            </div>
            <div>
                <h3 class="text-lg font-semibold text-gray-900">Delete Service</h3>
                <p class="text-sm text-gray-500">This will permanently remove the service.</p>
            </div>
        </div>
        <div class="bg-gray-50 rounded-lg p-3 mb-4">
            <p class="text-sm text-gray-700"><strong>Service:</strong> <span id="deleteServiceName"></span></p>
        </div>
        <div class="flex flex-col md:flex-row justify-end gap-2 md:space-x-3">
            <button type="button" onclick="closeDeleteServiceModal()" class="px-4 py-2 text-gray-600 hover:text-gray-800 w-full md:w-auto">Cancel</button>
            <button type="button" onclick="submitDeleteService()" class="px-6 py-2 bg-red-500 text-white font-medium rounded-lg hover:bg-red-600 w-full md:w-auto">Delete Service</button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
var serviceModal = document.getElementById('serviceModal');
var saveBtn = document.getElementById('saveBtn');
var deleteServiceFormId = null;

serviceModal.addEventListener('submit', function() {
    saveBtn.disabled = true;
    saveBtn.textContent = 'Saving...';
    saveBtn.classList.add('opacity-50', 'cursor-not-allowed');
});

function openServiceModal() {
    document.getElementById('modalTitle').textContent = 'Add New Service';
    var form = document.getElementById('serviceForm');
    form.action = '{{ route('admin.services.store') }}';
    var methodInput = form.querySelector('input[name="_method"]');
    if (methodInput) methodInput.remove();
    document.getElementById('serviceName').value = '';
    document.getElementById('serviceDesc').value = '';
    document.getElementById('serviceDuration').value = '';
    document.getElementById('serviceActive').checked = true;
    document.getElementById('serviceModal').classList.remove('hidden');
    document.getElementById('serviceModal').classList.add('flex');
}

function openEditServiceModal(id, name, desc, duration, isActive) {
    document.getElementById('modalTitle').textContent = 'Edit Service';
    var form = document.getElementById('serviceForm');
    form.action = '/admin/services/' + id;
    var methodInput = form.querySelector('input[name="_method"]');
    if (methodInput) methodInput.value = 'PUT';
    else { var input = document.createElement('input'); input.type = 'hidden'; input.name = '_method'; input.value = 'PUT'; form.appendChild(input); }
    document.getElementById('serviceName').value = name;
    document.getElementById('serviceDesc').value = desc || '';
    document.getElementById('serviceDuration').value = duration || '';
    document.getElementById('serviceActive').checked = isActive;
    document.getElementById('serviceModal').classList.remove('hidden');
    document.getElementById('serviceModal').classList.add('flex');
}

function closeServiceModal() {
    document.getElementById('serviceModal').classList.add('hidden');
    document.getElementById('serviceModal').classList.remove('flex');
}

function openDeleteServiceModal(formId, serviceName) {
    deleteServiceFormId = formId;
    document.getElementById('deleteServiceName').textContent = serviceName;
    document.getElementById('deleteServiceModal').classList.remove('hidden');
    document.getElementById('deleteServiceModal').classList.add('flex');
}

function closeDeleteServiceModal() {
    deleteServiceFormId = null;
    document.getElementById('deleteServiceModal').classList.add('hidden');
    document.getElementById('deleteServiceModal').classList.remove('flex');
}

function submitDeleteService() {
    if (deleteServiceFormId) {
        document.getElementById(deleteServiceFormId).submit();
    }
}
</script>
@endpush
