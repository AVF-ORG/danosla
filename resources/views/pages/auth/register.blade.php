@extends('layouts.fullscreen-layout')

@php
    $locale = LaravelLocalization::getCurrentLocale();
@endphp


@section('content')
    <div class="relative min-h-screen w-full lg:grid lg:grid-cols-12 flex flex-col overflow-x-hidden font-outfit bg-white dark:bg-gray-900">
        
        {{-- Left Side: Form Section (7/12 on lg+) --}}
        <div class="relative z-10 flex w-full flex-1 flex-col lg:col-span-7 bg-white dark:bg-gray-900 lg:shadow-2xl lg:overflow-y-auto custom-scrollbar">

            {{-- Top Branding & Actions (Hidden on lg+) --}}
            <div class="flex items-center justify-between p-6 lg:hidden">
                <a href="/" class="flex items-center gap-2 group">
                    <div class="size-9 bg-brand-500 rounded-lg flex items-center justify-center text-white shadow-lg shadow-brand-500/20">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                        </svg>
                    </div>
                    <span class="text-lg font-bold tracking-tight text-gray-900 dark:text-white">Danosla</span>
                </a>
                @include('layouts.header.language-dropdown')
            </div>

            {{-- Desktop Only Branding (Absolute) - Visible on lg+ --}}
            <div class="hidden lg:block absolute top-8 left-8 z-50">
                <a href="/" class="flex items-center gap-2 mb-8 group">
                    <div class="size-10 bg-brand-500 rounded-xl flex items-center justify-center text-white shadow-lg shadow-brand-500/20 group-hover:scale-110 transition-transform">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                        </svg>
                    </div>
                    <span class="text-xl font-bold tracking-tight text-gray-900 dark:text-white">Danosla</span>
                </a>
            </div>

            <div class="hidden lg:block absolute top-8 right-8 z-50">
                @include('layouts.header.language-dropdown')
            </div>

            {{-- Main Form Container --}}
            <div class="flex-1 flex flex-col justify-center px-6 sm:px-12 md:px-16 lg:px-12 xl:px-24 py-8 lg:py-24">
                <div class="w-full max-w-2xl mx-auto">
                    {{-- The Multi-step Form --}}
                    <div class="relative">
                        <livewire:auth.register />
                    </div>
                </div>
            </div>
        </div>

        {{-- Right Side: Cinematic Branding Panel (5/12) --}}
        <div class="hidden lg:flex lg:col-span-5 relative overflow-hidden bg-gray-900">
            {{-- Hero Image --}}
            <div class="absolute inset-0 z-0">
                <img src="{{ asset('images/auth/logistics-hero.png') }}" alt="Logistics Background" class="h-full w-full object-cover">
                <div class="absolute inset-0 bg-gradient-to-tr from-brand-900/90 via-brand-900/40 to-transparent"></div>
                
                {{-- Decorative Mesh Gradient Blur --}}
                <div class="absolute -top-[10%] -right-[10%] w-[50%] h-[50%] bg-brand-500/20 blur-[120px] rounded-full"></div>
                <div class="absolute -bottom-[10%] -left-[10%] w-[40%] h-[40%] bg-blue-500/20 blur-[100px] rounded-full"></div>
            </div>

            {{-- Content Overlay --}}
            <div class="relative z-10 w-full flex flex-col justify-end p-12 xl:p-16">
                <div class="max-w-xl animate-in fade-in slide-in-from-bottom-8 duration-1000">
                    <div class="bg-white/10 backdrop-blur-md border border-white/20 rounded-2xl p-8 shadow-2xl">
                        <div class="flex items-center gap-4 mb-6">
                            <div class="flex -space-x-3">
                                <div class="size-10 rounded-full border-2 border-brand-900 bg-gray-200">
                                    <img src="https://ui-avatars.com/api/?name=A&background=random" class="rounded-full">
                                </div>
                                <div class="size-10 rounded-full border-2 border-brand-900 bg-gray-200">
                                    <img src="https://ui-avatars.com/api/?name=B&background=random" class="rounded-full">
                                </div>
                                <div class="size-10 rounded-full border-2 border-brand-900 bg-gray-200">
                                    <img src="https://ui-avatars.com/api/?name=C&background=random" class="rounded-full">
                                </div>
                            </div>
                            <div class="text-white/80 text-sm">
                                <span class="block font-bold text-white">10k+ Professionals</span>
                                already joined our network
                            </div>
                        </div>

                        <h2 class="text-3xl xl:text-4xl font-bold text-white mb-6 leading-tight">
                            The most reliable way to ship and grow.
                        </h2>
                        
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-6 border-t border-white/10">
                            <div class="flex items-center gap-3">
                                <div class="size-8 rounded-lg bg-success-500/20 flex items-center justify-center text-success-400">
                                    <svg class="size-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                </div>
                                <span class="text-white/80 text-sm font-medium">Real-time Tracking</span>
                            </div>
                            <div class="flex items-center gap-3">
                                <div class="size-8 rounded-lg bg-success-500/20 flex items-center justify-center text-success-400">
                                    <svg class="size-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                </div>
                                <span class="text-white/80 text-sm font-medium">Verified Carriers</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Theme Toggler - Fixed within branding panel on desktop --}}
            <div class="absolute right-8 bottom-8 z-50">
                <button
                    class="bg-white/10 hover:bg-white/20 backdrop-blur-md border border-white/20 size-14 flex items-center justify-center rounded-2xl text-white shadow-lg transition-all active:scale-95 group overflow-hidden"
                    @click.prevent="$store.theme.toggle()">
                    <div class="relative w-full h-full flex items-center justify-center">
                        <svg class="absolute transition-all duration-500 dark:translate-y-0 translate-y-20 opacity-0 dark:opacity-100" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364-6.364l-.707.707M6.343 17.657l-.707.707m12.728 0l-.707-.707M6.343 6.343l-.707-.707M12 5a7 7 0 000 14 7 7 0 000-14z" />
                        </svg>
                        <svg class="absolute transition-all duration-500 dark:translate-y-20 translate-y-0 opacity-100 dark:opacity-0" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M21 12.79A9 9 0 1111.21 3 7 7 0 0021 12.79z" />
                        </svg>
                    </div>
                </button>
            </div>
        </div>
    </div>
@endsection
