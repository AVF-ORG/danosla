@props([
    'model' => 'open',
    'title' => null,
    'showCloseButton' => true,
    'closeOnOutside' => true,
    'closeOnEscape' => true,
    'maxWidth' => 'max-w-2xl',
    'footerClass' => '',
    'align' => 'items-center', // items-center, items-start
])

<div x-show="{{ $model }}" x-cloak x-data x-init="$watch('{{ $model }}', value => {
    document.body.classList.toggle('overflow-hidden', value)
})"
    @keydown.escape.window="{{ $closeOnEscape ? $model . ' = false' : '' }}"
    class="fixed inset-0 z-[99999] flex justify-center px-4 sm:px-8 overflow-y-auto py-10 {{ $align }}"
    role="dialog" aria-modal="true">
    
    {{-- Backdrop --}}
    <div class="fixed inset-0 bg-slate-400/20 backdrop-blur-[1px]" x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0" @if ($closeOnOutside) @click="{{ $model }} = false" @endif
        aria-hidden="true"></div>

    {{-- Modal panel --}}
    <div @click.stop
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 translate-y-3 scale-[0.98]"
        x-transition:enter-end="opacity-100 translate-y-0 scale-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 translate-y-0 scale-100"
        x-transition:leave-end="opacity-0 translate-y-3 scale-[0.98]"
        {{ $attributes->merge([
            'class' => "relative w-full flex flex-col rounded-xl bg-white shadow-2xl dark:bg-gray-900 border border-gray-200/60 $maxWidth" . ($align !== 'items-start' ? ' max-h-[90vh]' : '')
        ]) }}>
        
        <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100 dark:border-gray-800">
            {{-- Header --}}
            @if ($title)
                <h3 class="text-xl font-bold text-[#5c6b88] dark:text-white/90">
                    {{ $title }}
                </h3>
            @endif

            {{-- Close Button --}}
            @if ($showCloseButton)
                <button type="button" @click="{{ $model }} = false"
                    class="inline-flex h-10 w-10 items-center justify-center rounded-lg
                           bg-white text-gray-400 shadow-md ring-1 ring-black/5 transition hover:text-gray-600
                           dark:bg-gray-800 dark:text-gray-500 dark:hover:text-white"
                    aria-label="Close">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="18" y1="6" x2="6" y2="18"></line>
                        <line x1="6" y1="6" x2="18" y2="18"></line>
                    </svg>
                </button>
            @endif
        </div>

        {{-- Body --}}
        <div class="flex-1 overflow-y-auto px-5 py-5">
            {{ $slot }}
        </div>

        {{-- Footer --}}
        @isset($footer)
            <div class="px-5 py-4 border-t border-gray-100 dark:border-gray-800 {{ $footerClass }}">
                <div class="flex justify-end gap-3">
                    {{ $footer }}
                </div>
            </div>
        @endisset
    </div>
</div>
