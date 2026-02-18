@extends('layouts.app')

@section('content')
    <div class="mx-auto max-w-4xl">
        {{-- Header --}}
        <div class="mb-6">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
                        {{ isset($role) ? 'Edit Role' : 'Create New Role' }}
                    </h1>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                        {{ isset($role) ? 'Update role details and permissions.' : 'Define a new role and assign permissions.' }}
                    </p>
                </div>

                <a href="{{ route('dashboard.roles.index') }}"
                    class="inline-flex items-center gap-2 rounded-lg border border-gray-200 bg-white px-4 py-2.5 text-sm font-semibold text-gray-600 shadow-sm hover:bg-gray-50 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-400 dark:hover:bg-gray-800/50 transition-all">
                    Back to List
                </a>
            </div>
        </div>

        <form action="{{ isset($role) ? route('dashboard.roles.update', $role->id) : route('dashboard.roles.store') }}" method="POST">
            @csrf
            @if(isset($role))
                @method('PUT')
            @endif

            <div class="space-y-6">
                {{-- Basic Info --}}
                <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/[0.03]">
                    <div class="mb-5">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white">General Information</h3>
                    </div>

                    <div class="grid grid-cols-1 gap-6">
                        <div>
                            <label for="name" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                                Role Name
                            </label>
                            <input type="text" name="name" id="name" value="{{ old('name', isset($role) ? $role->name : '') }}"
                                class="h-11 w-full rounded-xl border border-gray-300 bg-white px-4 py-2.5 text-sm text-gray-800 focus:border-brand-500 focus:outline-none focus:ring-4 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white @error('name') border-red-500 @enderror"
                                placeholder="e.g. Administrator, Editor...">
                            @error('name')
                                <p class="mt-2 text-sm text-red-500">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                {{-- Permissions --}}
                <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/[0.03]">
                    <div class="mb-5">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white">Assign Permissions</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Select the permissions assigned to this role.</p>
                    </div>

                    @if($permissions->isEmpty())
                        <div class="flex flex-col items-center justify-center py-8 text-center">
                            <p class="text-sm text-gray-500 dark:text-gray-400 mb-3">No permissions found. Create permissions first.</p>
                            <a href="{{ route('dashboard.permissions.create') }}" class="text-brand-500 hover:text-brand-600 font-semibold text-sm">Create Permissions</a>
                        </div>
                    @else
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                            @foreach($permissions as $permission)
                                <label class="flex items-center gap-3 p-3 rounded-xl border border-gray-100 bg-gray-50/50 hover:bg-gray-50 dark:border-gray-800 dark:bg-white/[0.02] dark:hover:bg-white/[0.04] transition-colors cursor-pointer">
                                    <input type="checkbox" name="permissions[]" value="{{ $permission->id }}"
                                        {{ (collect(old('permissions'))->contains($permission->id) || (isset($rolePermissions) && in_array($permission->id, $rolePermissions))) ? 'checked' : '' }}
                                        class="h-5 w-5 rounded border-gray-300 text-brand-500 focus:ring-brand-500 dark:border-gray-700 dark:bg-gray-900">
                                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ $permission->name }}</span>
                                </label>
                            @endforeach
                        </div>
                    @endif
                </div>

                <div class="flex justify-end gap-3">
                    <button type="submit"
                        class="inline-flex items-center gap-2 rounded-lg bg-brand-500 px-6 py-3 text-sm font-bold text-white shadow-sm hover:bg-brand-600 focus-visible:outline focus-visible:outline-offset-2 focus-visible:outline-brand-500 transition-all active:scale-95">
                        <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                        </svg>
                        {{ isset($role) ? 'Update Role' : 'Create Role' }}
                    </button>
                </div>
            </div>
        </form>
    </div>
@endsection
