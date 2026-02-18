{{-- Language Dropdown Component (DB + mcamara, robust) --}}

@php
    use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

    // In case $languages is not shared on some pages
    $languages = $languages ?? collect();

    // Ensure we always have at least one language (fallback)
    if ($languages->isEmpty()) {
        $languages = collect([(object) ['code' => config('app.locale', 'en'), 'name' => 'English']]);
    }

    // Prefer mcamara’s current locale, fallback to app locale
    $currentLocale = LaravelLocalization::getCurrentLocale() ?: app()->getLocale() ?: config('app.locale', 'en');
    $defaultLocale = config('app.locale', 'en');

    $langs = $languages
        ->map(
            fn($l) => [
                'code' => $l->code,
                'label' => $l->name,
                'native' => $l->name,
            ],
        )
        ->values();

    // Build URLs based on CURRENT URL, so it keeps the same page when switching locale
    // Example: /en/dashboard -> /fr/dashboard
    $langUrls = $languages
        ->mapWithKeys(
            fn($l) => [
                $l->code => LaravelLocalization::getLocalizedURL($l->code, request()->fullUrl(), [], true),
            ],
        )
        ->toArray();
@endphp

<div class="relative" x-data="{
    open: false,
    q: '',
    current: @js($currentLocale),
    defaultLocale: @js($defaultLocale),
    languages: @js($langs),
    urls: @js($langUrls),

    toggle() { this.open = !this.open },
    close() {
        this.open = false;
        this.q = ''
    },

    get filtered() {
        const q = this.q.trim().toLowerCase()
        if (!q) return this.languages
        return this.languages.filter(l =>
            (l.label + ' ' + l.native + ' ' + l.code).toLowerCase().includes(q)
        )
    },

    pick(code) {
        this.current = code
        this.close()

        const target = this.urls?.[code]
        if (target) {
            window.location.replace(target)
            return
        }

        // last fallback
        window.location.assign('/' + code)
    },

    currentLang() {
        return this.languages.find(l => l.code === this.current) ||
            this.languages.find(l => l.code === this.defaultLocale) ||
            this.languages[0] || { code: 'en', label: 'English', native: 'English' }
    }
}" @click.away="close()" @keydown.escape.window="close()">

    <!-- Button -->
    <button type="button" @click="toggle()"
        class="relative flex items-center justify-center text-gray-500 transition-colors bg-white border border-gray-200 rounded-full
               hover:text-dark-900 h-11 w-11 hover:bg-gray-100 hover:text-gray-700
               dark:border-gray-800 dark:bg-gray-900 dark:text-gray-400 dark:hover:bg-gray-800 dark:hover:text-white"
        :aria-expanded="open" aria-haspopup="true">

        <!-- globe -->
        <svg class="fill-current" width="20" height="20" viewBox="0 0 24 24">
            <path d="M12 2C6.49 2 2 6.49 2 12s4.49 10 10 10
                     10-4.49 10-10S17.51 2 12 2Zm7.93 9h-3.17
                     c-.12-2.23-.72-4.3-1.7-5.87A8.03 8.03 0 0 1 19.93 11ZM12 4
                     c1.2 1.53 2.03 3.97 2.2 7H9.8C9.97 7.97 10.8 5.53 12 4ZM4.07 13h3.17
                     c.12 2.23.72 4.3 1.7 5.87A8.03 8.03 0 0 1 4.07 13Zm3.17-2H4.07
                     A8.03 8.03 0 0 1 8.94 5.13C7.96 6.7 7.36 8.77 7.24 11ZM12 20
                     c-1.2-1.53-2.03-3.97-2.2-7h4.4c-.17 3.03-1 5.47-2.2 7Zm3.06-1.13
                     c.98-1.57 1.58-3.64 1.7-5.87h3.17a8.03 8.03 0 0 1-4.87 5.87Z" />
        </svg>

        <!-- current locale badge -->
        <span
            class="absolute -bottom-1 -right-1 flex h-5 min-w-5 px-1 items-center justify-center rounded-full
                   border border-white bg-white text-[10px] font-semibold text-gray-700 uppercase
                   dark:border-gray-900 dark:bg-gray-900 dark:text-gray-200"
            x-text="currentLang().code"></span>
    </button>

    <!-- Dropdown -->
    <div x-show="open" x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="transform opacity-0 translate-y-2 scale-98"
        x-transition:enter-end="transform opacity-100 translate-y-0 scale-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="transform opacity-100 translate-y-0 scale-100"
        x-transition:leave-end="transform opacity-0 translate-y-2 scale-98"
        class="absolute right-0 mt-2 w-64 rounded-2xl border border-gray-100 bg-white p-2 shadow-xl
               dark:border-gray-800 dark:bg-gray-900"
        style="display:none;">

        <div class="px-3 py-2 text-[11px] font-bold uppercase tracking-wider text-gray-400 dark:text-gray-500">
            Language
        </div>

        <div class="space-y-0.5">
            <template x-for="lang in languages" :key="lang.code">
                <button type="button" @click="pick(lang.code)"
                    class="group flex w-full items-center justify-between rounded-xl px-3 py-2.5 transition-colors"
                    :class="lang.code === current ?
                        'bg-brand-50 text-brand-700 dark:bg-brand-500/10 dark:text-brand-400' :
                        'text-gray-700 hover:bg-gray-50 dark:text-gray-300 dark:hover:bg-white/5'">

                    <div class="flex flex-col items-start">
                        <span class="text-sm font-medium" x-text="lang.native"></span>
                        <span class="text-[11px] opacity-70" x-text="lang.label"></span>
                    </div>

                    <svg x-show="lang.code === current" class="w-4 h-4 stroke-current" viewBox="0 0 24 24"
                        fill="none" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="20 6 9 17 4 12"></polyline>
                    </svg>
                </button>
            </template>
        </div>
    </div>
</div>
