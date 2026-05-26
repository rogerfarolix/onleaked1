<x-guest-layout>
    <div class="text-center mb-6">
        <h2 class="text-2xl font-bold text-white mb-2">Reset Password</h2>
        <div class="text-sm text-zinc-400">
            {{ __('Forgot your password? No problem. Just let us know your email address and we will email you a password reset link that will allow you to choose a new one.') }}
        </div>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}" class="space-y-4">
        @csrf

        <!-- Email Address -->
        <div>
            <label for="email" class="block text-sm font-medium text-zinc-300 mb-1.5">{{ __('Email') }}</label>
            <input id="email" class="block w-full bg-white/5 border border-white/10 rounded-xl px-4 py-2.5 text-white placeholder-zinc-500 focus:border-violet-500/50 focus:ring-violet-500/50 transition-colors" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" placeholder="you@example.com" />
            <x-input-error :messages="$errors->get('email')" class="mt-2 text-red-400 text-sm" />
        </div>

        <button class="w-full py-3 px-4 bg-white text-black font-semibold rounded-xl hover:bg-zinc-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-zinc-500 focus:ring-offset-[#09090b] transition-all duration-200 mt-6">
            {{ __('Email Password Reset Link') }}
        </button>

        <p class="text-center text-sm text-zinc-500 mt-6">
            <a href="{{ route('login') }}" class="text-white hover:text-violet-400 transition-colors">Back to login</a>
        </p>
    </form>
</x-guest-layout>
