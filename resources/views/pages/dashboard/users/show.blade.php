@extends('layouts.app')

@section('content')
    <div class="mx-auto max-w-4xl">
        {{-- Header --}}
        <div class="mb-6">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
                        User Profile
                    </h1>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                        Detailed information for system user <span class="font-bold text-gray-900 dark:text-white">{{ $user->name }}</span>.
                    </p>
                </div>

                <div class="flex items-center gap-3">
                    <a href="{{ route('dashboard.users.index') }}"
                        class="inline-flex items-center gap-2 rounded-lg border border-gray-200 bg-white px-4 py-2.5 text-sm font-semibold text-gray-600 shadow-sm hover:bg-gray-50 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-400 dark:hover:bg-gray-800/50 transition-all">
                        Back to List
                    </a>
                    <a href="{{ route('dashboard.users.edit', $user->id) }}"
                        class="inline-flex items-center gap-2 rounded-lg bg-brand-500 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-brand-600 transition-all active:scale-95">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                        Edit Profile
                    </a>
                </div>
            </div>
        </div>

        <div class="space-y-6">
            {{-- Personal Identity --}}
            <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/[0.03]">
                <div class="mb-5 border-b border-gray-100 dark:border-gray-800 pb-4">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">General Information</h3>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-8 gap-y-6">
                    <div>
                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">Full Identity</p>
                        <p class="text-sm font-bold text-gray-800 dark:text-white">{{ $user->name }}</p>
                    </div>
                    <div>
                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">Email Connection</p>
                        <p class="text-sm font-bold text-gray-800 dark:text-white">{{ $user->email }}</p>
                    </div>
                    <div>
                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">Phone Number</p>
                        <p class="text-sm font-bold text-gray-800 dark:text-white">{{ $user->phone ?? 'Not provided' }}</p>
                    </div>
                    <div>
                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">Account Lifecycle</p>
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
                    </div>
                </div>
            </div>

            {{-- Corporate Details --}}
            <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/[0.03]">
                <div class="mb-5 border-b border-gray-100 dark:border-gray-800 pb-4">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">Organization Context</h3>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-8 gap-y-6">
                    <div>
                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">Company Name</p>
                        <p class="text-sm font-bold text-brand-600 dark:text-brand-400">{{ $user->company_name ?? 'Individual' }}</p>
                    </div>
                    <div>
                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">Registry Number</p>
                        <p class="text-sm font-bold text-gray-800 dark:text-white">{{ $user->company_number ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">Business Sector</p>
                        <p class="text-sm font-bold text-gray-800 dark:text-white">{{ $user->sector?->name ?? 'Unspecified' }}</p>
                    </div>
                    <div>
                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">Digital Presence</p>
                        @if($user->website)
                            <a href="{{ $user->website }}" target="_blank" class="text-sm font-bold text-brand-500 hover:text-brand-600 underline underline-offset-4 decoration-brand-500/30 transition-all font-mono">{{ parse_url($user->website, PHP_URL_HOST) }}</a>
                        @else
                            <p class="text-sm font-bold text-gray-400 italic">None</p>
                        @endif
                    </div>
                    <div class="sm:col-span-2">
                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">Physical Address</p>
                        <p class="text-sm font-bold text-gray-800 dark:text-white">{{ $user->address ?? 'Not Recorded' }}</p>
                    </div>
                </div>
            </div>

            {{-- Security --}}
            <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/[0.03]">
                <div class="mb-5 border-b border-gray-100 dark:border-gray-800 pb-4">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">System Access</h3>
                </div>

                <div class="space-y-6">
                    <div>
                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-3">Assigned Roles</p>
                        <div class="flex flex-wrap gap-2">
                            @forelse ($user->roles as $role)
                                <div class="flex items-center gap-2 rounded-lg bg-gray-50 dark:bg-white/5 border border-gray-100 dark:border-gray-800 px-3 py-2">
                                    <div class="h-2 w-2 rounded-full bg-brand-500"></div>
                                    <span class="text-xs font-bold text-gray-700 dark:text-gray-300">{{ $role->name }}</span>
                                </div>
                            @empty
                                <span class="text-xs font-medium text-gray-400 italic">No system privileges assigned.</span>
                            @endforelse
                        </div>
                    </div>

                    <div class="pt-4 border-t border-gray-50 dark:border-gray-800">
                        <form action="{{ route('dashboard.users.update-status', $user->id) }}" method="POST" class="flex flex-wrap items-end gap-4">
                            @csrf
                            @method('PATCH')
                            <div class="flex-1 min-w-[200px]">
                                <label class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2 block">Quick Lifecycle Override</label>
                                <select name="status" class="h-10 w-full rounded-lg border border-gray-300 bg-white px-3 text-xs font-bold text-gray-700 focus:border-brand-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300">
                                    <option value="active" {{ $user->status === 'active' ? 'selected' : '' }}>Set as Active</option>
                                    <option value="pending" {{ $user->status === 'pending' ? 'selected' : '' }}>Set as Pending</option>
                                    <option value="inactive" {{ $user->status === 'inactive' ? 'selected' : '' }}>Set as Inactive</option>
                                </select>
                            </div>
                            <button type="submit" class="h-10 px-6 rounded-lg bg-gray-900 dark:bg-brand-500 text-[10px] font-black uppercase tracking-widest text-white hover:bg-black transition-all">
                                Update Lifecycle
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
