@extends('layouts.dashboard')
@section('title', 'Feedback - VetCare')
@section('header-title', 'Feedback')

@section('content')
@if(Session::has('success'))
<div class="mb-4 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl">
    {{ Session::get('success') }}
</div>
@endif

<div class="bg-white rounded-xl shadow-sm p-4 md:p-6 mb-4 md:mb-6">
    <h3 class="text-base md:text-lg font-semibold text-gray-900 mb-3 md:mb-4">Share Your Feedback</h3>
    
    <form action="{{ route('feedback.store') }}" method="POST" class="space-y-3 md:space-y-4">
        @csrf
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1 md:mb-2">Rating</label>
            <div class="flex gap-2" id="starRating">
                @for($i = 1; $i <= 5; $i++)
                <button type="button" onclick="setRating({{ $i }})" class="star-btn p-1" data-rating="{{ $i }}">
                    <svg class="w-6 md:w-8 h-6 md:h-8 text-gray-300 hover:text-orange-400 transition-colors" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                    </svg>
                </button>
                @endfor
            </div>
            <input type="hidden" name="rating" id="selectedRating" required>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1 md:mb-2">Your Message</label>
            <textarea name="message" rows="3" class="w-full px-3 md:px-4 py-2 md:py-3 rounded-xl border border-gray-200 text-sm md:text-base" placeholder="Tell us about your experience..." required></textarea>
        </div>
        <button type="submit" class="px-6 py-2.5 bg-orange-500 text-white font-medium rounded-lg hover:bg-orange-600 transition-colors text-sm md:text-base w-full md:w-auto">Submit Feedback</button>
    </form>
</div>

<div class="bg-white rounded-xl shadow-sm p-4 md:p-6">
    <h3 class="text-base md:text-lg font-semibold text-gray-900 mb-4 md:mb-6">What Pet Owners Say</h3>
    <div class="space-y-4 md:space-y-6">
        @foreach($allFeedback as $fb)
        <div class="border border-gray-100 rounded-xl p-3 md:p-4">
            <div class="flex items-start justify-between">
                <div class="flex items-center gap-2 md:gap-3">
                    <div class="w-8 md:w-10 h-8 md:h-10 bg-orange-100 rounded-full flex items-center justify-center flex-shrink-0">
                        <span class="text-orange-500 font-semibold text-sm">{{ substr($fb->user->name, 0, 1) }}</span>
                    </div>
                    <div>
                        <p class="font-medium text-gray-900 text-sm">{{ $fb->user->name }}</p>
                        <div class="flex items-center gap-1">
                            @for($i = 0; $i < 5; $i++)
                            <svg class="w-3 h-3 {{ $i < $fb->rating ? 'text-orange-400' : 'text-gray-300' }}" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                            </svg>
                            @endfor
                        </div>
                    </div>
                </div>
                <div class="flex items-center gap-1 md:gap-2">
                    <span class="text-xs text-gray-400">{{ $fb->created_at->diffForHumans() }}</span>
                    @if($fb->user_id === auth()->id())
                    <button onclick="openEditFeedbackModal({{ $fb->id }}, {{ $fb->rating }}, '{{ addslashes($fb->message) }}')" class="text-gray-400 hover:text-orange-500 p-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path>
                        </svg>
                    </button>
                    <form action="{{ route('feedback.destroy', $fb) }}" method="POST" class="inline">
                        @csrf @method('delete')
                        <button type="submit" class="text-gray-400 hover:text-red-500 p-1" onclick="return confirm('Delete?')">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                            </svg>
                        </button>
                    </form>
                    @endif
                </div>
            </div>
            <p class="text-gray-600 mt-2 md:mt-3 text-sm">{{ $fb->message }}</p>
            
            @if($fb->replies->count() > 0)
            <div class="mt-3 md:mt-4 pl-3 md:pl-4 border-l-2 border-orange-200 space-y-2 md:space-y-3">
                @foreach($fb->replies as $reply)
                <div class="bg-gray-50 rounded-lg p-2 md:p-3">
                    <div class="flex items-center gap-1 md:gap-2 mb-1">
                        <span class="font-medium text-xs md:text-sm text-gray-900">{{ $reply->user->name }}</span>
                        @if($reply->user->role === 'admin')
                        <span class="text-xs bg-orange-100 text-orange-600 px-1.5 py-0.5 rounded">Admin</span>
                        @endif
                        <span class="text-xs text-gray-400">{{ $reply->created_at->diffForHumans() }}</span>
                    </div>
                    <p class="text-xs md:text-sm text-gray-600">{{ $reply->message }}</p>
                </div>
                @endforeach
            </div>
            @endif
            
            <button onclick="toggleReplyForm({{ $fb->id }})" class="mt-2 md:mt-3 text-xs md:text-sm text-orange-500 hover:text-orange-600 font-medium">Reply</button>
            <div id="replyForm{{ $fb->id }}" class="hidden mt-2 md:mt-3">
                <form action="{{ route('feedback.reply', $fb) }}" method="POST" class="flex flex-col md:flex-row gap-2 md:gap-2">
                    @csrf
                    <input type="text" name="message" placeholder="Write a reply..." class="flex-1 px-3 py-2 rounded-lg border border-gray-200 text-sm" required>
                    <button type="submit" class="px-4 py-2 bg-orange-500 text-white text-sm rounded-lg hover:bg-orange-600 w-full md:w-auto">Reply</button>
                </form>
            </div>
        </div>
        @endforeach
    </div>
    @if($allFeedback->hasPages())
    <div class="mt-4 md:mt-6">
        {{ $allFeedback->links() }}
    </div>
    @endif
