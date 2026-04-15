@extends('layouts.dashboard')
@section('title', 'Users - VetCare Admin')
@section('header-title', 'User Management')

@section('content')
<div class="mb-4 flex flex-col md:flex-row md:items-center gap-3">
    <h3 class="text-base md:text-lg font-semibold text-gray-900">All Users</h3>
    <button onclick="openAdminModal()" class="inline-flex items-center px-4 py-2 bg-orange-500 text-white text-sm font-medium rounded-lg hover:bg-orange-600 transition">
        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
        </svg>
        Add Admin
    </button>
</div>

<div class="bg-white rounded-xl shadow-sm overflow-hidden">
    <div class="md:hidden p-4 space-y-3">
        @forelse($users as $user)
        <div class="border border-gray-200 rounded-xl p-4">
            <div class="flex items-center gap-3 mb-3">
                <div class="w-10 h-10 bg-orange-100 rounded-full flex items-center justify-center flex-shrink-0">
                    <span class="text-orange-500 font-semibold text-sm">{{ substr($user->name, 0, 1) }}</span>
                </div>
                <div class="min-w-0 flex-1">
                    <p class="font-medium text-gray-900 truncate">{{ $user->name }}</p>
                    <p class="text-xs text-gray-500 truncate">{{ $user->email }}</p>
                </div>
            </div>
            <div class="flex items-center gap-2 mb-3">
                <span class="inline-flex px-2 py-0.5 text-xs font-medium rounded-full {{ $user->role === 'admin' ? 'bg-purple-100 text-purple-700' : 'bg-blue-100 text-blue-700' }}">
                    {{ ucfirst($user->role) }}
                </span>
                <span class="text-xs text-gray-400">{{ $user->created_at->format('M d, Y') }}</span>
            </div>
            <div class="flex gap-2">
                @if($user->pets->count() > 0)
                <button onclick="openPetsModal({{ $user->id }}, '{{ $user->name }}')" class="flex-1 text-xs bg-orange-100 text-orange-700 px-3 py-2 rounded-lg hover:bg-orange-200">Pets ({{ $user->pets->count() }})</button>
                @else
                <span class="flex-1 text-xs text-gray-400 text-center py-2">No Pets</span>
                @endif
                @if($user->id !== auth()->id())
                <button onclick="openDeleteUserModal({{ $user->id }}, '{{ $user->name }}')" class="flex-1 text-xs bg-red-100 text-red-700 px-3 py-2 rounded-lg hover:bg-red-200">Delete</button>
                @else
                <span class="flex-1 text-xs text-gray-400 text-center py-2">Current</span>
                @endif
            </div>
        </div>
        @empty
        <p class="text-center text-gray-500 py-8">No users found</p>
        @endforelse
    </div>
    
    <div class="hidden md:block overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">User</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Role</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Joined</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Pets</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($users as $user)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-orange-100 rounded-full flex items-center justify-center mr-3">
                                <span class="text-orange-500 font-semibold text-sm">{{ substr($user->name, 0, 1) }}</span>
                            </div>
                            <span class="font-medium text-gray-900">{{ $user->name }}</span>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-600">{{ $user->email }}</td>
                    <td class="px-6 py-4">
                        <span class="inline-flex px-3 py-1 text-xs font-medium rounded-full {{ $user->role === 'admin' ? 'bg-purple-100 text-purple-700' : 'bg-blue-100 text-blue-700' }}">
                            {{ ucfirst($user->role) }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-600">{{ $user->created_at->format('M d, Y') }}</td>
                    <td class="px-6 py-4">
                        @if($user->pets->count() > 0)
                        <button onclick="openPetsModal({{ $user->id }}, '{{ $user->name }}')" class="text-xs bg-orange-100 text-orange-700 px-3 py-1 rounded-lg hover:bg-orange-200">View ({{ $user->pets->count() }})</button>
                        @else
                        <span class="text-xs text-gray-400">No Pets</span>
                        @endif
                    </td>
                    <td class="px-6 py-4">
                        @if($user->id !== auth()->id())
                        <button onclick="openDeleteUserModal({{ $user->id }}, '{{ $user->name }}')" class="text-xs bg-red-100 text-red-700 px-3 py-1 rounded-lg hover:bg-red-200">Delete</button>
                        @else
                        <span class="text-xs text-gray-400">Current User</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-12 text-center">
                        <p class="text-gray-500">No users found</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@if($users->hasPages())
<div class="px-4 md:px-6 py-4">
    {{ $users->links() }}
</div>
@endif

<div id="adminModal" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50 p-4">
    <div class="bg-white rounded-2xl p-4 md:p-6 w-full max-w-md">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-semibold text-gray-900">Add Admin User</h3>
            <button onclick="closeAdminModal()" class="text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
        <form action="{{ route('admin.users.create-admin') }}" method="POST">
            @csrf
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Name</label>
                    <input type="text" name="name" required class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                    <input type="email" name="email" required class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                    <input type="password" name="password" required minlength="8" class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Confirm Password</label>
                    <input type="password" name="password_confirmation" required minlength="8" class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm">
                </div>
            </div>
            <div class="flex justify-end space-x-3 mt-6">
                <button type="button" onclick="closeAdminModal()" class="px-4 py-2 text-gray-600 hover:text-gray-800 text-sm">Cancel</button>
                <button type="submit" class="px-6 py-2 bg-orange-500 text-white font-medium rounded-lg hover:bg-orange-600 text-sm">Create</button>
            </div>
        </form>
    </div>
</div>

<div id="petsModal" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50 p-4">
    <div class="bg-white rounded-2xl p-4 md:p-6 w-full max-w-md max-h-[80vh] overflow-y-auto">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-semibold text-gray-900" id="petsModalTitle">User's Pets</h3>
            <button onclick="closePetsModal()" class="text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
        <div id="petsList" class="space-y-3 max-h-64 overflow-y-auto"></div>
    </div>
</div>

<div id="deleteUserModal" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50 p-4">
    <div class="bg-white rounded-2xl p-4 md:p-6 w-full max-w-md">
        <div class="text-center">
            <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-6 h-6 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                </svg>
            </div>
            <h3 class="text-lg font-semibold text-gray-900 mb-2">Delete User</h3>
            <p class="text-gray-500 mb-6 text-sm" id="deleteUserMessage">Are you sure you want to delete this user?</p>
            <div class="flex justify-center gap-3">
                <button type="button" onclick="closeDeleteUserModal()" class="px-5 py-2.5 bg-gray-100 text-gray-700 font-medium rounded-lg hover:bg-gray-200 transition">Cancel</button>
                <form id="deleteUserForm" method="POST">
                    @csrf @method('DELETE')
                    <button type="submit" class="px-5 py-2.5 bg-red-500 text-white font-medium rounded-lg hover:bg-red-600 transition">Delete</button>
                </form>
            </div>
        </div>
    </div>
</div>

<div id="deletePetModal" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50 p-4">
    <div class="bg-white rounded-2xl p-4 md:p-6 w-full max-w-md">
        <div class="text-center">
            <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-6 h-6 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                </svg>
            </div>
            <h3 class="text-lg font-semibold text-gray-900 mb-2">Delete Pet</h3>
            <p class="text-gray-500 mb-6 text-sm" id="deletePetMessage">Are you sure?</p>
            <div class="flex justify-center gap-3">
                <button type="button" onclick="closeDeletePetModal()" class="px-5 py-2.5 bg-gray-100 text-gray-700 font-medium rounded-lg hover:bg-gray-200 transition">Cancel</button>
                <form id="deletePetForm" method="POST">
                    @csrf @method('DELETE')
                    <button type="submit" class="px-5 py-2.5 bg-red-500 text-white font-medium rounded-lg hover:bg-red-600 transition">Delete</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function openAdminModal() {
    document.getElementById('adminModal').classList.remove('hidden');
    document.getElementById('adminModal').classList.add('flex');
}
function closeAdminModal() {
    document.getElementById('adminModal').classList.add('hidden');
    document.getElementById('adminModal').classList.remove('flex');
}

function openPetsModal(userId, userName) {
    document.getElementById('petsModalTitle').textContent = userName + "'s Pets";
    var petsList = document.getElementById('petsList');
    petsList.innerHTML = '<p class="text-gray-500 text-center py-4">Loading...</p>';
    document.getElementById('petsModal').classList.remove('hidden');
    document.getElementById('petsModal').classList.add('flex');
    fetch('/admin/users/' + userId + '/pets').then(r => r.text()).then(html => { petsList.innerHTML = html; }).catch(() => { petsList.innerHTML = '<p class="text-gray-500 text-center py-4">Error</p>'; });
}
function closePetsModal() {
    document.getElementById('petsModal').classList.add('hidden');
    document.getElementById('petsModal').classList.remove('flex');
}

function openDeleteUserModal(userId, userName) {
    document.getElementById('deleteUserMessage').textContent = 'Delete ' + userName + '? This will also delete all their pets.';
    document.getElementById('deleteUserForm').action = '/admin/users/' + userId;
    document.getElementById('deleteUserModal').classList.remove('hidden');
    document.getElementById('deleteUserModal').classList.add('flex');
}
function closeDeleteUserModal() {
    document.getElementById('deleteUserModal').classList.add('hidden');
    document.getElementById('deleteUserModal').classList.remove('flex');
}

function openDeletePetModal(petId, petName) {
    document.getElementById('deletePetMessage').textContent = 'Delete ' + petName + '? This will also delete all medical records.';
    document.getElementById('deletePetForm').action = '/admin/pets/' + petId;
    document.getElementById('deletePetModal').classList.remove('hidden');
    document.getElementById('deletePetModal').classList.add('flex');
}
function closeDeletePetModal() {
    document.getElementById('deletePetModal').classList.add('hidden');
    document.getElementById('deletePetModal').classList.remove('flex');
}
</script>
@endpush