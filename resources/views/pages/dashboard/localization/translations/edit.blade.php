@extends('layouts.app')

@section('content')
    @include('components.breadcrumb')

    <div
        class="rounded-2xl border border-gray-200 bg-white px-5 py-7 dark:border-gray-800 dark:bg-white/[0.03] xl:px-10 xl:py-12">
        @if (session('success'))
            <div class="mb-5 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">
                {{ session('success') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="mb-5 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                <ul class="list-disc space-y-1 pl-5">
                    @foreach ($errors->all() as $e)
                        <li>{{ $e }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="mb-5 rounded-xl border border-gray-200 bg-white/50 p-4 dark:border-gray-800 dark:bg-white/[0.02]">
            <div class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Key</div>
            <div class="mt-1 text-lg font-semibold text-gray-800 dark:text-white/90">
                {{ $translationKey->key }}
            </div>
            <div class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                Group: {{ $translationKey->group }}
            </div>

            <div class="mt-3 flex flex-wrap gap-2">
                <a href="{{ route('dashboard.localization.translations.index') }}"
                    class="inline-flex items-center justify-center rounded-lg border border-gray-300 px-3 py-2 text-sm font-medium text-gray-700 transition hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-white/5">
                    Back to list
                </a>

                <a href="{{ route('dashboard.localization.keys.edit', $translationKey) }}"
                    class="inline-flex items-center justify-center rounded-lg border border-gray-300 px-3 py-2 text-sm font-medium text-gray-700 transition hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-white/5">
                    Edit key
                </a>
            </div>
        </div>

        <form method="POST" action="{{ route('dashboard.localization.translations.update', $translationKey) }}"
            class="space-y-4">
            @csrf
            @method('PUT')

            @forelse ($languages as $lang)
                @php
                    $row = $existing[$lang->id] ?? null;
                    $value = old("values.$lang->id", $row?->value);
                @endphp

                <div class="rounded-xl border border-gray-200 p-4 dark:border-gray-800">
                    <div class="mb-2 flex items-center justify-between">
                        <div class="text-sm font-semibold text-gray-800 dark:text-white/90">
                            {{ strtoupper($lang->code) }} — {{ $lang->name }}
                        </div>

                        <span class="text-xs text-gray-500 dark:text-gray-400">
                            {{ $lang->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </div>

                    <textarea name="values[{{ $lang->id }}]" rows="2" placeholder="Translation value..."
                        class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-none dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30">{{ $value }}</textarea>
                </div>
            @empty
                <div class="rounded-lg border border-yellow-200 bg-yellow-50 px-4 py-3 text-sm text-yellow-700">
                    No active languages found. Create and activate at least one language first.
                </div>
            @endforelse

            <div class="pt-2">
                <button type="submit"
                    class="inline-flex items-center justify-center rounded-lg bg-brand-500 px-4 py-2.5 text-sm font-medium text-white transition hover:bg-brand-600">
                    Save Translations
                </button>
            </div>
        </form>
    </div>
@endsection