</div>

<div id="editFeedbackModal" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50 p-4">
    <div class="bg-white rounded-2xl p-4 md:p-6 w-full max-w-md">
        <div class="flex justify-between items-center mb-4 md:mb-5">
            <h3 class="text-lg font-semibold text-gray-900">Edit Feedback</h3>
            <button onclick="closeEditFeedbackModal()" class="text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
        <form id="editFeedbackForm" method="POST">
            @csrf @method('put')
            <div class="mb-3 md:mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1 md:mb-2">Rating</label>
                <div class="flex gap-2" id="editStarRating">
                    @for($i = 1; $i <= 5; $i++)
                    <button type="button" onclick="setEditRating({{ $i }})" class="edit-star-btn p-1" data-rating="{{ $i }}">
                        <svg class="w-5 md:w-6 h-5 md:h-6 text-gray-300 hover:text-orange-400" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                        </svg>
                    </button>
                    @endfor
                </div>
                <input type="hidden" name="rating" id="editSelectedRating">
            </div>
            <div class="mb-3 md:mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1 md:mb-2">Message</label>
                <textarea name="message" id="editFeedbackMessage" rows="3" class="w-full px-3 md:px-4 py-2 md:py-3 rounded-xl border border-gray-200 text-sm md:text-base"></textarea>
            </div>
            <div class="flex justify-end gap-2 md:gap-3">
                <button type="button" onclick="closeEditFeedbackModal()" class="px-4 md:px-5 py-2 md:py-2.5 text-gray-600 hover:text-gray-800 hover:bg-gray-100 rounded-lg transition-colors text-sm w-full md:w-auto">Cancel</button>
                <button type="submit" class="px-4 md:px-5 py-2 md:py-2.5 bg-orange-500 text-white font-medium rounded-lg hover:bg-orange-600 transition-colors text-sm w-full md:w-auto">Update</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
function setRating(rating) {
    document.getElementById('selectedRating').value = rating;
    document.querySelectorAll('#starRating .star-btn svg').forEach((star, index) => {
        star.classList.toggle('text-orange-400', index < rating);
        star.classList.toggle('text-gray-300', index >= rating);
    });
}

function toggleReplyForm(id) {
    document.getElementById('replyForm' + id).classList.toggle('hidden');
}

function openEditFeedbackModal(id, rating, message) {
    document.getElementById('editFeedbackForm').action = '/feedback/' + id;
    document.getElementById('editSelectedRating').value = rating;
    document.getElementById('editFeedbackMessage').value = message;
    document.querySelectorAll('#editStarRating .edit-star-btn svg').forEach((star, index) => {
        star.classList.toggle('text-orange-400', index < rating);
        star.classList.toggle('text-gray-300', index >= rating);
    });
    document.getElementById('editFeedbackModal').classList.remove('hidden');
    document.getElementById('editFeedbackModal').classList.add('flex');
}

function setEditRating(rating) {
    document.getElementById('editSelectedRating').value = rating;
    document.querySelectorAll('#editStarRating .edit-star-btn svg').forEach((star, index) => {
        star.classList.toggle('text-orange-400', index < rating);
        star.classList.toggle('text-gray-300', index >= rating);
    });
}

function closeEditFeedbackModal() {
    document.getElementById('editFeedbackModal').classList.add('hidden');
    document.getElementById('editFeedbackModal').classList.remove('flex');
}

document.getElementById('editFeedbackModal').addEventListener('click', function(e) {
    if (e.target === this) closeEditFeedbackModal();
});

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') closeEditFeedbackModal();
});
</script>
@endpush