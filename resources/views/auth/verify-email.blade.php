<x-auth-card>
    <div class="mb-6 text-center">
        <div class="w-16 h-16 bg-orange-100 rounded-full flex items-center justify-center mx-auto mb-4">
            <svg class="w-8 h-8 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
            </svg>
        </div>
        <h2 class="text-2xl font-bold text-gray-900 mb-2">Verify your email</h2>
        <p class="text-gray-600">Thanks for signing up! Before getting started, could you verify your email address by clicking on the link we just emailed to you?</p>
    </div>

    @if (session('status') == 'verification-link-sent')
    <div class="mb-4 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl text-sm">
        A new verification link has been sent to the email address you provided during registration.
    </div>
    @endif

    <form method="POST" action="{{ route('verification.send') }}">
        @csrf
        <button type="submit" class="w-full bg-orange-500 text-white font-semibold py-3 rounded-xl hover:bg-orange-600 transition shadow-lg shadow-orange-500/30">
            Resend Verification Email
        </button>
    </form>

    <form method="POST" action="{{ route('logout') }}" class="mt-6">
        @csrf
        <button type="submit" class="w-full text-gray-600 font-medium py-3 hover:text-gray-800 transition">
            Log out
        </button>
    </form>
</x-auth-card>
