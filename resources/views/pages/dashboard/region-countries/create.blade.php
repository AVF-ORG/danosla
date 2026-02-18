@extends('layouts.app')

@section('content')
    <div class="mb-6">
        <div class="flex items-center gap-3">
            <a href="{{ route('dashboard.region-countries.index') }}"
                class="inline-flex items-center justify-center rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 dark:hover:bg-gray-800">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Assign Countries to Region</h1>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Select a region and assign countries to it.</p>
            </div>
        </div>
    </div>

    <form method="POST" action="{{ route('dashboard.region-countries.store') }}" class="space-y-6">
        @csrf

        <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
            <div class="px-6 py-5">
                <h3 class="text-base font-medium text-gray-800 dark:text-white/90">Assignment Details</h3>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Choose a region and select countries to assign.</p>
            </div>

            <div class="border-t border-gray-100 p-4 dark:border-gray-800 sm:p-6">
                <div class="space-y-6">

                    {{-- Region Selection --}}
                    <div class="group">
                        <label class="mb-1.5 block text-sm font-medium text-gray-700 transition-colors group-focus-within:text-brand-500 dark:text-gray-400 dark:group-focus-within:text-brand-400">
                            Select Region <span class="text-red-500">*</span>
                        </label>

                        <select name="region_id" required
                            class="h-11 w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm text-gray-800 focus:border-brand-500 focus:outline-none focus:ring-4 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 @error('region_id') border-error-300 dark:border-error-700 @enderror">
                            <option value="">Choose a region...</option>
                            @foreach ($regions as $region)
                                <option value="{{ $region->id }}" {{ old('region_id') == $region->id ? 'selected' : '' }}>
                                    {{ $region->code }} - {{ $region->getTranslation('name', app()->getLocale()) }}
                                </option>
                            @endforeach
                        </select>

                        @error('region_id')
                            <p class="mt-1.5 text-xs text-error-500">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Countries Selection --}}
                    <div class="group">
                        <label class="mb-1.5 block text-sm font-medium text-gray-700 transition-colors group-focus-within:text-brand-500 dark:text-gray-400 dark:group-focus-within:text-brand-400">
                            Select Countries <span class="text-red-500">*</span>
                        </label>

                        <div class="rounded-lg border border-gray-300 bg-white p-4 dark:border-gray-700 dark:bg-gray-900 @error('country_ids') border-error-300 dark:border-error-700 @enderror">
                            <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-3">
                                @foreach ($countries as $country)
                                    <label class="flex items-center gap-3 rounded-lg border border-gray-200 bg-gray-50/50 p-3 cursor-pointer hover:bg-gray-100 dark:border-gray-700 dark:bg-white/[0.02] dark:hover:bg-white/[0.04] transition-colors">
                                        <input type="checkbox" name="country_ids[]" value="{{ $country->id }}"
                                            {{ in_array($country->id, old('country_ids', [])) ? 'checked' : '' }}
                                            class="h-4 w-4 rounded border-gray-300 text-brand-500 focus:ring-brand-500 dark:border-gray-600 dark:bg-gray-800">
                                        <div class="flex-1">
                                            <div class="text-sm font-medium text-gray-900 dark:text-white">
                                                {{ $country->getTranslation('name', app()->getLocale()) }}
                                            </div>
                                            <div class="text-xs text-gray-500 dark:text-gray-400">
                                                {{ $country->iso2 }} • {{ $country->international_code }}
                                            </div>
                                        </div>
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        @error('country_ids')
                            <p class="mt-1.5 text-xs text-error-500">{{ $message }}</p>
                        @enderror

                        <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">
                            Select one or more countries to assign to the region.
                        </p>
                    </div>

                </div>
            </div>
        </div>

        <div class="flex items-center justify-end gap-3">
            <a href="{{ route('dashboard.region-countries.index') }}"
                class="inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 dark:hover:bg-gray-800">
                Cancel
            </a>
            <button type="submit"
                class="inline-flex items-center gap-2 rounded-lg bg-brand-500 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-brand-600 focus-visible:outline focus-visible:outline-offset-2 focus-visible:outline-brand-500 transition-all active:scale-95">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                Assign Countries
            </button>
        </div>
    </form>
@endsection
