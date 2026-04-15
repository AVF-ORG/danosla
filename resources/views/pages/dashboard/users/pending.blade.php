@extends('layouts.app')

@section('content')
    <div>
        {{-- Header --}}
        <div class="mb-6">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
                        Pending Approvals
                    </h1>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                        Review and authorize system registration requests.
                    </p>
                </div>

                <div class="flex items-center gap-4 px-4 py-2 bg-amber-50 dark:bg-amber-500/10 rounded-lg border border-amber-200 dark:border-amber-800">
                    <div class="flex items-center gap-2">
                        <span class="text-sm font-bold text-amber-700 dark:text-amber-400">{{ $stats['pending_count'] }}</span>
                        <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Awaiting Review</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Search --}}
        <div class="mb-6">
            <form method="GET" action="{{ route('dashboard.users.pending') }}" class="flex gap-3">
                <div class="flex-1">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search pending requests..."
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

        {{-- Table --}}
        <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="border-b border-gray-200 bg-gray-50/50 dark:border-gray-800 dark:bg-white/[0.02]">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">
                                Name
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">
                                Email
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">
                                Phone
                            </th>
                            <th class="px-6 py-4 text-center text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">
                                Roles
                            </th>
                            <th class="px-6 py-4 text-center text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">
                                Submission Date
                            </th>
                            <th class="px-6 py-4 text-right text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-800">
                        @forelse ($users as $user)
                            <tr class="group hover:bg-gray-50/50 dark:hover:bg-white/[0.02] transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap text-left">
                                    <div class="flex items-center gap-3">
                                        <div class="flex h-10 w-10 items-center justify-center rounded-full bg-gray-100 dark:bg-white/5 text-gray-600 dark:text-gray-400 font-bold text-xs uppercase">
                                            {{ collect(explode(' ', $user->name))->map(fn($w) => substr($w, 0, 1))->take(2)->implode('') }}
                                        </div>
                                        <span class="text-sm font-medium text-gray-900 dark:text-white">
                                            {{ $user->name }}
                                        </span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-left text-sm text-gray-500 dark:text-gray-400">
                                    {{ $user->email }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-left text-sm text-gray-500 dark:text-gray-400">
                                    {{ $user->phone ?? '---' }}
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <div class="flex flex-wrap justify-center gap-1.5">
                                        @forelse ($user->roles as $role)
                                            <span class="inline-flex items-center rounded-md bg-brand-50 px-2.5 py-1 text-[10px] font-bold uppercase tracking-wider text-brand-700 dark:bg-brand-500/10 dark:text-brand-400">
                                                {{ $role->name }}
                                            </span>
                                        @empty
                                            <span class="text-[10px] font-medium text-gray-400 italic">No roles</span>
                                        @endforelse
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <div class="flex items-center justify-center gap-1.5">

                                        <span class="text-[11px] font-bold text-gray-400 tracking-tight">
                                           @if($user->created_at->diffInHours() < 24)
                                                {{ $user->created_at->diffForHumans(['short' => true]) }}
                                            @else
                                              {{ $user->created_at->format('d/m/Y H:i') }}
                                            @endif
                                        </span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
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
                                             class="absolute right-0 z-50 mt-2 w-48 rounded-xl bg-white dark:bg-gray-800 shadow-xl border border-gray-100 dark:border-gray-700 focus:outline-none"
                                             style="display: none;">
                                            <div class="py-1">
                                                <!-- Primary Approve Action -->
                                                <form action="{{ route('dashboard.users.update-status', $user->id) }}" method="POST" class="block">
                                                    @csrf
                                                    @method('PATCH')
                                                    <input type="hidden" name="status" value="active">
                                                    <button type="submit" class="flex w-full items-center px-4 py-2.5 text-sm font-bold text-brand-600 hover:bg-brand-50 dark:hover:bg-brand-900/20 transition-colors" onclick="event.stopPropagation();">
                                                        <svg class="w-4 h-4 mr-3 text-brand-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                                        </svg>
                                                        Approuver
                                                    </button>
                                                </form>

                                                <form action="{{ route('dashboard.users.update-status', $user->id) }}" method="POST" class="block">
                                                    @csrf
                                                    @method('PATCH')
                                                    <input type="hidden" name="status" value="inactive">
                                                    <button type="submit" class="flex w-full items-center px-4 py-2.5 text-sm font-bold text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors" onclick="event.stopPropagation();">
                                                        <svg class="w-4 h-4 mr-3 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                                        </svg>
                                                        Rejeter
                                                    </button>
                                                </form>

                                                <hr class="my-1 border-gray-100 dark:border-gray-700">

                                                <a href="{{ route('dashboard.users.show', $user->id) }}" class="flex items-center px-4 py-2.5 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors" onclick="event.stopPropagation();">
                                                    <svg class="w-4 h-4 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                                    Voir Détails
                                                </a>
                                                <a href="{{ route('dashboard.users.edit', $user->id) }}" class="flex items-center px-4 py-2.5 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors" onclick="event.stopPropagation();">
                                                    <svg class="w-4 h-4 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                                    Éditer
                                                </a>
                                                @if(auth()->id() != $user->id)
                                                    <button @click.stop.prevent="dropdownOpen = false; openDeleteModal = true" type="button" class="flex w-full items-center px-4 py-2.5 text-sm text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors">
                                                        <svg class="w-4 h-4 mr-3 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                        Supprimer
                                                    </button>
                                                @endif
                                            </div>
                                        </div>

                                        <!-- Delete Modal for List Item -->
                                        <template x-teleport="body">
                                            <div @click.stop>
                                                <x-ui.modal model="openDeleteModal" title="Supprimer l'utilisateur" maxWidth="max-w-md">
                                                    <div class="sm:flex sm:items-start whitespace-normal">
                                                        <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 dark:bg-red-900/30 sm:mx-0 sm:h-10 sm:w-10">
                                                            <svg class="h-6 w-6 text-red-600 dark:text-red-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                                            </svg>
                                                        </div>
                                                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                                                            <p class="text-sm text-gray-500 dark:text-gray-400 break-words">
                                                                Êtes-vous sûr de vouloir supprimer l'utilisateur <strong>{{ $user->name }}</strong> ? Cette action est irréversible.
                                                            </p>
                                                        </div>
                                                    </div>

                                                    <x-slot:footer>
                                                        <button @click.stop.prevent="openDeleteModal = false" type="button" class="w-full inline-flex justify-center rounded-xl border border-gray-300 dark:border-gray-600 shadow-sm px-4 py-2 bg-white dark:bg-gray-800 text-base font-semibold text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 sm:w-auto sm:text-sm transition-colors">
                                                            Annuler
                                                        </button>
                                                        <form action="{{ route('dashboard.users.destroy', $user->id) }}" method="POST" class="inline">
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
                                <td colspan="6" class="px-6 py-12 text-center">
                                    <div class="flex flex-col items-center justify-center">
                                        <div class="h-12 w-12 flex items-center justify-center rounded-full bg-green-50 dark:bg-green-500/10 text-green-600">
                                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                            </svg>
                                        </div>
                                        <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">All clear</h3>
                                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">No pending requests at this moment.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($users->hasPages())
                <div class="border-t border-gray-200 bg-white px-6 py-4 dark:border-gray-800 dark:bg-white/[0.03]">
                    {{ $users->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection
