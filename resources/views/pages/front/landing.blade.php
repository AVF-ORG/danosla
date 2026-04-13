@extends('layouts.fullscreen-layout')

@section('content')
<div class="min-h-screen bg-white dark:bg-gray-900 flex flex-col">

    <!-- Header -->
    <header class="flex items-center justify-between px-6 py-4 border-b border-gray-200 dark:border-gray-800">
        <a href="/" class="text-xl font-bold text-gray-800 dark:text-white">
            {{ config('app.name') }}
        </a>

        <nav class="flex items-center gap-4">
            @auth
                <a href="/dashboard"
                   class="text-sm font-medium text-gray-700 dark:text-gray-300 hover:text-brand-500">
                    Dashboard
                </a>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button
                        type="submit"
                        class="text-sm font-medium text-red-600 hover:text-red-700"
                    >
                        Logout
                    </button>
                </form>
            @else
                <a href="{{ route('login') }}"
                   class="text-sm font-medium text-gray-700 dark:text-gray-300 hover:text-brand-500">
                    Login
                </a>

                <a href="{{ route('register') }}"
                   class="rounded-lg bg-brand-500 px-4 py-2 text-sm font-medium text-white hover:bg-brand-600">
                    Get Started
                </a>
            @endauth
        </nav>
    </header>

    <!-- Hero -->
    <main class="flex-1">
        <!-- Hero Section -->
        <section class="flex min-h-[70vh] items-center justify-center px-6 py-20">
            <div class="max-w-4xl text-center">
                <div class="mb-6 inline-flex items-center rounded-full bg-brand-50 px-4 py-1.5 text-sm font-medium text-brand-600 dark:bg-brand-500/10 dark:text-brand-400">
                    <span class="mr-2 flex h-2 w-2 rounded-full bg-brand-500"></span>
                    Now with AI-Powered Logistics
                </div>
                <h1 class="mb-6 text-5xl font-extrabold tracking-tight text-gray-900 dark:text-white lg:text-6xl">
                    Streamline your <span class="bg-gradient-to-r from-brand-500 to-indigo-600 bg-clip-text text-transparent">Transport Operations</span>
                </h1>

                <p class="mx-auto mb-10 max-w-2xl text-lg text-gray-600 dark:text-gray-400 md:text-xl">
                    The ultimate platform for transport firms to manage bids, lots, and logistics with a modern, high-performance interface.
                </p>

                <div class="flex flex-col justify-center gap-4 sm:flex-row">
                    @auth
                        <a href="/dashboard"
                           class="inline-flex items-center justify-center rounded-xl bg-brand-500 px-8 py-4 text-base font-semibold text-white shadow-xl shadow-brand-500/30 transition-all hover:bg-brand-600 hover:shadow-brand-500/40 active:scale-[0.98]">
                            Go to Dashboard
                            <svg class="ml-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                            </svg>
                        </a>
                    @else
                        <a href="{{ route('register') }}"
                           class="inline-flex items-center justify-center rounded-xl bg-brand-500 px-8 py-4 text-base font-semibold text-white shadow-xl shadow-brand-500/30 transition-all hover:bg-brand-600 hover:shadow-brand-500/40 active:scale-[0.98]">
                            Start Free Trial
                        </a>

                        <a href="{{ route('login') }}"
                           class="inline-flex items-center justify-center rounded-xl border border-gray-200 bg-white px-8 py-4 text-base font-semibold text-gray-700 transition-all hover:bg-gray-50 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300 dark:hover:bg-white/5">
                            Login to Account
                        </a>
                    @endauth
                </div>
            </div>
        </section>

        <!-- Stats Section -->
        <section class="border-y border-gray-100 bg-gray-50/50 py-12 dark:border-gray-800 dark:bg-white/[0.02]">
            <div class="mx-auto max-w-7xl px-6">
                <div class="grid grid-cols-2 gap-8 md:grid-cols-4">
                    <div class="text-center">
                        <div class="text-3xl font-bold text-gray-900 dark:text-white">10k+</div>
                        <div class="mt-1 text-sm text-gray-500">Active Shipments</div>
                    </div>
                    <div class="text-center">
                        <div class="text-3xl font-bold text-gray-900 dark:text-white">99.9%</div>
                        <div class="mt-2 text-sm text-gray-500">Reliability Rate</div>
                    </div>
                    <div class="text-center">
                        <div class="text-3xl font-bold text-gray-900 dark:text-white">500+</div>
                        <div class="mt-2 text-sm text-gray-500">Global Partners</div>
                    </div>
                    <div class="text-center">
                        <div class="text-3xl font-bold text-gray-900 dark:text-white">24/7</div>
                        <div class="mt-2 text-sm text-gray-500">Expert Support</div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Features Section -->
        <section id="features" class="py-24">
            <div class="mx-auto max-w-7xl px-6">
                <div class="mb-16 text-center">
                    <h2 class="mb-4 text-3xl font-bold text-gray-900 dark:text-white md:text-4xl">Everything you need to scale</h2>
                    <p class="mx-auto max-w-2xl text-gray-600 dark:text-gray-400">
                        Powerful features designed to help you manage your transport business more efficiently than ever.
                    </p>
                </div>

                <div class="grid gap-8 md:grid-cols-3">
                    <!-- Feature 1 -->
                    <div class="group rounded-3xl border border-gray-100 bg-white p-8 transition-all hover:border-brand-500/20 hover:shadow-2xl hover:shadow-brand-500/5 dark:border-gray-800 dark:bg-gray-900">
                        <div class="mb-6 flex h-12 w-12 items-center justify-center rounded-2xl bg-brand-50 text-brand-600 dark:bg-brand-500/10 dark:text-brand-400">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                            </svg>
                        </div>
                        <h3 class="mb-3 text-xl font-bold text-gray-900 dark:text-white">Real-time Bidding</h3>
                        <p class="text-gray-600 dark:text-gray-400 leading-relaxed">
                            Monitor and manage transport bids in real-time with our high-performance dashboard system.
                        </p>
                    </div>

                    <!-- Feature 2 -->
                    <div class="group rounded-3xl border border-gray-100 bg-white p-8 transition-all hover:border-indigo-500/20 hover:shadow-2xl hover:shadow-indigo-500/5 dark:border-gray-800 dark:bg-gray-900">
                        <div class="mb-6 flex h-12 w-12 items-center justify-center rounded-2xl bg-indigo-50 text-indigo-600 dark:bg-indigo-500/10 dark:text-indigo-400">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9" />
                            </svg>
                        </div>
                        <h3 class="mb-3 text-xl font-bold text-gray-900 dark:text-white">Global Reach</h3>
                        <p class="text-gray-600 dark:text-gray-400 leading-relaxed">
                            Manage shipments across multiple regions and countries with full localization support.
                        </p>
                    </div>

                    <!-- Feature 3 -->
                    <div class="group rounded-3xl border border-gray-100 bg-white p-8 transition-all hover:border-brand-500/20 hover:shadow-2xl hover:shadow-brand-500/5 dark:border-gray-800 dark:bg-gray-900">
                        <div class="mb-6 flex h-12 w-12 items-center justify-center rounded-2xl bg-brand-50 text-brand-600 dark:bg-brand-500/10 dark:text-brand-400">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                            </svg>
                        </div>
                        <h3 class="mb-3 text-xl font-bold text-gray-900 dark:text-white">Enterprise Security</h3>
                        <p class="text-gray-600 dark:text-gray-400 leading-relaxed">
                            Role-based access control and encrypted data ensure your business information remains private.
                        </p>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <!-- Footer -->
    <footer class="border-t border-gray-100 bg-white py-12 dark:border-gray-800 dark:bg-gray-950">
        <div class="mx-auto max-w-7xl px-6">
            <div class="grid gap-12 md:grid-cols-4">
                <div class="col-span-1 md:col-span-2">
                    <a href="/" class="mb-4 inline-block text-2xl font-bold text-gray-900 dark:text-white">
                        {{ config('app.name') }}
                    </a>
                    <p class="max-w-xs text-gray-500 dark:text-gray-400">
                        The next generation platform for transport firms and logistics management. Built for speed, security, and scale.
                    </p>
                </div>
                <div>
                    <h4 class="mb-4 text-sm font-bold uppercase tracking-wider text-gray-900 dark:text-white">Product</h4>
                    <ul class="space-y-2 text-sm text-gray-500 dark:text-gray-400">
                        <li><a href="#" class="hover:text-brand-500">Features</a></li>
                        <li><a href="#" class="hover:text-brand-500">Solutions</a></li>
                        <li><a href="#" class="hover:text-brand-500">Pricing</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="mb-4 text-sm font-bold uppercase tracking-wider text-gray-900 dark:text-white">Company</h4>
                    <ul class="space-y-2 text-sm text-gray-500 dark:text-gray-400">
                        <li><a href="#" class="hover:text-brand-500">About Us</a></li>
                        <li><a href="#" class="hover:text-brand-500">Contact</a></li>
                        <li><a href="#" class="hover:text-brand-500">Privacy Policy</a></li>
                    </ul>
                </div>
            </div>
            <div class="mt-12 border-t border-gray-100 pt-8 text-center text-sm text-gray-500 dark:border-gray-800 dark:text-gray-400">
                © {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
            </div>
        </div>
    </footer>

</div>
@endsection
