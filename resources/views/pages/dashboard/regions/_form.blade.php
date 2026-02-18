@csrf

@php
    $inputClass =
        'dark:bg-dark-900 shadow-theme-xs transition-all duration-200 focus:border-brand-500 focus:ring-brand-500/10 dark:focus:border-brand-500 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30';
    $textareaClass =
        'dark:bg-dark-900 shadow-theme-xs transition-all duration-200 focus:border-brand-500 focus:ring-brand-500/10 dark:focus:border-brand-500 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30';
@endphp

<div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
    <div class="px-6 py-5">
        <h3 class="text-base font-medium text-gray-800 dark:text-white/90">Region Information</h3>
        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Fill translations for all active languages.</p>
    </div>

    <div class="border-t border-gray-100 p-4 dark:border-gray-800 sm:p-6">
        <div class="space-y-6">

            {{-- Region Code --}}
            <div class="group">
                <label class="mb-1.5 block text-sm font-medium text-gray-700 transition-colors group-focus-within:text-brand-500 dark:text-gray-400 dark:group-focus-within:text-brand-400">
                    Region Code <span class="text-red-500">*</span>
                </label>

                <div class="relative">
                    <input type="text" name="code" value="{{ old('code', $region->code ?? '') }}"
                        class="{{ $inputClass }} @error('code') border-error-300 dark:border-error-700 @enderror"
                        placeholder="e.g., EU, NA, ASIA" maxlength="10">
                    
                    @error('code')
                        <p class="mt-1.5 text-theme-xs text-error-500">{{ $message }}</p>
                    @enderror
                </div>
                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Unique identifier for this region (max 10 characters)</p>
            </div>

            {{-- Name Translations --}}
            <div>
                <h4 class="mb-3 text-sm font-medium text-gray-700 dark:text-gray-300">Region Name</h4>
                <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                    @foreach ($languages as $lang)
                        @php
                            $code = $lang->code;
                            $value = old("name.$code", isset($region) ? $region->getTranslation('name', $code, false) : '');
                        @endphp

                        <div class="group">
                            <label class="mb-1.5 block text-sm font-medium text-gray-700 transition-colors group-focus-within:text-brand-500 dark:text-gray-400 dark:group-focus-within:text-brand-400">
                                Name ({{ strtoupper($code) }})
                            </label>

                            <div class="relative">
                                <input type="text" name="name[{{ $code }}]" value="{{ $value }}"
                                    class="{{ $inputClass }} @error("name.$code") border-error-300 dark:border-error-700 @enderror"
                                    placeholder="Region name in {{ strtoupper($code) }}">
                                
                                @error("name.$code")
                                    <p class="mt-1.5 text-theme-xs text-error-500">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    @endforeach
                </div>

                @error('name')
                    <div class="mt-3 rounded-lg bg-error-50 p-3 border border-error-100 dark:bg-error-500/5 dark:border-error-500/20">
                        <p class="text-theme-xs text-error-500 flex items-center gap-2">
                            <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                            </svg>
                            {{ $message }}
                        </p>
                    </div>
                @enderror
            </div>

            {{-- Description Translations --}}
            <div>
                <h4 class="mb-3 text-sm font-medium text-gray-700 dark:text-gray-300">Description (Optional)</h4>
                <div class="grid grid-cols-1 gap-6">
                    @foreach ($languages as $lang)
                        @php
                            $code = $lang->code;
                            $value = old("description.$code", isset($region) ? $region->getTranslation('description', $code, false) : '');
                        @endphp

                        <div class="group">
                            <label class="mb-1.5 block text-sm font-medium text-gray-700 transition-colors group-focus-within:text-brand-500 dark:text-gray-400 dark:group-focus-within:text-brand-400">
                                Description ({{ strtoupper($code) }})
                            </label>

                            <div class="relative">
                                <textarea name="description[{{ $code }}]" rows="3"
                                    class="{{ $textareaClass }} @error("description.$code") border-error-300 dark:border-error-700 @enderror"
                                    placeholder="Brief description in {{ strtoupper($code) }}">{{ $value }}</textarea>
                                
                                @error("description.$code")
                                    <p class="mt-1.5 text-theme-xs text-error-500">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

        </div>
    </div>
</div>
