@extends('layouts.app')

@section('content')
    <div class="mx-auto max-w-4xl">
        {{-- Header --}}
        <div class="mb-6">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
                        Edit Identity
                    </h1>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                        Update system profile for <span class="font-bold text-gray-900 dark:text-white">{{ $user->name }}</span>.
                    </p>
                </div>

                <a href="{{ route('dashboard.users.index') }}"
                    class="inline-flex items-center gap-2 rounded-lg border border-gray-200 bg-white px-4 py-2.5 text-sm font-semibold text-gray-600 shadow-sm hover:bg-gray-50 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-400 dark:hover:bg-gray-800/50 transition-all">
                    Back to List
                </a>
            </div>
        </div>

        <form action="{{ route('dashboard.users.update', $user->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="space-y-6">
                {{-- Basic Identity --}}
                <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/[0.03]">
                    <div class="mb-5 border-b border-gray-100 dark:border-gray-800 pb-4">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white">Profile Details</h3>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <div>
                            <label for="name" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                                Full Name
                            </label>
                            <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}"
                                class="h-11 w-full rounded-xl border border-gray-300 bg-white px-4 py-2.5 text-sm text-gray-800 focus:border-brand-500 focus:outline-none dark:border-gray-700 dark:bg-gray-900 dark:text-white @error('name') border-red-500 @enderror">
                            @error('name') <p class="mt-2 text-sm text-red-500">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="email" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                                Email Connection
                            </label>
                            <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}"
                                class="h-11 w-full rounded-xl border border-gray-300 bg-white px-4 py-2.5 text-sm text-gray-800 focus:border-brand-500 focus:outline-none dark:border-gray-700 dark:bg-gray-900 dark:text-white @error('email') border-red-500 @enderror">
                            @error('email') <p class="mt-2 text-sm text-red-500">{{ $message }}</p> @enderror
                        </div>

                        <div class="sm:col-span-2">
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3">Account Lifecycle Status</label>
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                                @foreach($statuses as $value => $label)
                                    <label class="relative flex cursor-pointer items-center justify-center rounded-xl border p-3 transition-all {{ old('status', $user->status) == $value ? 'border-brand-500 bg-brand-50/20 dark:bg-brand-500/10' : 'border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900' }} group">
                                        <input type="radio" name="status" value="{{ $value }}" class="sr-only" {{ old('status', $user->status) == $value ? 'checked' : '' }}>
                                        <div class="flex items-center gap-2">
                                            <div class="h-2 w-2 rounded-full {{ old('status', $user->status) == $value ? 'bg-brand-500' : 'bg-gray-300' }}"></div>
                                            <span class="text-xs font-bold uppercase tracking-widest {{ old('status', $user->status) == $value ? 'text-brand-700 dark:text-brand-400' : 'text-gray-500' }}">{{ $label }}</span>
                                        </div>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Access Controls --}}
                <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/[0.03]">
                    <div class="mb-5 border-b border-gray-100 dark:border-gray-800 pb-4">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white">Access Privileges</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Assign system roles to this user.</p>
                    </div>

                    @if($roles->isEmpty())
                        <div class="py-8 text-center bg-gray-50 dark:bg-white/5 rounded-xl border border-dashed border-gray-200 dark:border-gray-800">
                            <p class="text-sm text-gray-500">No roles defined in system.</p>
                        </div>
                    @else
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            @foreach($roles as $role)
                                <label class="flex items-center gap-3 p-4 rounded-xl border {{ (collect(old('roles'))->contains($role->id) || in_array($role->id, $userRoles)) ? 'border-brand-500 bg-brand-50/20 dark:bg-brand-500/10' : 'border-gray-100 bg-gray-50/30 dark:border-gray-800 dark:bg-gray-900' }} transition-all cursor-pointer group">
                                    <input type="checkbox" name="roles[]" value="{{ $role->id }}"
                                        {{ (collect(old('roles'))->contains($role->id) || in_array($role->id, $userRoles)) ? 'checked' : '' }}
                                        class="h-5 w-5 rounded border-gray-300 text-brand-500 focus:ring-brand-500">
                                    <div class="flex flex-col">
                                        <span class="text-sm font-bold text-gray-900 dark:text-white">{{ $role->name }}</span>
                                        <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">{{ $role->permissions->count() }} hooks</span>
                                    </div>
                                </label>
                            @endforeach
                        </div>
                    @endif
                </div>

                {{-- Actions --}}
                <div class="flex justify-end gap-3 pt-4">
                    <button type="submit"
                        class="inline-flex items-center gap-2 rounded-lg bg-brand-500 px-8 py-3 text-sm font-bold text-white shadow-sm hover:bg-brand-600 transition-all active:scale-95">
                        <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                        </svg>
                        Update User Identity
                    </button>
                </div>
            </div>
        </form>
    </div>
@endsection
