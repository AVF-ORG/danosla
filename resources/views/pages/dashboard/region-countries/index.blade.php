@extends('layouts.app')

@section('content')
    <div x-data="{
        openDelete: false,
        actionUrl: '',
        deleteName: '',
        confirmDelete(url, regionName, countryName) {
            this.actionUrl = url
            this.deleteName = `${countryName} from ${regionName}`
            this.openDelete = true
        },
        closeDelete() {
            this.openDelete = false
            this.actionUrl = ''
            this.deleteName = ''
        }
    }">


        {{-- Header --}}
        <div class="mb-6">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
                        Region-Country Assignments
                    </h1>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                        Manage which countries belong to each region.
                    </p>
                </div>

                <a href="{{ route('dashboard.region-countries.create') }}"
                    class="inline-flex items-center gap-2 rounded-lg bg-brand-500 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-brand-600 focus-visible:outline focus-visible:outline-offset-2 focus-visible:outline-brand-500 transition-all active:scale-95">
                    <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                    </svg>
                    Assign Countries
                </a>
            </div>
        </div>

        {{-- Stats --}}
        @php
            $totalAssignments = \App\Models\Region::withCount('countries')->get()->sum('countries_count');
            $regionsWithCountries = \App\Models\Region::has('countries')->count();
        @endphp

        <div class="mb-8 grid grid-cols-1 gap-4 sm:grid-cols-3">
            <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03]">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Assignments</p>
                        <h4 class="mt-2 text-2xl font-bold text-gray-900 dark:text-white">{{ $totalAssignments }}</h4>
                    </div>
                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-brand-50 text-brand-600 dark:bg-brand-500/10 dark:text-brand-400">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                    </div>
                </div>
            </div>

            <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03]">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Active Regions</p>
                        <h4 class="mt-2 text-2xl font-bold text-gray-900 dark:text-white">{{ $regionsWithCountries }}</h4>
                    </div>
                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-success-50 text-success-600 dark:bg-success-500/10 dark:text-success-400">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
            </div>

            <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03]">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Regions</p>
                        <h4 class="mt-2 text-2xl font-bold text-gray-900 dark:text-white">{{ $regions->total() }}</h4>
                    </div>
                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-blue-50 text-blue-600 dark:bg-blue-500/10 dark:text-blue-400">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7" />
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        {{-- Search --}}
        <div class="mb-6">
            <form method="GET" action="{{ route('dashboard.region-countries.index') }}" class="flex gap-3">
                <div class="flex-1">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search regions..."
                        class="h-11 w-full rounded-xl border border-gray-300 bg-white px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:border-brand-500 focus:outline-none focus:ring-4 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-gray-500">
                </div>
                <button type="submit"
                    class="inline-flex items-center gap-2 rounded-xl bg-gray-100 px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-200 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                    Search
                </button>
            </form>
        </div>

        {{-- Regions List --}}
        <div class="space-y-4">
            @forelse ($regions as $region)
                <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
                    <div class="bg-gray-50/50 px-6 py-4 dark:bg-white/[0.02]">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <span class="inline-flex items-center rounded-md bg-brand-50 px-2.5 py-1 text-xs font-medium text-brand-700 dark:bg-brand-500/10 dark:text-brand-400">
                                    {{ $region->code }}
                                </span>
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                                    {{ $region->getTranslation('name', app()->getLocale()) }}
                                </h3>
                                <span class="text-sm text-gray-500 dark:text-gray-400">
                                    ({{ $region->countries->count() }} {{ Str::plural('country', $region->countries->count()) }})
                                </span>
                            </div>
                        </div>
                    </div>

                    @if ($region->countries->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="w-full">
                                <thead class="border-b border-gray-200 bg-gray-50/30 dark:border-gray-800 dark:bg-white/[0.01]">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">
                                            Country
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">
                                            ISO2
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">
                                            Int. Code
                                        </th>
                                        <th class="px-6 py-3 text-right text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">
                                            Actions
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200 dark:divide-gray-800">
                                    @foreach ($region->countries as $country)
                                        <tr class="hover:bg-gray-50/50 dark:hover:bg-white/[0.02] transition-colors">
                                            <td class="px-6 py-3 text-sm font-medium text-gray-900 dark:text-white">
                                                {{ $country->getTranslation('name', app()->getLocale()) }}
                                            </td>
                                            <td class="px-6 py-3 text-sm text-gray-500 dark:text-gray-400">
                                                {{ $country->iso2 }}
                                            </td>
                                            <td class="px-6 py-3 text-sm text-gray-500 dark:text-gray-400">
                                                {{ $country->international_code }}
                                            </td>
                                            <td class="px-6 py-3 text-right">
                                                <button @click="confirmDelete('{{ route('dashboard.region-countries.destroy', [$region->id, $country->id]) }}', '{{ $region->getTranslation('name', app()->getLocale()) }}', '{{ $country->getTranslation('name', app()->getLocale()) }}')"
                                                    class="p-2 text-gray-400 hover:text-red-500 transition-colors" title="Remove">
                                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                    </svg>
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="px-6 py-8 text-center">
                            <p class="text-sm text-gray-500 dark:text-gray-400">No countries assigned to this region yet.</p>
                        </div>
                    @endif
                </div>
            @empty
                <div class="rounded-2xl border border-gray-200 bg-white p-12 text-center dark:border-gray-800 dark:bg-white/[0.03]">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">No regions found</h3>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Create regions first to manage assignments.</p>
                </div>
            @endforelse
        </div>

        {{-- Pagination --}}
        @if ($regions->hasPages())
            <div class="mt-6">
                {{ $regions->links() }}
            </div>
        @endif

        {{-- Delete Modal --}}
        <x-ui.modal model="openDelete" title="Remove Assignment" maxWidth="max-w-2xl">
            <div class="py-2">
                <p class="text-base text-gray-600 dark:text-gray-400 leading-relaxed">
                    Are you sure you want to remove <span class="font-bold text-gray-800 dark:text-white">"<span x-text="deleteName"></span>"</span>?
                    This action is permanent and cannot be undone.
                </p>
            </div>

            <x-slot:footer>
                <button type="button" @click="closeDelete()"
                    class="rounded-lg border border-gray-200 bg-white px-6 py-2.5 text-sm font-semibold text-gray-500 hover:bg-gray-50 transition-all focus:outline-none dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-gray-700">
                    Close
                </button>
                <form :action="actionUrl" method="POST" class="inline-block">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                        class="rounded-lg bg-red-600 px-6 py-2.5 text-sm font-bold text-white shadow-md hover:bg-red-700 transition-all active:scale-95 focus:outline-none">
                        Remove
                    </button>
                </form>
            </x-slot:footer>
        </x-ui.modal>

    </div>
@endsection
