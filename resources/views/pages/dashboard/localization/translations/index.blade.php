@extends('layouts.app')

@section('content')
    @include('components.breadcrumb')

    <div
        class="rounded-2xl border border-gray-200 bg-white px-5 py-7 dark:border-gray-800 dark:bg-white/[0.03] xl:px-10 xl:py-12">
        <div class="mb-6 flex flex-wrap items-center justify-between gap-4">
            <h3 class="text-xl font-semibold text-gray-900 dark:text-white">Manage Translations</h3>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full min-w-[900px] text-left text-sm">
                <thead class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">
                    <tr class="border-b border-gray-100 dark:border-gray-800">
                        <th class="px-4 py-3 text-center">Group</th>
                        <th class="px-4 py-3 text-center">Key</th>
                        <th class="px-4 py-3 text-center">Language</th>
                        <th class="px-4 py-3 text-center">Translation</th>
                        <th class="px-4 py-3 text-center">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                    @forelse ($translations as $translation)
                        <tr>
                            <td class="px-4 py-3 text-gray-700 dark:text-gray-300">{{ $translation->key->group }}</td>
                            <td class="px-4 py-3 text-gray-700 dark:text-gray-300">{{ $translation->key->key }}</td>
                            <td class="px-4 py-3 text-gray-700 dark:text-gray-300">{{ $translation->language->name }}</td>
                            <td class="px-4 py-3 font-medium text-gray-900 dark:text-white">{{ $translation->value }}</td>
                            <td class="px-4 py-3 text-right">
                                <div x-data="{ dropdownOpen: false }" class="relative inline-block text-left" @click.away="dropdownOpen = false">
                                    <!-- Dropdown Trigger -->
                                    <button @click.stop.prevent="dropdownOpen = !dropdownOpen" type="button" class="inline-flex p-2 items-center justify-center text-gray-400 hover:text-brand-600 hover:bg-brand-50 dark:hover:bg-gray-700 rounded-lg transition-colors focus:outline-none focus:ring-2 focus:ring-brand-500" title="Actions">
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z" />
                                        </svg>
                                    </button>

                                    <!-- Dropdown Menu -->
                                    <div x-show="dropdownOpen" 
                                         x-transition:enter="transition ease-out duration-100"
                                         x-transition:enter-start="transform opacity-0 scale-95"
                                         x-transition:enter-end="transform opacity-100 scale-100"
                                         x-transition:leave="transition ease-in duration-75"
                                         x-transition:leave-start="transform opacity-100 scale-100"
                                         x-transition:leave-end="transform opacity-0 scale-95"
                                         class="absolute right-0 z-50 mt-2 w-40 rounded-xl bg-white dark:bg-gray-800 shadow-xl border border-gray-100 dark:border-gray-700 focus:outline-none"
                                         style="display: none;">
                                        <div class="py-1 text-left">
                                            <a href="{{ route('dashboard.localization.translations.edit', $translation) }}" class="flex items-center px-4 py-2.5 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors" onclick="event.stopPropagation();">
                                                <svg class="w-4 h-4 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                                Éditer
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">
                                No translations found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
