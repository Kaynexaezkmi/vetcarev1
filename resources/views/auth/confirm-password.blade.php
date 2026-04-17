<x-auth-card>
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-900 mb-2">Confirm Password</h2>
        <p class="text-gray-600">This is a secure area of the application. Please confirm your password before continuing.</p>
    </div>

    <form method="POST" action="{{ route('password.confirm') }}">
        @csrf
        
        <div class="mb-6">
            <label for="password" class="block text-sm font-medium text-gray-700 mb-2">Password</label>
            <input id="password" type="password" name="password" required autocomplete="current-password" 
                class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition @error('password') border-red-500 @enderror">
            @error('password')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <button type="submit" class="w-full bg-orange-500 text-white font-semibold py-3 rounded-xl hover:bg-orange-600 transition shadow-lg shadow-orange-500/30">
            Confirm
        </button>
    </form>
</x-auth-card>
