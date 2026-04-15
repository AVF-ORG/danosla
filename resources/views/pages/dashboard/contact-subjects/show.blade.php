@extends('layouts.app')

@section('content')
    <div class="mb-6">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Contact Subject Details</h1>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Comprehensive overview of the contact subject and its translations.</p>
            </div>

            <div class="flex items-center gap-3">
                <a href="{{ route('dashboard.contact-subjects.index') }}"
                    class="inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 Transition-all active:scale-95">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Back
                </a>

                <a href="{{ route('dashboard.contact-subjects.edit', $contactSubject->id) }}"
                    class="inline-flex items-center gap-2 rounded-lg bg-brand-500 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-brand-600 Transition-all active:scale-95">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                    Edit Subject
                </a>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        {{-- Main Info --}}
        <div class="lg:col-span-1 space-y-6">
            <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-800 dark:bg-white/[0.03]">
                <h3 class="text-base font-semibold text-gray-800 dark:text-white/90 mb-4">Identification</h3>
                <div class="space-y-4">
                    <div>
                        <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">ID Number</p>
                        <p class="mt-1 text-xl font-bold text-gray-900 dark:text-white">#{{ $contactSubject->id }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Primary Name</p>
                        <p class="mt-1 text-lg font-semibold text-brand-500">{{ $contactSubject->name }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Created At</p>
                        <p class="mt-1 text-sm text-gray-700 dark:text-gray-300">{{ optional($contactSubject->created_at)->format('M d, Y H:i') }}</p>
                    </div>
                </div>
            </div>

            <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-800 dark:bg-white/[0.03]">
                <h3 class="text-base font-semibold text-gray-800 dark:text-white/90 mb-4">Status</h3>
                <div class="flex items-center gap-3">
                    @if ($contactSubject->is_active)
                        <span class="inline-flex items-center gap-1.5 rounded-full bg-success-50 px-3 py-1 text-sm font-semibold text-success-700 dark:bg-success-500/10 dark:text-success-400">
                            <span class="h-2 w-2 rounded-full bg-success-600 dark:bg-success-400 animate-pulse"></span>
                            Active
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1.5 rounded-full bg-gray-100 px-3 py-1 text-sm font-semibold text-gray-600 dark:bg-white/5 dark:text-gray-400">
                            <span class="h-2 w-2 rounded-full bg-gray-400"></span>
                            Inactive
                        </span>
                    @endif
                </div>
                <p class="mt-4 text-xs text-gray-500 dark:text-gray-400 italic">
                    {{ $contactSubject->is_active ? 'Visible to all users on the platform.' : 'Hidden from the public frontend.' }}
                </p>
            </div>
        </div>

        {{-- Translations --}}
        <div class="lg:col-span-2">
            <div class="rounded-2xl border border-gray-200 bg-white shadow-sm overflow-hidden dark:border-gray-800 dark:bg-white/[0.03]">
                <div class="px-6 py-5 border-b border-gray-100 dark:border-gray-800">
                    <h3 class="text-base font-semibold text-gray-800 dark:text-white/90">Translation Overview</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-100 dark:divide-white/[0.05]">
                        <thead class="bg-gray-50 dark:bg-gray-900/50">
                            <tr>
                                <th class="px-6 py-4 text-center text-theme-xs font-bold text-gray-500 uppercase tracking-wider">Language</th>
                                <th class="px-6 py-4 text-center text-theme-xs font-bold text-gray-500 uppercase tracking-wider">Value</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-white/[0.05]">
                            @forelse ($contactSubject->getTranslations('name') ?? [] as $code => $value)
                                <tr class="group transition-colors hover:bg-gray-50/50 dark:hover:bg-white/[0.02]">
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-3">
                                            <span class="inline-flex h-8 w-8 items-center justify-center rounded-lg bg-gray-100 text-xs font-bold text-gray-600 dark:bg-gray-800 dark:text-gray-400 border border-gray-200 dark:border-gray-700">
                                                {{ strtoupper($code) }}
                                            </span>
                                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">
                                                Language ({{ strtoupper($code) }})
                                            </span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ $value }}</p>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="2" class="px-6 py-10 text-center text-gray-400">No translations available</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
