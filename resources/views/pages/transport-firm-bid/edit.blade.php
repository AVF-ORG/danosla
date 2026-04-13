@extends('layouts.app')

@section('content')
    <div>
        <!-- Header & Breadcrumbs -->
        <div class="mb-4">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h1 class="text-xl font-bold text-gray-900 dark:text-white">
                        Éditer la demande #{{ str_pad($shipment->id, 5, '0', STR_PAD_LEFT) }}
                    </h1>
                    <nav class="mt-1">
                        <ol class="flex items-center gap-2 text-sm text-gray-500 dark:text-gray-400">
                            <li>
                                <a class="font-medium hover:text-brand-500 transition-colors" href="{{ route('dashboard.index') }}">Dashboard /</a>
                            </li>
                            <li>
                                <a class="font-medium hover:text-brand-500 transition-colors" href="{{ route('transport-firm-bid.index') }}">Mes Expéditions /</a>
                            </li>
                            <li class="font-medium text-brand-500">Éditer</li>
                        </ol>
                    </nav>
                </div>
                
                <!-- Back Button -->
                <div class="flex items-center space-x-3">
                    <a href="{{ route('transport-firm-bid.show', $shipment) }}" 
                        class="inline-flex items-center justify-center px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brand-500 shadow-sm">
                        <svg class="mr-2 -ml-1 w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                        Retour aux détails
                    </a>
                </div>
            </div>
        </div>

        <div class="pb-4">
            <livewire:shipping-lot-form :shipmentRecord="$shipment" />
        </div>
    </div>
@endsection
