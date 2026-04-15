<x-auth-card>
    <form method="POST" action="{{ route('register') }}">
        @csrf
        
        <div class="mb-6">
            <h2 class="text-2xl font-bold text-gray-900 mb-2">Create your account</h2>
            <p class="text-gray-600">Join VetCare today</p>
        </div>

        <div class="space-y-5">
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Full Name</label>
                <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus autocomplete="name" 
                    class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition @error('name') border-red-500 @enderror">
                @error('name')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="username" 
                    class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition @error('email') border-red-500 @enderror">
                @error('email')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="password" class="block text-sm font-medium text-gray-700 mb-2">Password</label>
                <input id="password" type="password" name="password" required autocomplete="new-password" 
                    class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition @error('password') border-red-500 @enderror">
                @error('password')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">Confirm Password</label>
                <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password" 
                    class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition">
            </div>

            <button type="submit" class="w-full bg-orange-500 text-white font-semibold py-3 rounded-xl hover:bg-orange-600 transition shadow-lg shadow-orange-500/30">
                Create Account
            </button>
        </div>

        @if (Route::has('login'))
        <p class="mt-6 text-center text-gray-600">
            Already have an account? <a href="{{ route('login') }}" class="text-orange-500 hover:text-orange-600 font-medium">Sign in</a>
        </p>
        @endif
    </form>
</x-auth-card>
