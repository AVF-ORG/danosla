@csrf

@php
    $inputClass =
        'dark:bg-dark-900 shadow-theme-xs transition-all duration-200 focus:border-brand-500 focus:ring-brand-500/10 dark:focus:border-brand-500 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30';
@endphp

<div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
    <div class="px-6 py-5">
        <h3 class="text-base font-medium text-gray-800 dark:text-white/90">Country Information</h3>
        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Fill country details and translations.</p>
    </div>

    <div class="border-t border-gray-100 p-4 dark:border-gray-800 sm:p-6">
        <div class="space-y-6">

            {{-- ISO2 and International Code --}}
            <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                <div class="group">
                    <label class="mb-1.5 block text-sm font-medium text-gray-700 transition-colors group-focus-within:text-brand-500 dark:text-gray-400 dark:group-focus-within:text-brand-400">
                        ISO2 Code <span class="text-red-500">*</span>
                    </label>

                    <div class="relative">
                        <input type="text" name="iso2" value="{{ old('iso2', $country->iso2 ?? '') }}"
                            class="{{ $inputClass }} @error('iso2') border-error-300 dark:border-error-700 @enderror"
                            placeholder="e.g., US, GB, FR" maxlength="2">
                        
                        @error('iso2')
                            <p class="mt-1.5 text-theme-xs text-error-500">{{ $message }}</p>
                        @enderror
                    </div>
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">2-letter ISO country code</p>
                </div>

                <div class="group">
                    <label class="mb-1.5 block text-sm font-medium text-gray-700 transition-colors group-focus-within:text-brand-500 dark:text-gray-400 dark:group-focus-within:text-brand-400">
                        International Code <span class="text-red-500">*</span>
                    </label>

                    <div class="relative">
                        <input type="text" name="international_code" value="{{ old('international_code', $country->international_code ?? '') }}"
                            class="{{ $inputClass }} @error('international_code') border-error-300 dark:border-error-700 @enderror"
                            placeholder="e.g., +1, +44, +33" maxlength="10">
                        
                        @error('international_code')
                            <p class="mt-1.5 text-theme-xs text-error-500">{{ $message }}</p>
                        @enderror
                    </div>
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Phone dialing code</p>
                </div>
            </div>

            {{-- Name Translations --}}
            <div>
                <h4 class="mb-3 text-sm font-medium text-gray-700 dark:text-gray-300">Country Name</h4>
                <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                    @foreach ($languages as $lang)
                        @php
                            $code = $lang->code;
                            $value = old("name.$code", isset($country) ? $country->getTranslation('name', $code, false) : '');
                        @endphp

                        <div class="group">
                            <label class="mb-1.5 block text-sm font-medium text-gray-700 transition-colors group-focus-within:text-brand-500 dark:text-gray-400 dark:group-focus-within:text-brand-400">
                                Name ({{ strtoupper($code) }})
                            </label>

                            <div class="relative">
                                <input type="text" name="name[{{ $code }}]" value="{{ $value }}"
                                    class="{{ $inputClass }} @error("name.$code") border-error-300 dark:border-error-700 @enderror"
                                    placeholder="Country name in {{ strtoupper($code) }}">
                                
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

            {{-- SVG Flag (Optional) --}}
            <div class="group">
                <label class="mb-1.5 block text-sm font-medium text-gray-700 transition-colors group-focus-within:text-brand-500 dark:text-gray-400 dark:group-focus-within:text-brand-400">
                    SVG Flag (Optional)
                </label>

                <div class="relative">
                    <textarea name="svg" rows="4"
                        class="{{ $inputClass }} @error('svg') border-error-300 dark:border-error-700 @enderror"
                        placeholder="Paste SVG code here...">{{ old('svg', $country->svg ?? '') }}</textarea>
                    
                    @error('svg')
                        <p class="mt-1.5 text-theme-xs text-error-500">{{ $message }}</p>
                    @enderror
                </div>
                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">SVG code for country flag</p>
            </div>

        </div>
    </div>
</div>
