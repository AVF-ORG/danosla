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
    <main class="flex flex-1 items-center justify-center px-6">
        <div class="max-w-2xl text-center">
            <h1 class="mb-4 text-4xl font-bold text-gray-800 dark:text-white">
                Build faster with a clean Laravel architecture
            </h1>

            <p class="mb-8 text-gray-500 dark:text-gray-400">
                A modern authentication system with clean controllers, scalable routing,
                and production-ready UI.
            </p>

            <div class="flex justify-center gap-4">
                @auth
                    <a href="/dashboard"
                       class="rounded-lg bg-brand-500 px-6 py-3 text-sm font-medium text-white hover:bg-brand-600">
                        Go to Dashboard
                    </a>
                @else
                    <a href="{{ route('register') }}"
                       class="rounded-lg bg-brand-500 px-6 py-3 text-sm font-medium text-white hover:bg-brand-600">
                        Create Account
                    </a>

                    <a href="{{ route('login') }}"
                       class="rounded-lg border border-gray-300 px-6 py-3 text-sm font-medium text-gray-700 hover:bg-gray-100 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-white/5">
                        Login
                    </a>
                @endauth
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="px-6 py-4 text-center text-sm text-gray-500 dark:text-gray-400">
        © {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
    </footer>

</div>
@endsection
