@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'bg-white/5 border border-white/10 rounded-xl px-4 py-2.5 text-white placeholder-zinc-500 focus:border-violet-500/50 focus:ring-violet-500/50 transition-colors shadow-sm']) }}>
