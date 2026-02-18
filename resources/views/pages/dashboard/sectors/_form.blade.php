@csrf

@php
    $inputClass =
        'dark:bg-dark-900 shadow-theme-xs transition-all duration-200 focus:border-brand-500 focus:ring-brand-500/10 dark:focus:border-brand-500 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30';
@endphp

<div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
    <div class="px-6 py-5">
        <h3 class="text-base font-medium text-gray-800 dark:text-white/90">Sector Information</h3>
        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Fill translations for all active languages.</p>
    </div>

    <div class="border-t border-gray-100 p-4 dark:border-gray-800 sm:p-6">
        <div class="space-y-6">

            {{-- Status Toggle Section --}}
            <div class="flex items-center justify-between p-4 rounded-xl bg-gray-50 dark:bg-white/5 border border-gray-100 dark:border-gray-800">
                <div class="flex flex-col">
                    <span class="text-sm font-semibold text-gray-800 dark:text-white/90">Active Status</span>
                    <span class="text-xs text-gray-500 dark:text-gray-400">Determine if this sector is visible on the platform.</span>
                </div>
                <div x-data="{ active: {{ old('is_active', $sector->is_active ?? true) ? 'true' : 'false' }} }">
                    <input type="hidden" name="is_active" :value="active ? '1' : '0'">
                    <button type="button" 
                        @click="active = !active"
                        :class="active ? 'bg-brand-500' : 'bg-gray-200 dark:bg-gray-700'"
                        class="relative inline-flex h-6 w-11 shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-brand-500 focus:ring-offset-2">
                        <span aria-hidden="true" 
                            :class="active ? 'translate-x-5' : 'translate-x-0'"
                            class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out">
                        </span>
                    </button>
                </div>
            </div>

            {{-- Translations --}}
            <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                @foreach ($languages as $lang)
                    @php
                        $code = $lang->code;
                        $value = old("name.$code", isset($sector) ? $sector->getTranslation('name', $code, false) : '');
                    @endphp

                    <div class="group">
                        <label class="mb-1.5 block text-sm font-medium text-gray-700 transition-colors group-focus-within:text-brand-500 dark:text-gray-400 dark:group-focus-within:text-brand-400">
                            Name ({{ strtoupper($code) }})
                        </label>

                        <div class="relative">
                            <input type="text" name="name[{{ $code }}]" value="{{ $value }}"
                                class="{{ $inputClass }} @error("name.$code") border-error-300 dark:border-error-700 @enderror"
                                placeholder="Sector name in {{ strtoupper($code) }}">
                            
                            @error("name.$code")
                                <p class="mt-1.5 text-theme-xs text-error-500">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                @endforeach
            </div>

            @error('name')
                <div class="rounded-lg bg-error-50 p-3 border border-error-100 dark:bg-error-500/5 dark:border-error-500/20">
                    <p class="text-theme-xs text-error-500 flex items-center gap-2">
                        <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                        </svg>
                        {{ $message }}
                    </p>
                </div>
            @enderror

        </div>
    </div>
</div>
