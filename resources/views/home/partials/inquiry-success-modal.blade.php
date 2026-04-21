@if(session('success'))
<div id="inquiry-success-modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-gray-900/60 px-4">
    <div class="w-full max-w-md rounded-3xl bg-white p-8 shadow-2xl">
        <div class="flex items-start justify-between gap-4">
            <div>
                <div class="mb-4 inline-flex h-12 w-12 items-center justify-center rounded-full bg-green-100">
                    <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0"></path>
                    </svg>
                </div>
                <h3 class="text-2xl font-bold text-gray-900">Inquiry Sent</h3>
                <p class="mt-3 text-gray-600">{{ session('success') }}</p>
            </div>
            <button type="button" id="close-inquiry-success-modal" class="rounded-full p-2 text-gray-400 transition hover:bg-gray-100 hover:text-gray-600" aria-label="Close inquiry success message">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
        <div class="mt-6">
            <button type="button" id="dismiss-inquiry-success-modal" class="inline-flex w-full items-center justify-center rounded-xl bg-orange-500 px-5 py-3 font-semibold text-white transition hover:bg-orange-600">
                Okay
            </button>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const modal = document.getElementById('inquiry-success-modal');
        const closeButton = document.getElementById('close-inquiry-success-modal');
        const dismissButton = document.getElementById('dismiss-inquiry-success-modal');

        if (!modal || !closeButton || !dismissButton) {
            return;
        }

        const openModal = function () {
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            document.body.classList.add('overflow-hidden');
        };

        const closeModal = function () {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
            document.body.classList.remove('overflow-hidden');
        };

        openModal();

        closeButton.addEventListener('click', closeModal);
        dismissButton.addEventListener('click', closeModal);
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
