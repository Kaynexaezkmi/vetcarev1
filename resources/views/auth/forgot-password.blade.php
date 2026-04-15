<x-auth-card>
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-900 mb-2">Forgot your password?</h2>
        <p class="text-gray-600">No problem. Just let us know your email address and we will email you a password reset link.</p>
    </div>

    @if (session('status'))
    <div class="mb-4 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl text-sm">
        {{ session('status') }}
    </div>
    @endif

    <form method="POST" action="{{ route('password.email') }}">
        @csrf
        
        <div class="mb-6">
            <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username" 
                class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition @error('email') border-red-500 @enderror">
            @error('email')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <button type="submit" class="w-full bg-orange-500 text-white font-semibold py-3 rounded-xl hover:bg-orange-600 transition shadow-lg shadow-orange-500/30">
            Email Password Reset Link
        </button>
    </form>

    <p class="mt-6 text-center text-gray-600">
        Remember your password? <a href="{{ route('login') }}" class="text-orange-500 hover:text-orange-600 font-medium">Sign in</a>
    </p>
</x-auth-card>
