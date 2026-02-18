@extends('layouts.fullscreen-layout')

@section('content')
@php($currentYear = date('Y'))
<div class="relative z-1 flex min-h-screen flex-col items-center justify-center overflow-hidden p-6">
    <x-common.common-grid-shape />

    <div class="mx-auto w-full max-w-[242px] text-center sm:max-w-[472px]">
        <h1 class="mb-8 text-title-md font-bold text-gray-800 dark:text-white/90 xl:text-title-2xl">
            ERROR
        </h1>

        <img src="{{ asset('images/error/500.svg') }}" alt="500" class="dark:hidden" />
        <img src="{{ asset('images/error/500-dark.svg') }}" alt="500" class="hidden dark:block" />

        <p class="mt-10 mb-6 text-base text-gray-700 dark:text-gray-400 sm:text-lg">
            Something went wrong on our side. Please try again later.
        </p>

        <div class="flex flex-col items-center justify-center gap-3 sm:flex-row">
            <a href="{{ url()->previous() }}"
                class="inline-flex items-center justify-center rounded-lg border border-gray-300 bg-white px-5 py-3.5 text-sm font-medium text-gray-700 shadow-theme-xs hover:bg-gray-50 hover:text-gray-800 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03] dark:hover:text-gray-200">
                Go Back
            </a>

            <a href="{{ route('landing') }}"
                class="inline-flex items-center justify-center rounded-lg bg-brand-500 px-5 py-3.5 text-sm font-medium text-white shadow-theme-xs hover:bg-brand-600">
                Back to Home
            </a>
        </div>
    </div>

    <p class="absolute bottom-6 left-1/2 -translate-x-1/2 text-center text-sm text-gray-500 dark:text-gray-400">
        &copy; {{ $currentYear }} - TailAdmin
    </p>
</div>
@endsection
