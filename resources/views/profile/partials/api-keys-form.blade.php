<section x-data="apiKeyManager()">
    <header>
        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">API Keys</h2>
        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
            Generate personal API keys to access Onleaked tools programmatically.
            Keys are shown only once at creation.
        </p>
    </header>

    <div class="mt-6 space-y-4">

        <!-- Create new key form -->
        <form @submit.prevent="createKey" class="flex gap-3">
            <input x-model="newKeyName" type="text" placeholder="Key name (e.g. My Script)"
                class="flex-1 rounded-lg border border-white/10 bg-white/5 text-white text-sm px-4 py-2.5 placeholder-zinc-500 focus:outline-none focus:border-violet-500/50 focus:ring-1 focus:ring-violet-500/30">
            <button type="submit" :disabled="creating || !newKeyName.trim()"
                class="px-4 py-2 bg-violet-600 text-white text-sm font-medium rounded-lg hover:bg-violet-500 disabled:opacity-50 disabled:cursor-not-allowed transition-colors">
                <span x-show="!creating">Generate Key</span>
                <span x-show="creating" x-cloak>Generating…</span>
            </button>
        </form>

        <!-- One-time key display -->
        <template x-if="newKeyValue">
            <div class="p-4 rounded-xl border border-emerald-500/20 bg-emerald-500/5">
                <p class="text-sm text-emerald-400 font-semibold mb-2">Key generated — copy it now, it won't be shown again!</p>
                <div class="flex items-center gap-2">
                    <code class="flex-1 text-xs font-mono text-zinc-300 bg-black/30 px-3 py-2 rounded-lg break-all" x-text="newKeyValue"></code>
                    <button @click="copyKey()" class="shrink-0 text-xs px-3 py-2 rounded-lg bg-white/10 text-zinc-300 hover:bg-white/20 transition-colors" x-text="copied ? 'Copied!' : 'Copy'"></button>
                </div>
            </div>
        </template>

        <!-- Error display -->
        <template x-if="error">
            <div class="p-3 rounded-lg bg-red-500/10 border border-red-500/20 text-red-400 text-sm" x-text="error"></div>
        </template>

        <!-- Existing keys list -->
        <div class="space-y-2">
            <template x-if="loading">
                <p class="text-zinc-500 text-sm">Loading keys…</p>
            </template>
            <template x-if="!loading && keys.length === 0">
                <p class="text-zinc-600 text-sm">No API keys yet.</p>
            </template>
            <template x-for="key in keys" :key="key.id">
                <div class="flex items-center justify-between p-4 rounded-xl border border-white/5 bg-white/[0.02]">
                    <div>
                        <p class="text-sm font-medium text-white" x-text="key.name"></p>
                        <p class="text-xs text-zinc-500 mt-0.5">
                            Created <span x-text="formatDate(key.created_at)"></span>
                            <template x-if="key.last_used_at">
                                &mdash; Last used <span x-text="formatDate(key.last_used_at)"></span>
                            </template>
                        </p>
                    </div>
                    <button @click="deleteKey(key.id)"
                        class="text-xs px-3 py-1.5 rounded-lg border border-red-500/20 text-red-400 hover:bg-red-500/10 transition-colors">
                        Revoke
                    </button>
                </div>
            </template>
        </div>

        <!-- API docs hint -->
        <div class="mt-4 p-4 rounded-xl border border-white/5 bg-white/[0.02] text-xs text-zinc-500">
            <p class="font-mono mb-1">POST /api/v1/check-email</p>
            <p class="font-mono mb-2">POST /api/v1/analyze-domain</p>
            <p>Send <code class="text-zinc-400">Authorization: Bearer YOUR_KEY</code> header with each request.</p>
        </div>
    </div>
</section>

@push('scripts')
<script nonce="{{ $cspNonce }}">
    function apiKeyManager() {
        return {
            keys: [],
            loading: true,
            creating: false,
            newKeyName: '',
            newKeyValue: null,
            error: null,
            copied: false,
            async init() {
                await this.loadKeys();
            },
            async loadKeys() {
                this.loading = true;
                try {
                    const res = await fetch('{{ route("api-keys.index") }}', { headers: { 'Accept': 'application/json' } });
                    this.keys = await res.json();
                } catch {}
                finally { this.loading = false; }
            },
            async createKey() {
                this.creating = true; this.newKeyValue = null; this.error = null;
                try {
                    const res = await fetch('{{ route("api-keys.store") }}', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Accept': 'application/json' },
                        body: JSON.stringify({ name: this.newKeyName }),
                    });
                    const data = await res.json();
                    if (!res.ok) { this.error = data.message || 'Error creating key.'; return; }
                    this.newKeyValue = data.key;
                    this.newKeyName = '';
                    await this.loadKeys();
                } catch { this.error = 'Connection error.'; }
                finally { this.creating = false; }
            },
            async deleteKey(id) {
                if (!confirm('Revoke this API key? This cannot be undone.')) return;
                await fetch(`/profile/api-keys/${id}`, {
                    method: 'DELETE',
                    headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
                });
                await this.loadKeys();
            },
            async copyKey() {
                await navigator.clipboard.writeText(this.newKeyValue);
                this.copied = true;
                setTimeout(() => this.copied = false, 2000);
            },
            formatDate(d) {
                return d ? new Date(d).toLocaleDateString() : '—';
            }
        }
    }
</script>
@endpush
