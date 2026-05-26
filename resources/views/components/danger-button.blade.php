<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center px-4 py-2 bg-red-500 text-white font-semibold rounded-xl hover:bg-red-600 transition-all duration-200 text-sm focus:outline-none disabled:opacity-50']) }}>
    {{ $slot }}
</button>
