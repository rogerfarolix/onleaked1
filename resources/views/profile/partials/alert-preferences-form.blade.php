<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">Alert Preferences</h2>
        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
            Choose how often you receive vulnerability alert emails for technologies you track.
        </p>
    </header>

    <form method="post" action="{{ route('profile.alerts') }}" class="mt-6 space-y-4">
        @csrf
        @method('PATCH')

        @if (session('status') === 'alerts-updated')
            <p class="text-sm text-green-600 dark:text-green-400">Alert preferences saved.</p>
        @endif

        <div class="space-y-3">
            @foreach([
                'instant' => ['label' => 'Instant', 'desc' => 'Receive an email as soon as a new CVE is detected'],
                'daily'   => ['label' => 'Daily Digest', 'desc' => 'One email per day summarizing new CVEs'],
                'weekly'  => ['label' => 'Weekly Digest', 'desc' => 'One email every Monday with the week\'s CVEs'],
                'never'   => ['label' => 'Never', 'desc' => 'Disable all CVE alert emails'],
            ] as $value => $option)
            <label class="flex items-start gap-3 p-4 rounded-xl border cursor-pointer transition-colors
                {{ ($user->alert_frequency ?? 'instant') === $value
                    ? 'border-violet-500/40 bg-violet-500/5'
                    : 'border-white/10 hover:border-white/20' }}">
                <input type="radio" name="alert_frequency" value="{{ $value }}"
                    {{ ($user->alert_frequency ?? 'instant') === $value ? 'checked' : '' }}
                    class="mt-0.5 accent-violet-500">
                <div>
                    <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $option['label'] }}</p>
                    <p class="text-xs text-gray-500 dark:text-zinc-400 mt-0.5">{{ $option['desc'] }}</p>
                </div>
            </label>
            @endforeach
        </div>

        <div class="flex items-center gap-4">
            <button type="submit"
                class="px-4 py-2 bg-violet-600 text-white text-sm font-medium rounded-lg hover:bg-violet-500 transition-colors">
                Save Preferences
            </button>
        </div>
    </form>
</section>
