@extends('layouts.app')

@section('content')
    <div>

        {{-- Header --}}
        <div class="mb-6">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
                        Contact Messages
                    </h1>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                        View and manage incoming messages from the platform.
                    </p>
                </div>
            </div>
        </div>

        {{-- Filters & Search --}}
        <div class="mb-6 rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03]">
            <form action="{{ route('dashboard.contacts.index') }}" method="GET" class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
                {{-- Search --}}
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </span>
                    <input type="text" name="search" value="{{ request('search') }}"
                        class="block w-full rounded-lg border border-gray-300 bg-transparent py-2 pl-10 pr-3 text-sm focus:border-brand-500 focus:ring-brand-500 dark:border-gray-700 dark:text-white"
                        placeholder="Search by name, email...">
                </div>

                {{-- Subject Filter --}}
                <select name="subject_id"
                    class="block w-full rounded-lg border border-gray-300 bg-transparent py-2 px-3 text-sm focus:border-brand-500 focus:ring-brand-500 dark:border-gray-700 dark:text-white">
                    <option value="">All Subjects</option>
                    @foreach($subjects as $subject)
                        <option value="{{ $subject->id }}" {{ request('subject_id') == $subject->id ? 'selected' : '' }}>
                            {{ $subject->name }}
                        </option>
                    @endforeach
                </select>

                {{-- Status Filter --}}
                <select name="status"
                    class="block w-full rounded-lg border border-gray-300 bg-transparent py-2 px-3 text-sm focus:border-brand-500 focus:ring-brand-500 dark:border-gray-700 dark:text-white">
                    <option value="">All Status</option>
                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="replied" {{ request('status') === 'replied' ? 'selected' : '' }}>Replied</option>
                </select>

                <div class="flex gap-2">
                    <button type="submit"
                        class="flex-1 rounded-lg bg-brand-500 px-4 py-2 text-sm font-semibold text-white hover:bg-brand-600 transition-all">
                        Filter
                    </button>
                    <a href="{{ route('dashboard.contacts.index') }}"
                        class="flex items-center justify-center rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400">
                        Reset
                    </a>
                </div>
            </form>
        </div>

        {{-- Table --}}
        <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
            <div class="border-t border-gray-100 p-6 dark:border-gray-800">
                <div class="overflow-x-auto">
                    <table class="min-w-full">
                        <thead>
                            <tr class="border-y border-gray-100 dark:border-white/[0.05]">
                                <th class="px-6 py-4 text-center text-theme-xs font-semibold uppercase tracking-wider text-gray-500">Sender</th>
                                <th class="px-6 py-4 text-center text-theme-xs font-semibold uppercase tracking-wider text-gray-500">Subject</th>
                                <th class="px-6 py-4 text-center text-theme-xs font-semibold uppercase tracking-wider text-gray-500">Status</th>
                                <th class="px-6 py-4 text-center text-theme-xs font-semibold uppercase tracking-wider text-gray-500">Date</th>
                                <th class="px-6 py-4 text-center text-theme-xs font-semibold uppercase tracking-wider text-gray-500">Actions</th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-gray-100 dark:divide-white/[0.05]">
                            @forelse($contacts as $contact)
                                <tr class="group transition-colors hover:bg-gray-50/50 dark:hover:bg-white/[0.02]">
                                    <td class="px-6 py-4">
                                        <div class="flex flex-col">
                                            <span class="text-theme-sm font-semibold text-gray-800 dark:text-white/90">
                                                {{ $contact->name }}
                                            </span>
                                            <span class="text-theme-xs text-gray-500 dark:text-gray-400">
                                                {{ $contact->email }}
                                            </span>
                                        </div>
                                    </td>

                                    <td class="px-6 py-4">
                                        <span class="text-theme-sm text-gray-600 dark:text-gray-300">
                                            {{ $contact->subject->name ?? 'N/A' }}
                                        </span>
                                    </td>

                                    <td class="px-6 py-4 text-center">
                                        @if ($contact->isReplied())
                                            <span class="inline-flex items-center rounded-full bg-green-500 px-3 py-0.5 text-[10px] font-bold uppercase tracking-wider text-white shadow-sm">
                                                Replied
                                            </span>
                                        @else
                                            <span class="inline-flex items-center rounded-full bg-amber-500 px-3 py-0.5 text-[10px] font-bold uppercase tracking-wider text-white shadow-sm">
                                                Pending
                                            </span>
                                        @endif
                                    </td>

                                    <td class="px-6 py-4 text-theme-sm text-gray-500">
                                        {{ $contact->created_at->format('M d, Y') }}
                                        <br>
                                        <span class="text-[10px]">{{ $contact->created_at->format('H:i') }}</span>
                                    </td>

                                    <td class="px-6 py-4">
                                        <div x-data="{ dropdownOpen: false, openDeleteModal: false }" class="relative inline-block text-left w-full" @click.away="dropdownOpen = false">
                                            <div class="flex justify-end">
                                                <!-- Dropdown Trigger -->
                                                <button @click.stop.prevent="dropdownOpen = !dropdownOpen" type="button" class="inline-flex p-2 items-center justify-center text-gray-400 hover:text-brand-600 hover:bg-brand-50 dark:hover:bg-gray-700 rounded-lg transition-colors focus:outline-none focus:ring-2 focus:ring-brand-500" title="Actions">
                                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                                        <path d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z" />
                                                    </svg>
                                                </button>
                                            </div>

                                            <!-- Dropdown Menu -->
                                            <div x-show="dropdownOpen" 
                                                 x-transition:enter="transition ease-out duration-100"
                                                 x-transition:enter-start="transform opacity-0 scale-95"
                                                 x-transition:enter-end="transform opacity-100 scale-100"
                                                 x-transition:leave="transition ease-in duration-75"
                                                 x-transition:leave-start="transform opacity-100 scale-100"
                                                 x-transition:leave-end="transform opacity-0 scale-95"
                                                 class="absolute right-0 z-50 mt-2 w-48 rounded-xl bg-white dark:bg-gray-800 shadow-xl border border-gray-100 dark:border-gray-700 focus:outline-none"
                                                 style="display: none;">
                                                <div class="py-1">
                                                    <a href="{{ route('dashboard.contacts.show', $contact->id) }}" class="flex items-center px-4 py-2.5 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors" onclick="event.stopPropagation();">
                                                        <svg class="w-4 h-4 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/></svg>
                                                        Voir & Répondre
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
                                                    <x-ui.modal model="openDeleteModal" title="Supprimer le message" maxWidth="max-w-md">
                                                        <div class="sm:flex sm:items-start whitespace-normal">
                                                            <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 dark:bg-red-900/30 sm:mx-0 sm:h-10 sm:w-10">
                                                                <svg class="h-6 w-6 text-red-600 dark:text-red-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                                                </svg>
                                                            </div>
                                                            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                                                                <p class="text-sm text-gray-500 dark:text-gray-400 break-words">
                                                                    Êtes-vous sûr de vouloir supprimer le message de <strong>{{ $contact->name }}</strong> ? Cette action est irréversible.
                                                                </p>
                                                            </div>
                                                        </div>

                                                        <x-slot:footer>
                                                            <button @click.stop.prevent="openDeleteModal = false" type="button" class="w-full inline-flex justify-center rounded-xl border border-gray-300 dark:border-gray-600 shadow-sm px-4 py-2 bg-white dark:bg-gray-800 text-base font-semibold text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 sm:w-auto sm:text-sm transition-colors">
                                                                Annuler
                                                            </button>
                                                            <form action="{{ route('dashboard.contacts.destroy', $contact->id) }}" method="POST" class="inline">
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
                                    <td colspan="5" class="px-6 py-12 text-center">
                                        <div class="flex flex-col items-center justify-center">
                                            <p class="text-gray-500 dark:text-gray-400 font-medium">No messages found</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-6">
                    {{ $contacts->withQueryString()->links() }}
                </div>
            </div>
        </div>



    </div>
@endsection
