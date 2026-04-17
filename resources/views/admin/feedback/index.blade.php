@extends('layouts.dashboard')
@section('title', 'Feedback - VetCare Admin')
@section('header-title', 'Feedback Management')

@section('content')
@if(Session::has('success'))
<div class="mb-4 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl">
    {{ Session::get('success') }}
</div>
@endif

<div class="bg-white rounded-xl shadow-sm p-4 mb-4 md:mb-6">
    <form action="{{ route('admin.feedback.index') }}" method="GET" class="flex items-center gap-2" id="searchForm">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search..." class="w-30 sm:w-48 px-3 py-1.5 rounded-lg border border-gray-200 text-sm focus:ring-2 focus:ring-orange-500 focus:border-orange-500" id="searchInput">
        @if(request('search'))
        <a href="{{ route('admin.feedback.index') }}" class="px-2 py-1.5 text-gray-500 hover:text-gray-700 text-sm">Clear</a>
        @endif
    </form>
</div>

<div class="bg-white rounded-xl shadow-sm overflow-hidden">
    <div class="md:hidden p-4 space-y-3">
        @foreach($feedback as $fb)
        <div class="border border-gray-200 rounded-xl p-4">
            <div class="flex items-center justify-between mb-2">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 bg-orange-100 rounded-full flex items-center justify-center">
                        <span class="text-orange-500 font-semibold text-sm">{{ substr($fb->user->name, 0, 1) }}</span>
                    </div>
                    <div>
                        <p class="font-medium text-gray-900 text-sm">{{ $fb->user->name }}</p>
                        <p class="text-xs text-gray-500">{{ $fb->user->email }}</p>
                    </div>
                </div>
                <span class="text-xs text-gray-400">{{ $fb->created_at->format('M d') }}</span>
            </div>
            <div class="flex items-center gap-1 mb-2">
                @for($i = 0; $i < 5; $i++)
                <svg class="w-3 h-3 {{ $i < $fb->rating ? 'text-orange-400' : 'text-gray-300' }}" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                </svg>
                @endfor
            </div>
            <p class="text-sm text-gray-600 line-clamp-2 mb-2">{{ $fb->message }}</p>
            <button onclick="openViewFeedbackModal({{ $fb->id }})" class="w-full text-center text-sm text-orange-500 hover:text-orange-600 font-medium py-2 border border-orange-500 rounded-lg">View</button>
        </div>
        @endforeach
    </div>
    
    <div class="hidden md:block overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-4 text-left text-sm font-semibold text-gray-900">User</th>
                    <th class="px-6 py-4 text-left text-sm font-semibold text-gray-900">Rating</th>
                    <th class="px-6 py-4 text-left text-sm font-semibold text-gray-900">Message</th>
                    <th class="px-6 py-4 text-left text-sm font-semibold text-gray-900">Date</th>
                    <th class="px-6 py-4 text-right text-sm font-semibold text-gray-900">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @foreach($feedback as $fb)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-orange-100 rounded-full flex items-center justify-center">
                                <span class="text-orange-500 font-semibold">{{ substr($fb->user->name, 0, 1) }}</span>
                            </div>
                            <div>
                                <p class="font-medium text-gray-900">{{ $fb->user->name }}</p>
                                <p class="text-sm text-gray-500">{{ $fb->user->email }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-1">
                            @for($i = 0; $i < 5; $i++)
                            <svg class="w-4 h-4 {{ $i < $fb->rating ? 'text-orange-400' : 'text-gray-300' }}" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                            </svg>
                            @endfor
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <p class="text-gray-600 max-w-xs truncate">{{ $fb->message }}</p>
                        @if($fb->replies->count() > 0)
                        <p class="text-xs text-orange-500 mt-1">{{ $fb->replies->count() }} replies</p>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-500">
                        {{ $fb->created_at->format('M d, Y') }}
                    </td>
                    <td class="px-6 py-4 text-right">
                        <button onclick="openViewFeedbackModal({{ $fb->id }})" class="text-orange-500 hover:text-orange-600 text-sm font-medium">View</button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    
    @if($feedback->isEmpty())
    <div class="p-8 md:p-12 text-center">
        <p class="text-gray-500">No feedback found</p>
    </div>
    @endif
</div>

<div class="mt-4">
    {{ $feedback->links() }}
</div>

<div id="viewFeedbackModal" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50 p-4">
    <div class="bg-white rounded-2xl p-4 md:p-6 w-full max-w-2xl max-h-[80vh] overflow-y-auto">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-semibold text-gray-900">Feedback Details</h3>
            <button onclick="closeViewFeedbackModal()" class="text-gray-400 hover:text-gray-600 p-1">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4l16 16M4 20L20 4" stroke="currentColor"></path>
                </svg>
            </button>
        </div>
        <div id="feedbackContent"></div>
        
        <div class="mt-4 pt-4 border-t">
            <h4 class="text-sm font-medium text-gray-900 mb-2">Reply</h4>
            <form id="replyForm" method="POST" class="flex gap-2">
                @csrf
                <input type="text" name="message" placeholder="Write a reply..." class="flex-1 px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-orange-500 focus:border-orange-500">
                <button type="submit" class="px-4 py-2 bg-orange-500 text-white rounded-lg hover:bg-orange-600 text-sm font-medium">Reply</button>
            </form>
        </div>
        
        <div class="flex justify-end gap-2 mt-4 pt-4 border-t">
            <button onclick="closeViewFeedbackModal(); setTimeout(() => openEditFeedbackModal(currentFeedbackId), 100);" class="px-4 py-2 text-blue-500 border border-blue-500 rounded-lg hover:bg-blue-50 text-sm font-medium">Edit</button>
            <button onclick="closeViewFeedbackModal(); setTimeout(() => openDeleteFeedbackModal(currentFeedbackId), 100);" class="px-4 py-2 text-red-500 border border-red-500 rounded-lg hover:bg-red-50 text-sm font-medium">Delete</button>
        </div>
    </div>
</div>

<div id="deleteFeedbackModal" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50 p-4">
    <div class="bg-white rounded-2xl p-4 md:p-6 w-full max-w-md">
        <div class="text-center">
            <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-6 h-6 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                </svg>
            </div>
            <h3 class="text-lg font-semibold text-gray-900 mb-2">Delete Feedback</h3>
            <p class="text-gray-500 mb-6 text-sm">Are you sure you want to delete this feedback?</p>
            <div class="flex justify-center gap-3">
                <button type="button" onclick="closeDeleteFeedbackModal()" class="px-5 py-2.5 bg-gray-100 text-gray-700 font-medium rounded-lg hover:bg-gray-200 transition">Cancel</button>
                <form id="deleteFeedbackForm" method="POST">
                    @csrf @method('delete')
                    <button type="submit" class="px-5 py-2.5 bg-red-500 text-white font-medium rounded-lg hover:bg-red-600 transition">Delete</button>
                </form>
            </div>
        </div>
    </div>
</div>

<div id="editFeedbackModal" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50 p-4">
    <div class="bg-white rounded-2xl p-4 md:p-6 w-full max-w-md">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-semibold text-gray-900">Edit Feedback</h3>
            <button type="button" onclick="closeEditFeedbackModal()" class="text-gray-400 hover:text-gray-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
        <form id="editFeedbackForm" method="POST">
            @csrf @method('put')
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Rating</label>
                <div class="flex gap-1" id="ratingStars">
                    @for($i = 1; $i <= 5; $i++)
                    <button type="button" class="star-btn text-gray-300 hover:text-orange-400" data-rating="{{ $i }}">
                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                        </svg>
                    </button>
                    @endfor
                </div>
                <input type="hidden" name="rating" id="editRating" value="5">
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Message</label>
                <textarea name="message" id="editMessage" rows="4" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-orange-500 focus:border-orange-500"></textarea>
            </div>
            <div class="flex justify-end gap-2">
                <button type="button" onclick="closeEditFeedbackModal()" class="px-4 py-2 bg-gray-100 text-gray-700 font-medium rounded-lg hover:bg-gray-200 transition">Cancel</button>
                <button type="submit" class="px-4 py-2 bg-orange-500 text-white font-medium rounded-lg hover:bg-orange-600 transition">Save</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
const feedbackData = @json($feedback->toArray()['data'] ?? []);
let currentFeedbackId = null;

function openViewFeedbackModal(id) {
    currentFeedbackId = id;
    const fb = feedbackData.find(f => f.id === id);
    if (!fb) return;
    
    const user = fb.user;
    let repliesHtml = '';
    if (fb.replies && fb.replies.length > 0) {
        fb.replies.forEach(reply => {
            const isAdmin = reply.user && reply.user.role === 'admin';
            repliesHtml += `
                <div class="bg-gray-50 rounded-lg p-3 mt-3">
                    <div class="flex items-center gap-2 mb-1">
                        <span class="font-medium text-sm text-gray-900">${reply.user ? reply.user.name : 'Unknown'}</span>
                        ${isAdmin ? '<span class="text-xs bg-orange-100 text-orange-600 px-2 py-0.5 rounded">Admin</span>' : ''}
                        <span class="text-xs text-gray-400">${new Date(reply.created_at).toLocaleDateString()}</span>
                    </div>
                    <p class="text-sm text-gray-600">${reply.message}</p>
                </div>
            `;
        });
    }
    
    let stars = '';
    for (let i = 0; i < 5; i++) {
        stars += `<svg class="w-5 h-5 ${i < fb.rating ? 'text-orange-400' : 'text-gray-300'}" fill="currentColor" viewBox="0 0 20 20">
            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
        </svg>`;
    }
    
    document.getElementById('feedbackContent').innerHTML = `
        <div class="bg-orange-50 rounded-xl p-4 mb-4">
            <div class="flex items-center gap-3 mb-3">
                <div class="w-12 h-12 bg-orange-100 rounded-full flex items-center justify-center">
                    <span class="text-orange-500 font-semibold text-lg">${user.name.charAt(0)}</span>
                </div>
                <div>
                    <p class="font-semibold text-gray-900">${user.name}</p>
                    <p class="text-sm text-gray-500">${user.email}</p>
                </div>
            </div>
            <div class="flex items-center gap-2 mb-2">${stars}</div>
            <p class="text-gray-700">${fb.message}</p>
            <p class="text-xs text-gray-400 mt-2">Posted on ${new Date(fb.created_at).toLocaleDateString()}</p>
        </div>
        ${repliesHtml}
    `;
    
    document.getElementById('viewFeedbackModal').classList.remove('hidden');
    document.getElementById('viewFeedbackModal').classList.add('flex');
}

function closeViewFeedbackModal() {
    document.getElementById('viewFeedbackModal').classList.add('hidden');
    document.getElementById('viewFeedbackModal').classList.remove('flex');
    document.getElementById('feedbackContent').innerHTML = '';
}

function openDeleteFeedbackModal(id) {
    document.getElementById('deleteFeedbackForm').action = '/admin/feedback/' + id;
    document.getElementById('deleteFeedbackModal').classList.remove('hidden');
    document.getElementById('deleteFeedbackModal').classList.add('flex');
}

function closeDeleteFeedbackModal() {
    document.getElementById('deleteFeedbackModal').classList.add('hidden');
    document.getElementById('deleteFeedbackModal').classList.remove('flex');
}

function openEditFeedbackModal(id) {
    const fb = feedbackData.find(f => f.id === id);
    if (!fb) return;
    
    document.getElementById('editFeedbackForm').action = '/admin/feedback/' + id;
    document.getElementById('editRating').value = fb.rating;
    document.getElementById('editMessage').value = fb.message;
    
    updateRatingStars(fb.rating);
    
    document.getElementById('editFeedbackModal').classList.remove('hidden');
    document.getElementById('editFeedbackModal').classList.add('flex');
}

function closeEditFeedbackModal() {
    document.getElementById('editFeedbackModal').classList.add('hidden');
    document.getElementById('editFeedbackModal').classList.remove('flex');
}

function updateRatingStars(rating) {
    const stars = document.querySelectorAll('#ratingStars .star-btn');
    stars.forEach((star, index) => {
        if (index < rating) {
            star.classList.remove('text-gray-300');
            star.classList.add('text-orange-400');
        } else {
            star.classList.remove('text-orange-400');
            star.classList.add('text-gray-300');
        }
    });
}

document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchInput');
    let timeout = null;
    searchInput.addEventListener('input', function() {
        clearTimeout(timeout);
        timeout = setTimeout(function() {
            document.getElementById('searchForm').submit();
        }, 500);
    });
    
    document.querySelectorAll('#ratingStars .star-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const rating = parseInt(this.dataset.rating);
            document.getElementById('editRating').value = rating;
            updateRatingStars(rating);
        });
    });
    
    document.getElementById('replyForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const input = this.querySelector('input[name="message"]');
        const message = input.value.trim();
        if (!message || !currentFeedbackId) return;
        
        fetch('/admin/feedback/' + currentFeedbackId + '/reply', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: 'message=' + encodeURIComponent(message)
        })
        .then(response => {
            if (response.ok) {
                input.value = '';
                location.reload();
            }
        });
    });
});
</script>
@endpush