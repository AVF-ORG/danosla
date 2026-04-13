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
                                <th class="px-6 py-4 text-left text-theme-xs font-semibold uppercase tracking-wider text-gray-500">Sender</th>
                                <th class="px-6 py-4 text-left text-theme-xs font-semibold uppercase tracking-wider text-gray-500">Subject</th>
                                <th class="px-6 py-4 text-left text-theme-xs font-semibold uppercase tracking-wider text-gray-500">Status</th>
                                <th class="px-6 py-4 text-left text-theme-xs font-semibold uppercase tracking-wider text-gray-500">Date</th>
                                <th class="px-6 py-4 text-right text-theme-xs font-semibold uppercase tracking-wider text-gray-500">Actions</th>
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

                                    <td class="px-6 py-4">
                                        @if ($contact->isReplied())
                                            <span class="inline-flex items-center gap-1.5 rounded-full bg-success-50 px-2.5 py-0.5 text-xs font-medium text-success-700 dark:bg-success-500/10 dark:text-success-400">
                                                Replied
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1.5 rounded-full bg-brand-50 px-2.5 py-0.5 text-xs font-medium text-brand-700 dark:bg-brand-500/10 dark:text-brand-400">
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
                                        <div class="flex justify-end gap-2">
                                            <a href="{{ route('dashboard.contacts.show', $contact->id) }}"
                                                class="p-2 text-gray-400 hover:text-brand-500 transition-colors" title="View & Reply">
                                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                                </svg>
                                            </a>

                                            <button type="button"
                                                @click="confirmDelete('{{ route('dashboard.contacts.destroy', $contact->id) }}','{{ $contact->name }}')"
                                                class="p-2 text-gray-400 hover:text-error-500 transition-colors" title="Delete">
                                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
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

        {{-- Delete Modal --}}
        <x-ui.modal model="openDelete" title="Confirm Delete" maxWidth="max-w-2xl">
            <div class="py-2">
                <p class="text-base text-gray-600 dark:text-gray-400 leading-relaxed">
                    Are you sure you want to delete this message from <span class="font-bold text-gray-800 dark:text-white">"<span x-text="deleteName"></span>"</span>?
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
