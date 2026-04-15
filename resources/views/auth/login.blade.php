<x-auth-card>
    <form method="POST" action="{{ route('login') }}">
        @csrf
        
        <div class="mb-6">
            <h2 class="text-2xl font-bold text-gray-900 mb-2">Welcome back</h2>
            <p class="text-gray-600">Sign in to your account to continue</p>
        </div>

        @if (session('status'))
        <div class="mb-4 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl text-sm">
            {{ session('status') }}
        </div>
        @endif

        <div class="space-y-5">
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username" 
                    class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition @error('email') border-red-500 @enderror">
                @error('email')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="password" class="block text-sm font-medium text-gray-700 mb-2">Password</label>
                <input id="password" type="password" name="password" required autocomplete="current-password" 
                    class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition @error('password') border-red-500 @enderror">
                @error('password')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex items-center justify-between">
                <label class="flex items-center">
                    <input type="checkbox" name="remember" class="w-4 h-4 text-orange-500 rounded focus:ring-orange-500">
                    <span class="ml-2 text-sm text-gray-600">Remember me</span>
                </label>
                @if (Route::has('password.request'))
                <a href="{{ route('password.request') }}" class="text-sm text-orange-500 hover:text-orange-600">Forgot password?</a>
                @endif
            </div>

            <button type="submit" class="w-full bg-orange-500 text-white font-semibold py-3 rounded-xl hover:bg-orange-600 transition shadow-lg shadow-orange-500/30">
                Sign In
            </button>
        </div>

        @if (Route::has('register'))
        <p class="mt-6 text-center text-gray-600">
            Don't have an account? <a href="{{ route('register') }}" class="text-orange-500 hover:text-orange-600 font-medium">Sign up</a>
        </p>
        @endif
    </form>
</x-auth-card>
