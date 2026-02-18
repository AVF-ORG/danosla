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
                        User Management
                    </h1>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                        Manage system users, their roles, and account statuses.
                    </p>
                </div>

                <div class="flex flex-wrap items-center gap-3">
                    <div class="flex items-center gap-4 mr-4 px-4 py-2 bg-gray-50 dark:bg-white/5 rounded-lg border border-gray-200 dark:border-gray-800">
                        <div class="flex items-center gap-2">
                            <span class="text-sm font-bold text-gray-900 dark:text-white">{{ $stats['total'] }}</span>
                            <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Total</span>
                        </div>
                        <span class="h-4 w-px bg-gray-200 dark:bg-gray-700"></span>
                        <div class="flex items-center gap-2">
                            <span class="text-sm font-bold text-green-600 dark:text-green-400">{{ $stats['active'] }}</span>
                            <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Active</span>
                        </div>
                    </div>
                    
                    {{-- User management doesn't usually have a 'Create' button here if it's strictly management, but adding one if needed or just stats --}}
                </div>
            </div>
        </div>

        {{-- Filters --}}
        <div class="mb-6">
            <form method="GET" action="{{ route('dashboard.users.index') }}" class="flex flex-wrap gap-3">
                <div class="flex-1 min-w-[200px]">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by name, email..."
                        class="h-11 w-full rounded-xl border border-gray-300 bg-white px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:border-brand-500 focus:outline-none focus:ring-4 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-gray-500">
                </div>
                
                <div class="w-full sm:w-auto">
                    <select name="status" onchange="this.form.submit()"
                        class="h-11 w-full sm:w-48 rounded-xl border border-gray-300 bg-white px-4 py-2.5 text-sm text-gray-800 focus:border-brand-500 focus:outline-none focus:ring-4 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
                        <option value="">All Statuses</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>

                <button type="submit"
                    class="inline-flex items-center gap-2 rounded-xl bg-gray-100 px-6 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-200 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700 transition-colors">
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
                                User Details
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">
                                Roles
                            </th>
                            <th class="px-6 py-4 text-center text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">
                                Status
                            </th>
                            <th class="px-6 py-4 text-right text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-800">
                        @forelse ($users as $user)
                            <tr class="group hover:bg-gray-50/50 dark:hover:bg-white/[0.02] transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center gap-3">
                                        <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-gray-100 dark:bg-white/5 text-gray-600 dark:text-gray-400 font-bold text-sm">
                                            {{ strtoupper(substr($user->name, 0, 1)) }}
                                        </div>
                                        <div>
                                            <div class="text-sm font-medium text-gray-900 dark:text-white">
                                                {{ $user->name }}
                                            </div>
                                            <div class="text-xs text-gray-500 dark:text-gray-400">
                                                {{ $user->email }}
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex flex-wrap gap-1.5">
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
                                    @php
                                        $statusConfig = [
                                            'active' => 'bg-green-100 text-green-700 dark:bg-green-500/10 dark:text-green-400',
                                            'pending' => 'bg-amber-100 text-amber-700 dark:bg-amber-500/10 dark:text-amber-400',
                                            'inactive' => 'bg-red-100 text-red-700 dark:bg-red-500/10 dark:text-red-400',
                                        ][$user->status] ?? 'bg-gray-100 text-gray-700';
                                    @endphp
                                    <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-[10px] font-bold uppercase tracking-widest {{ $statusConfig }}">
                                        {{ $user->status }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex items-center justify-end gap-2">
                                        <a href="{{ route('dashboard.users.show', $user->id) }}"
                                            class="p-2 text-gray-400 hover:text-brand-500 transition-colors" title="View Details">
                                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                        </a>
                                        <a href="{{ route('dashboard.users.edit', $user->id) }}"
                                            class="p-2 text-gray-400 hover:text-brand-500 transition-colors" title="Edit">
                                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                        </a>
                                        @if(auth()->id() != $user->id)
                                            <button @click="confirmDelete('{{ route('dashboard.users.destroy', $user->id) }}', '{{ $user->name }}')"
                                                class="p-2 text-gray-400 hover:text-red-500 transition-colors" title="Delete">
                                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-12 text-center border-none">
                                    <div class="flex flex-col items-center justify-center">
                                        <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">No users found</h3>
                                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Try adjusting your filters.</p>
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

        {{-- Delete Modal --}}
        <x-ui.modal model="openDelete" title="Confirm Delete" maxWidth="max-w-2xl">
            <div class="py-2">
                <p class="text-base text-gray-600 dark:text-gray-400 leading-relaxed">
                    Are you sure you want to delete <span class="font-bold text-gray-800 dark:text-white">"<span x-text="deleteName"></span>"</span>?
                    This action will archive the user and cannot be undone.
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
                        Delete
                    </button>
                </form>
            </x-slot:footer>
        </x-ui.modal>

    </div>
@endsection
