@extends('layouts.app')

@section('content')
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-xl font-semibold text-gray-800 dark:text-white/90">Edit Contact Subject</h1>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Update contact subject translations.</p>
            </div>

            <a href="{{ route('dashboard.contact-subjects.index') }}"
                class="inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-3 text-theme-sm font-medium text-gray-700 shadow-theme-xs hover:bg-gray-50 hover:text-gray-800 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03] dark:hover:text-gray-200">
                Back
            </a>
        </div>
    </div>

    <form action="{{ route('dashboard.contact-subjects.update', $contactSubject->id) }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')

        @include('pages.dashboard.contact-subjects._form', ['contactSubject' => $contactSubject, 'languages' => $languages])

        <div class="flex justify-end gap-3">
            <a href="{{ route('dashboard.contact-subjects.index') }}"
                class="inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-3 text-theme-sm font-medium text-gray-700 shadow-theme-xs hover:bg-gray-50 hover:text-gray-800 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03] dark:hover:text-gray-200">
                Cancel
            </a>

            <button type="submit"
                class="inline-flex items-center gap-2 rounded-lg bg-brand-500 px-5 py-3 text-theme-sm font-medium text-white shadow-theme-xs hover:bg-brand-600">
                Update
            </button>
        </div>
    </form>
@endsection
