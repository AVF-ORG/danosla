@extends('layouts.app')

@section('content')
    @include('components.breadcrumb')

    <div class="rounded-2xl border border-gray-200 bg-white px-5 py-7 dark:border-gray-800 dark:bg-white/[0.03] xl:px-10 xl:py-12"
        x-data="{
            open: false,
            actionUrl: '',
            confirmDelete(url) {
                this.actionUrl = url;
                this.open = true;
            },
            closeModal() {
                this.open = false;
                this.actionUrl = '';
            }
        }">

        <div class="mb-6 flex flex-wrap items-center justify-between gap-4">
            <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
                Translation Keys
            </h3>

            <a href="{{ route('dashboard.localization.keys.create') }}"
                class="flex items-center gap-2 rounded-lg bg-brand-500 px-4 py-2.5 text-sm font-medium text-white hover:bg-brand-600">
                <svg width="20" height="20" viewBox="0 0 20 20" fill="none">
                    <path d="M10 4V16M4 10H16" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round" />
                </svg>
                Add Key
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full min-w-[900px] text-left text-sm">
                <thead class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">
                    <tr class="border-b border-gray-100 dark:border-gray-800">
                        <th class="px-4 py-3">Group</th>
                        <th class="px-4 py-3">Key</th>
                        <th class="px-4 py-3 text-right">Actions</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                    @forelse ($keys as $key)
                        <tr>
                            <td class="px-4 py-3 text-gray-700 dark:text-gray-300">
                                {{ $key->group }}
                            </td>

                            <td class="px-4 py-3 font-medium text-gray-900 dark:text-white">
                                {{ $key->key }}
                            </td>

                            <td class="px-4 py-3 text-right">
                                <div class="flex items-center justify-end gap-3">
                                    <a href="{{ route('dashboard.localization.keys.edit', $key) }}"
                                        class="text-gray-500 hover:text-brand-500 dark:text-gray-400 dark:hover:text-brand-500">
                                        Edit
                                    </a>

                                    <button type="button"
                                        @click="confirmDelete('{{ route('dashboard.localization.keys.destroy', $key) }}')"
                                        class="text-gray-500 hover:text-error-500 dark:text-gray-400 dark:hover:text-error-500">
                                        Delete
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">
                                No keys found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Delete Modal --}}
        <x-ui.modal model="open" title="Confirm Delete" maxWidth="max-w-xl">
            <p class="text-sm text-gray-700 dark:text-gray-300">
                Are you sure you want to delete this translation key?
                This action cannot be undone.
            </p>

            <x-slot:footer>
                <button type="button" @click="closeModal()"
                    class="rounded-lg border border-gray-300 px-5 py-2.5 text-sm font-medium text-gray-700
                           hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-800">
                    Cancel
                </button>

                <form :action="actionUrl" method="POST" class="inline" @submit="closeModal()">
                    @csrf
                    @method('DELETE')

                    <button type="submit"
                        class="rounded-lg bg-error-500 px-5 py-2.5 text-sm font-semibold text-white hover:bg-error-600
                               focus:outline-none focus:ring-2 focus:ring-error-500/40">
                        Delete
                    </button>
                </form>
            </x-slot:footer>
        </x-ui.modal>

    </div>
@endsection
