@extends('layouts.app')

@section('content')
    @include('components.breadcrumb')

    <div class="rounded-2xl border border-gray-200 bg-white px-5 py-7 dark:border-gray-800 dark:bg-white/[0.03] xl:px-10 xl:py-12">

        <div class="mb-6 flex flex-wrap items-center justify-between gap-4">
            <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
                Translation Keys
            </h3>

            <a href="{{ route('dashboard.localization.keys.create') }}"
                class="flex items-center gap-2 rounded-lg bg-brand-500 px-4 py-2.5 text-sm font-medium text-white hover:bg-brand-600">
                <svg width="20" height="20" viewBox="0 0 20 20" fill="none">
                    <path d="M10 4V16M4 10H16" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round" />
                </svg>
                Add Key
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full min-w-[900px] text-left text-sm">
                <thead class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">
                    <tr class="border-b border-gray-100 dark:border-gray-800">
                        <th class="px-4 py-3 text-center">Group</th>
                        <th class="px-4 py-3 text-center">Key</th>
                        <th class="px-4 py-3 text-center">Actions</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                    @forelse ($keys as $key)
                        <tr>
                            <td class="px-4 py-3 text-gray-700 dark:text-gray-300">
                                {{ $key->group }}
                            </td>

                            <td class="px-4 py-3 font-medium text-gray-900 dark:text-white">
                                {{ $key->key }}
                            </td>

                            <td class="px-4 py-3 text-right">
                                <div x-data="{ dropdownOpen: false, openDeleteModal: false }" class="relative inline-block text-left" @click.away="dropdownOpen = false">
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
                                            <a href="{{ route('dashboard.localization.keys.edit', $key) }}" class="flex items-center px-4 py-2.5 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors" onclick="event.stopPropagation();">
                                                <svg class="w-4 h-4 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                                Éditer
                                            </a>
                                            <button @click.stop.prevent="dropdownOpen = false; openDeleteModal = true" type="button" class="flex w-full items-center px-4 py-2.5 text-sm text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors">
                                                <svg class="w-4 h-4 mr-3 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                Supprimer
                                            </button>
                                        </div>
                                    </div>

                                    <!-- Delete Modal for List Item -->
                                    <template x-teleport="body">
                                        <div @click.stop>
                                            <x-ui.modal model="openDeleteModal" title="Supprimer la clé" maxWidth="max-w-md">
                                                <div class="sm:flex sm:items-start whitespace-normal">
                                                    <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 dark:bg-red-900/30 sm:mx-0 sm:h-10 sm:w-10">
                                                        <svg class="h-6 w-6 text-red-600 dark:text-red-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                                        </svg>
                                                    </div>
                                                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                                                        <p class="text-sm text-gray-500 dark:text-gray-400 break-words">
                                                            Êtes-vous sûr de vouloir supprimer la clé <strong>{{ $key->key }}</strong> ? Cette action est irréversible.
                                                        </p>
                                                    </div>
                                                </div>

                                                <x-slot:footer>
                                                    <button @click.stop.prevent="openDeleteModal = false" type="button" class="w-full inline-flex justify-center rounded-xl border border-gray-300 dark:border-gray-600 shadow-sm px-4 py-2 bg-white dark:bg-gray-800 text-base font-semibold text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 sm:w-auto sm:text-sm transition-colors">
                                                        Annuler
                                                    </button>
                                                    <form action="{{ route('dashboard.localization.keys.destroy', $key) }}" method="POST" class="inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="w-full inline-flex justify-center rounded-xl border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-semibold text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:w-auto sm:text-sm transition-colors" @click.stop>
                                                            Supprimer définitivement
                                                        </button>
                                                    </form>
                                                </x-slot:footer>
                                            </x-ui.modal>
                                        </div>
                                    </template>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">
                                No keys found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>



    </div>
@endsection
