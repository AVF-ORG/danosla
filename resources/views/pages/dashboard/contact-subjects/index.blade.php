@extends('layouts.app')

@section('content')
    <div x-data="{
        openDelete: false,
        actionUrl: '',
        deleteName: '',
        confirmDelete(url, name) {
            this.actionUrl = url
            this.deleteName = name
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
                        Contact Subjects Management
                    </h1>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                        Total control over your contact subjects and their translations.
                    </p>
                </div>

                <div class="flex flex-wrap items-center gap-3">
                    <div class="flex items-center rounded-lg border border-gray-200 bg-white p-1 dark:border-gray-800 dark:bg-gray-900">
                        <a href="{{ route('dashboard.contact-subjects.index') }}"
                            class="rounded-md px-3 py-1.5 text-xs font-medium transition-colors {{ !request('trash') ? 'bg-brand-500 text-white' : 'text-gray-500 hover:text-gray-700 dark:hover:text-gray-300' }}">
                            Active
                        </a>
                        <a href="{{ route('dashboard.contact-subjects.index', ['trash' => 1]) }}"
                            class="rounded-md px-3 py-1.5 text-xs font-medium transition-colors {{ request('trash') ? 'bg-brand-500 text-white' : 'text-gray-500 hover:text-gray-700 dark:hover:text-gray-300' }}">
                            Trash
                        </a>
                    </div>

                    <a href="{{ route('dashboard.contact-subjects.create') }}"
                        class="inline-flex items-center gap-2 rounded-lg bg-brand-500 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-brand-600 focus-visible:outline focus-visible:outline-offset-2 focus-visible:outline-brand-500 transition-all active:scale-95">
                        <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                        </svg>
                        Add Contact Subject
                    </a>
                </div>
            </div>
        </div>

        {{-- Stats --}}
        <div class="mb-8 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03]">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Subjects</p>
                        <h4 class="mt-2 text-2xl font-bold text-gray-900 dark:text-white">{{ $contactSubjects->total() }}</h4>
                    </div>
                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-brand-50 text-brand-600 dark:bg-brand-500/10 dark:text-brand-400">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                        </svg>
                    </div>
                </div>
            </div>

            <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03]">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Active Now</p>
                        <h4 class="mt-2 text-2xl font-bold text-gray-900 dark:text-white">{{ $contactSubjects->where('is_active', true)->count() }}</h4>
                    </div>
                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-success-50 text-success-600 dark:bg-success-500/10 dark:text-success-400">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
            </div>

            <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03]">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Translations</p>
                        <h4 class="mt-2 text-2xl font-bold text-gray-900 dark:text-white">{{ count($languages ?? []) }}</h4>
                    </div>
                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-blue-50 text-blue-600 dark:bg-blue-500/10 dark:text-blue-400">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 9h7M11 21l5-10 5 10M12.751 5C11.783 10.77 8.07 15.61 3 18.129" />
                        </svg>
                    </div>
                </div>
            </div>

            <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03]">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Deleted</p>
                        <h4 class="mt-2 text-2xl font-bold text-gray-900 dark:text-white">{{ $contactSubjects->where('deleted_at', '!=', null)->count() }}</h4>
                    </div>
                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-error-50 text-error-600 dark:bg-error-500/10 dark:text-error-400">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                    </div>
                </div>
            </div>
        </div>


        {{-- Table --}}
        <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
            <div class="px-6 py-5">
                <h3 class="text-base font-medium text-gray-800 dark:text-white/90">
                    {{ request('trash') ? 'Deleted Contact Subjects' : 'All Contact Subjects' }}
                </h3>
            </div>

            <div class="border-t border-gray-100 p-6 dark:border-gray-800">
                <div class="overflow-x-auto">
                    <table class="min-w-full">
                        <thead>
                            <tr class="border-y border-gray-100 dark:border-white/[0.05]">
                                <th class="px-6 py-4 text-left text-theme-xs font-semibold uppercase tracking-wider text-gray-500">ID</th>
                                <th class="px-6 py-4 text-left text-theme-xs font-semibold uppercase tracking-wider text-gray-500">Subject Name</th>
                                <th class="px-6 py-4 text-left text-theme-xs font-semibold uppercase tracking-wider text-gray-500">Status</th>
                                <th class="px-6 py-4 text-left text-theme-xs font-semibold uppercase tracking-wider text-gray-500">Translations</th>
                                <th class="px-6 py-4 text-left text-theme-xs font-semibold uppercase tracking-wider text-gray-500">Created</th>
                                <th class="px-6 py-4 text-right text-theme-xs font-semibold uppercase tracking-wider text-gray-500">Actions</th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-gray-100 dark:divide-white/[0.05]">
                            @forelse($contactSubjects as $contactSubject)
                                <tr class="group transition-colors hover:bg-gray-50/50 dark:hover:bg-white/[0.02]">
                                    <td class="px-6 py-4 text-theme-sm font-medium text-gray-500">
                                        #{{ $contactSubject->id }}
                                    </td>

                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-3">
                                            <div class="h-8 w-8 rounded-lg bg-gray-100 dark:bg-white/5 flex items-center justify-center text-brand-500 font-bold text-xs uppercase">
                                                {{ substr($contactSubject->name, 0, 2) }}
                                            </div>
                                            <span class="text-theme-sm font-semibold text-gray-800 dark:text-white/90">
                                                {{ $contactSubject->name }}
                                            </span>
                                        </div>
                                    </td>

                                    <td class="px-6 py-4">
                                        @if ($contactSubject->is_active)
                                            <span class="inline-flex items-center gap-1.5 rounded-full bg-success-50 px-2.5 py-0.5 text-xs font-medium text-success-700 dark:bg-success-500/10 dark:text-success-400">
                                                <span class="h-1.5 w-1.5 rounded-full bg-success-600 dark:bg-success-400"></span>
                                                Active
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1.5 rounded-full bg-gray-100 px-2.5 py-0.5 text-xs font-medium text-gray-600 dark:bg-white/5 dark:text-gray-400">
                                                <span class="h-1.5 w-1.5 rounded-full bg-gray-400"></span>
                                                Inactive
                                            </span>
                                        @endif
                                    </td>

                                    <td class="px-6 py-4">
                                        <div class="flex -space-x-2">
                                            @foreach ($contactSubject->getTranslations('name') as $code => $val)
                                                <span class="inline-flex h-6 w-6 items-center justify-center rounded-full border-2 border-white bg-gray-100 text-[10px] font-bold text-gray-600 ring-1 ring-gray-200 dark:border-gray-900 dark:bg-gray-800 dark:text-gray-400 dark:ring-gray-700" title="{{ strtoupper($code) }}">
                                                    {{ strtoupper($code) }}
                                                </span>
                                            @endforeach
                                        </div>
                                    </td>

                                    <td class="px-6 py-4 text-theme-sm text-gray-500">
                                        {{ optional($contactSubject->created_at)->format('M d, Y') }}
                                    </td>

                                    <td class="px-6 py-4">
                                        <div class="flex justify-end gap-2">
                                            <a href="{{ route('dashboard.contact-subjects.show', $contactSubject) }}"
                                                class="p-2 text-gray-400 hover:text-brand-500 transition-colors" title="View">
                                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                </svg>
                                            </a>

                                            @if (!request('trash'))
                                                <a href="{{ route('dashboard.contact-subjects.edit', $contactSubject) }}"
                                                    class="p-2 text-gray-400 hover:text-blue-500 transition-colors" title="Edit">
                                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                    </svg>
                                                </a>

                                                <button type="button"
                                                    @click="confirmDelete('{{ route('dashboard.contact-subjects.destroy', $contactSubject) }}','{{ $contactSubject->name }}')"
                                                    class="p-2 text-gray-400 hover:text-error-500 transition-colors" title="Delete">
                                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                    </svg>
                                                </button>
                                            @else
                                                <a href="{{ route('dashboard.contact-subjects.restore', $contactSubject->id) }}"
                                                    class="p-2 text-gray-400 hover:text-success-500 transition-colors" title="Restore">
                                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                                    </svg>
                                                </a>

                                                <button type="button"
                                                    @click="confirmDelete('{{ route('dashboard.contact-subjects.forceDelete', $contactSubject->id) }}','{{ $contactSubject->name }}')"
                                                    class="p-2 text-gray-400 hover:text-error-700 transition-colors" title="Force Delete">
                                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                                                    </svg>
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-12 text-center">
                                        <div class="flex flex-col items-center justify-center">
                                            <div class="h-16 w-16 mb-4 rounded-full bg-gray-50 flex items-center justify-center text-gray-400 dark:bg-white/5">
                                                <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                                                </svg>
                                            </div>
                                            <p class="text-gray-500 dark:text-gray-400 font-medium">No contact subjects found</p>
                                            <p class="text-gray-400 text-sm mt-1">Try changing your filters or add a new subject.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-6">
                    {{ $contactSubjects->withQueryString()->links() }}
                </div>
            </div>
        </div>


        {{-- Delete Modal --}}
        <x-ui.modal model="openDelete" title="Confirm Delete" maxWidth="max-w-2xl">
            <div class="py-2">
                <p class="text-base text-gray-600 dark:text-gray-400 leading-relaxed">
                    Are you sure you want to delete <span class="font-bold text-gray-800 dark:text-white">"<span x-text="deleteName"></span>"</span>?
                    This action is permanent and cannot be undone.
                </p>
            </div>

            <x-slot:footer>
                <button type="button" @click="closeDelete()"
                    class="rounded-lg border border-gray-200 bg-white px-6 py-2.5 text-sm font-semibold text-gray-500 hover:bg-gray-50 transition-all focus:outline-none">
                    Close
                </button>
                <form :action="actionUrl" method="POST" class="inline-block">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                        class="rounded-lg bg-red-600 px-6 py-2.5 text-sm font-bold text-white shadow-md hover:bg-red-700 transition-all active:scale-95 focus:outline-none">
                        Delete
                    </button>
                </form>
            </x-slot:footer>
        </x-ui.modal>

    </div>
@endsection
