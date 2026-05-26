<button {{ $attributes->merge(['type' => 'button', 'class' => 'inline-flex items-center px-4 py-2 bg-transparent border border-white/20 rounded-xl font-semibold text-white hover:bg-white/5 transition-all duration-200 text-sm focus:outline-none disabled:opacity-50']) }}>
    {{ $slot }}
</button>
