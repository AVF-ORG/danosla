@extends('layouts.app')

@section('content')
    <div class="mb-6">
        <div class="flex items-center gap-3">
            <a href="{{ route('dashboard.countries.index') }}"
                class="inline-flex items-center justify-center rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 dark:hover:bg-gray-800">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Create New Country</h1>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Add a new country to the system.</p>
            </div>
        </div>
    </div>

    <form method="POST" action="{{ route('dashboard.countries.store') }}" class="space-y-6">
        @include('pages.dashboard.countries._form')

        <div class="flex items-center justify-end gap-3">
            <a href="{{ route('dashboard.countries.index') }}"
                class="inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 dark:hover:bg-gray-800">
                Cancel
            </a>
            <button type="submit"
                class="inline-flex items-center gap-2 rounded-lg bg-brand-500 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-brand-600 focus-visible:outline focus-visible:outline-offset-2 focus-visible:outline-brand-500 transition-all active:scale-95">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                Create Country
            </button>
        </div>
    </form>
@endsection
