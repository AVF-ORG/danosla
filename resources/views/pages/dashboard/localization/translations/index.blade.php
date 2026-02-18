@extends('layouts.app')

@section('content')
    @include('components.breadcrumb')

    <div
        class="rounded-2xl border border-gray-200 bg-white px-5 py-7 dark:border-gray-800 dark:bg-white/[0.03] xl:px-10 xl:py-12">
        <div class="mb-6 flex flex-wrap items-center justify-between gap-4">
            <h3 class="text-xl font-semibold text-gray-900 dark:text-white">Manage Translations</h3>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full min-w-[900px] text-left text-sm">
                <thead class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">
                    <tr class="border-b border-gray-100 dark:border-gray-800">
                        <th class="px-4 py-3">Group</th>
                        <th class="px-4 py-3">Key</th>
                        <th class="px-4 py-3">Language</th>
                        <th class="px-4 py-3">Translation</th>
                        <th class="px-4 py-3 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                    @forelse ($translations as $translation)
                        <tr>
                            <td class="px-4 py-3 text-gray-700 dark:text-gray-300">{{ $translation->key->group }}</td>
                            <td class="px-4 py-3 text-gray-700 dark:text-gray-300">{{ $translation->key->key }}</td>
                            <td class="px-4 py-3 text-gray-700 dark:text-gray-300">{{ $translation->language->name }}</td>
                            <td class="px-4 py-3 font-medium text-gray-900 dark:text-white">{{ $translation->value }}</td>
                            <td class="px-4 py-3 text-right">
                                <a href="{{ route('dashboard.localization.translations.edit', $translation) }}"
                                    class="text-gray-500 hover:text-brand-500 dark:text-gray-400 dark:hover:text-brand-500">
                                    Edit
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">
                                No translations found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
