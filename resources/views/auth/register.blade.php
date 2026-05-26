<x-guest-layout>
    <div class="text-center mb-8">
        <h2 class="text-2xl font-bold text-white mb-2">Create an Account</h2>
        <p class="text-zinc-400 text-sm">Join Onleaked to secure your digital assets</p>
    </div>

    <form method="POST" action="{{ route('register') }}" class="space-y-4">
        @csrf

        <!-- Name -->
        <div>
            <label for="name" class="block text-sm font-medium text-zinc-300 mb-1.5">{{ __('Name') }}</label>
            <input id="name" class="block w-full bg-white/5 border border-white/10 rounded-xl px-4 py-2.5 text-white placeholder-zinc-500 focus:border-violet-500/50 focus:ring-violet-500/50 transition-colors" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" placeholder="John Doe" />
            <x-input-error :messages="$errors->get('name')" class="mt-2 text-red-400 text-sm" />
        </div>

        <!-- Email Address -->
        <div>
            <label for="email" class="block text-sm font-medium text-zinc-300 mb-1.5">{{ __('Email') }}</label>
            <input id="email" class="block w-full bg-white/5 border border-white/10 rounded-xl px-4 py-2.5 text-white placeholder-zinc-500 focus:border-violet-500/50 focus:ring-violet-500/50 transition-colors" type="email" name="email" :value="old('email')" required autocomplete="username" placeholder="you@example.com" />
            <x-input-error :messages="$errors->get('email')" class="mt-2 text-red-400 text-sm" />
        </div>

        <!-- Password -->
        <div>
            <label for="password" class="block text-sm font-medium text-zinc-300 mb-1.5">{{ __('Password') }}</label>
            <input id="password" class="block w-full bg-white/5 border border-white/10 rounded-xl px-4 py-2.5 text-white placeholder-zinc-500 focus:border-violet-500/50 focus:ring-violet-500/50 transition-colors" type="password" name="password" required autocomplete="new-password" placeholder="••••••••" />
            <x-input-error :messages="$errors->get('password')" class="mt-2 text-red-400 text-sm" />
        </div>

        <!-- Confirm Password -->
        <div>
            <label for="password_confirmation" class="block text-sm font-medium text-zinc-300 mb-1.5">{{ __('Confirm Password') }}</label>
            <input id="password_confirmation" class="block w-full bg-white/5 border border-white/10 rounded-xl px-4 py-2.5 text-white placeholder-zinc-500 focus:border-violet-500/50 focus:ring-violet-500/50 transition-colors" type="password" name="password_confirmation" required autocomplete="new-password" placeholder="••••••••" />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2 text-red-400 text-sm" />
        </div>

        <button class="w-full py-3 px-4 bg-white text-black font-semibold rounded-xl hover:bg-zinc-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-zinc-500 focus:ring-offset-[#09090b] transition-all duration-200 mt-6">
            {{ __('Register') }}
        </button>
        
        <p class="text-center text-sm text-zinc-500 mt-6">
            Already registered? 
            <a href="{{ route('login') }}" class="text-white hover:text-violet-400 transition-colors">Log in</a>
        </p>
    </form>
</x-guest-layout>
