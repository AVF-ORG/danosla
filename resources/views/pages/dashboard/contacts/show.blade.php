@extends('layouts.app')

@section('content')
    <div class="mb-6">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Message Details</h1>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Read and respond to the message.</p>
            </div>

            <div class="flex items-center gap-3">
                <a href="{{ route('dashboard.contacts.index') }}"
                    class="inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 Transition-all active:scale-95">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Back to List
                </a>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        {{-- Message Column --}}
        <div class="lg:col-span-2 space-y-6">
            {{-- Sender Info Card --}}
            <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-800 dark:bg-white/[0.03]">
                <div class="flex items-center justify-between mb-6">
                    <div class="flex items-center gap-4">
                        <div class="h-12 w-12 rounded-full bg-brand-500 flex items-center justify-center text-white font-bold text-lg">
                            {{ substr($contact->name, 0, 1) }}
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white">{{ $contact->name }}</h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400">{{ $contact->email }} • {{ $contact->phone ?? 'No phone' }}</p>
                        </div>
                    </div>
                    <div class="text-right text-xs text-gray-400">
                        {{ $contact->created_at->format('M d, Y') }} at {{ $contact->created_at->format('H:i') }}
                    </div>
                </div>

                <div class="space-y-4">
                    <div>
                        <span class="text-xs font-semibold uppercase tracking-wider text-gray-400">Subject</span>
                        <p class="mt-1 text-brand-500 font-semibold text-base">{{ $contact->subject->name ?? 'No Subject' }}</p>
                    </div>
                    
                    <div class="pt-4 border-t border-gray-100 dark:border-gray-800">
                        <span class="text-xs font-semibold uppercase tracking-wider text-gray-400 block mb-2">Message Content</span>
                        <div class="text-gray-700 dark:text-gray-300 leading-relaxed whitespace-pre-wrap bg-gray-50 dark:bg-white/5 p-4 rounded-xl">
                            {{ $contact->content }}
                        </div>
                    </div>
                </div>
            </div>

            {{-- Reply Section --}}
            <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-800 dark:bg-white/[0.03]">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-6">
                    {{ $contact->isReplied() ? 'Recorded Reply' : 'Send a Reply' }}
                </h3>

                @if($contact->isReplied())
                    <div class="space-y-4">
                        <div class="bg-success-50 dark:bg-success-500/5 border border-success-100 dark:border-success-500/20 p-4 rounded-xl relative">
                            <span class="absolute top-2 right-4 text-[10px] text-success-600 dark:text-success-400">
                                Replied on {{ $contact->replied_at->format('M d, Y at H:i') }} by {{ $contact->repliedBy->name ?? 'Admin' }}
                            </span>
                            <div class="text-gray-800 dark:text-white/90 whitespace-pre-wrap pt-4">
                                {{ $contact->reply_content }}
                            </div>
                        </div>
                        
                        <div x-data="{ editing: false }">
                            <button @click="editing = !editing" class="text-sm text-brand-500 hover:underline">
                                Need to update the reply? Click here.
                            </button>
                            
                            <form x-show="editing" action="{{ route('dashboard.contacts.reply', $contact->id) }}" method="POST" class="mt-4 space-y-4">
                                @csrf
                                <textarea name="reply_content" rows="4" 
                                    class="w-full rounded-xl border border-gray-300 bg-transparent p-4 text-sm focus:border-brand-500 focus:ring-brand-500 dark:border-gray-700 dark:text-white"
                                    placeholder="Type your reply session record here...">{{ $contact->reply_content }}</textarea>
                                <div class="flex justify-end gap-2">
                                    <button type="button" @click="editing = false" class="px-4 py-2 text-sm text-gray-500">Cancel</button>
                                    <button type="submit" class="rounded-lg bg-brand-500 px-4 py-2 text-sm font-semibold text-white hover:bg-brand-600">Update Reply</button>
                                </div>
                            </form>
                        </div>
                    </div>
                @else
                    <form action="{{ route('dashboard.contacts.reply', $contact->id) }}" method="POST" class="space-y-4">
                        @csrf
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Your Reply Record</label>
                            <textarea name="reply_content" rows="6" 
                                class="w-full rounded-xl border border-gray-300 bg-transparent p-4 text-sm focus:border-brand-500 focus:ring-brand-500 dark:border-gray-700 dark:text-white"
                                placeholder="Write down what you replied to the user..."></textarea>
                        </div>
                        <div class="flex justify-end">
                            <button type="submit"
                                class="inline-flex items-center gap-2 rounded-lg bg-brand-500 px-6 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-brand-600 Transition-all active:scale-95">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                                </svg>
                                Save Reply Record
                            </button>
                        </div>
                    </form>
                @endif
            </div>
        </div>

        {{-- Sidebar Info --}}
        <div class="lg:col-span-1 space-y-6">
            <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-800 dark:bg-white/[0.03]">
                <h3 class="text-base font-semibold text-gray-800 dark:text-white/90 mb-4">Quick Stats</h3>
                <div class="space-y-4">
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-500">Status</span>
                        @if ($contact->isReplied())
                            <span class="bg-success-50 text-success-700 text-xs px-2 py-1 rounded-full">Replied</span>
                        @else
                            <span class="bg-brand-50 text-brand-700 text-xs px-2 py-1 rounded-full">Pending</span>
                        @endif
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-500">Subject</span>
                        <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $contact->subject->name ?? 'N/A' }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-500">Date Received</span>
                        <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $contact->created_at->format('M d, Y') }}</span>
                    </div>
                </div>
            </div>

            <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-800 dark:bg-white/[0.03]">
                <h3 class="text-base font-semibold text-gray-800 dark:text-white/90 mb-4">Actions</h3>
                <div class="space-y-3">
                    <button type="button" 
                        @click="window.location.href='mailto:{{ $contact->email }}'"
                        class="w-full flex items-center justify-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 transition-all">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                        Email Externaly
                    </button>
                    
                    <form action="{{ route('dashboard.contacts.destroy', $contact->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this message?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="w-full flex items-center justify-center gap-2 rounded-lg bg-error-50 px-4 py-2.5 text-sm font-semibold text-error-600 hover:bg-error-100 dark:bg-error-500/10 dark:text-error-400 Transition-all">
                            Delete Message
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
