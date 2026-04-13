@extends('layouts.app')

@section('content')
<div class="py-6">
    <!-- Header & Breadcrumbs -->
    <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <div class="flex items-center gap-3">
                <h1 class="text-xl font-bold text-gray-900 dark:text-white tracking-tight">
                    Expédition #{{ str_pad($shipment->id, 5, '0', STR_PAD_LEFT) }}
                </h1>
                
                @if($shipment->status === 'pending')
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-400 border border-amber-200 dark:border-amber-800/50">
                        <span class="w-2 h-2 mr-2 bg-amber-500 rounded-full animate-pulse"></span>
                        En attente
                    </span>
                @else
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-300 border border-gray-200 dark:border-gray-700">
                        {{ ucfirst($shipment->status) }}
                    </span>
                @endif
            </div>
            
            <nav class="mt-2">
                <ol class="flex items-center gap-2 text-sm text-gray-500 dark:text-gray-400">
                    <li><a class="font-medium hover:text-brand-500 transition-colors" href="{{ route('dashboard.index') }}">Dashboard /</a></li>
                    <li><a class="font-medium hover:text-brand-500 transition-colors" href="{{ route('transport-firm-bid.index') }}">Mes Expéditions /</a></li>
                    <li class="font-medium text-brand-500">Détails</li>
                </ol>
            </nav>
        </div>
        
        <!-- Action Buttons -->
        <div class="flex items-center space-x-3">
            <div x-data="{ openDeleteModal: false }" class="inline">
                <!-- Trigger Button -->
                <button @click="openDeleteModal = true" type="button" class="inline-flex items-center justify-center px-4 py-2 bg-red-50 hover:bg-red-100 border border-transparent rounded-xl text-sm font-semibold text-red-600 dark:bg-red-900/20 dark:text-red-400 shadow-sm transition-all focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2">
                    <svg class="mr-2 -ml-1 w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                    Supprimer
                </button>

                <!-- Delete Modal -->
                <x-ui.modal model="openDeleteModal" title="Supprimer la demande" maxWidth="max-w-md">
                    <div class="sm:flex sm:items-start">
                        <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 dark:bg-red-900/30 sm:mx-0 sm:h-10 sm:w-10">
                            <svg class="h-6 w-6 text-red-600 dark:text-red-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                            <p class="text-sm text-gray-500 dark:text-gray-400 break-words">
                                Êtes-vous sûr de vouloir supprimer cette demande d'expédition ? Toutes les données associées (marchandises) seront définitivement effacées. Cette action est irréversible.
                            </p>
                        </div>
                    </div>

                    <x-slot:footer>
                        <button @click="openDeleteModal = false" type="button" class="mt-3 w-full inline-flex justify-center rounded-xl border border-gray-300 dark:border-gray-600 shadow-sm px-4 py-2 bg-white dark:bg-gray-800 text-base font-semibold text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 sm:mt-0 sm:w-auto sm:text-sm transition-colors">
                            Annuler
                        </button>
                        <form action="{{ route('transport-firm-bid.destroy', $shipment) }}" method="POST" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="w-full inline-flex justify-center rounded-xl border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-semibold text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:w-auto sm:text-sm transition-colors">
                                Supprimer définitivement
                            </button>
                        </form>
                    </x-slot:footer>
                </x-ui.modal>
            </div>

            <a href="{{ route('transport-firm-bid.edit', $shipment) }}" 
                class="inline-flex items-center justify-center px-4 py-2 bg-brand-50 hover:bg-brand-100 border border-transparent rounded-xl text-sm font-semibold text-brand-600 dark:bg-brand-900/20 dark:text-brand-400 shadow-sm transition-all focus:outline-none focus:ring-2 focus:ring-brand-500 focus:ring-offset-2">
                <svg class="mr-2 -ml-1 w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                </svg>
                Éditer
            </a>
            
            <a href="{{ route('transport-firm-bid.index') }}" 
                class="inline-flex items-center justify-center px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-xl text-sm font-semibold text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 shadow-sm transition-all">
                <svg class="mr-2 -ml-1 w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Retour
            </a>
        </div>
    </div>

    <!-- Description Block -->
    @if($shipment->description)
    <div class="mb-8 p-4 bg-gray-50 dark:bg-gray-800/40 rounded-xl border border-gray-200/60 dark:border-gray-700/60">
        <h3 class="text-sm font-bold text-gray-900 dark:text-gray-100 mb-1">Description</h3>
        <p class="text-gray-600 dark:text-gray-400 text-sm whitespace-pre-wrap">{{ $shipment->description }}</p>
    </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
        
        <!-- Logistics Card (Spans 2 columns) -->
        <div class="lg:col-span-2 bg-white dark:bg-gray-800 rounded-2xl border border-gray-200/60 dark:border-gray-700 shadow-sm overflow-hidden flex flex-col">
            <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700 flex items-center bg-gray-50/50 dark:bg-gray-800/50">
                <svg class="w-5 h-5 text-brand-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7" /></svg>
                <h2 class="text-lg font-bold text-gray-900 dark:text-white">Logistique du Trajet</h2>
            </div>
            
            <div class="p-6 flex-1">
                <div class="relative">
                    <!-- Vertical Line Connecter -->
                    <div class="absolute left-6 top-8 bottom-8 w-0.5 bg-gray-200 dark:bg-gray-700 rounded"></div>
                    
                    <!-- Pickup -->
                    <div class="flex relative mb-8">
                        <div class="w-12 h-12 flex-shrink-0 bg-brand-50 text-brand-600 dark:bg-brand-900/30 dark:text-brand-400 rounded-xl flex items-center justify-center ring-4 ring-white dark:ring-gray-800 z-10">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18" /></svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400 mb-1">Enlèvement</p>
                            <h3 class="text-base font-bold text-gray-900 dark:text-white">{{ $shipment->pickup_address }}</h3>
                            <div class="flex items-center mt-2 text-base text-gray-600 dark:text-gray-400">
                                <span class="px-2 py-1 bg-gray-100 dark:bg-gray-700 rounded text-sm font-medium mr-3">Type: {{ ucfirst($shipment->pickup_type ?? 'Non spécifié') }}</span>
                                <svg class="w-4 h-4 mr-1 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                                Avant le {{ $shipment->latest_pickup_date ? \Carbon\Carbon::parse($shipment->latest_pickup_date)->format('d/m/Y') : 'Non spécifié' }}
                                @if(isset($shipment->requirements['latestPickupTime']))
                                    à {{ $shipment->requirements['latestPickupTime'] }}
                                @endif

                                @if($shipment->requirements['pickupNotify'] ?? false)
                                    <span class="ml-3 inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-amber-50 text-amber-600 border border-amber-100 uppercase tracking-tight">
                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" /></svg>
                                        Appeler avant pass.
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                    
                    <!-- Delivery -->
                    <div class="flex relative">
                        <div class="w-12 h-12 flex-shrink-0 bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-300 rounded-xl flex items-center justify-center ring-4 ring-white dark:ring-gray-800 z-10">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3" /></svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400 mb-1">Livraison</p>
                            <h3 class="text-base font-bold text-gray-900 dark:text-white">{{ $shipment->delivery_address }}</h3>
                            <div class="flex items-center mt-2 text-base text-gray-600 dark:text-gray-400">
                                <span class="px-2 py-1 bg-gray-100 dark:bg-gray-700 rounded text-sm font-medium mr-3">Type: {{ ucfirst($shipment->delivery_type ?? 'Non spécifié') }}</span>
                                <svg class="w-4 h-4 mr-1 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                                Avant le {{ $shipment->latest_delivery_date ? \Carbon\Carbon::parse($shipment->latest_delivery_date)->format('d/m/Y') : 'Non spécifié' }}
                                @if(isset($shipment->requirements['latestDeliveryTime']))
                                    à {{ $shipment->requirements['latestDeliveryTime'] }}
                                @endif

                                @if($shipment->requirements['deliveryNotify'] ?? false)
                                    <span class="ml-3 inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-blue-50 text-blue-600 border border-blue-100 uppercase tracking-tight">
                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" /></svg>
                                        Appeler avant pass.
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Totals Footer -->
            <div class="grid grid-cols-2 divide-x divide-gray-200 dark:divide-gray-700 bg-gray-50 dark:bg-gray-800/80 border-t border-gray-200 dark:border-gray-700">
                <div class="p-4 text-center">
                    <span class="block text-sm font-medium text-gray-500 dark:text-gray-400 uppercase">Poids Total</span>
                    <span class="block text-lg font-bold text-gray-900 dark:text-white">{{ number_format($shipment->total_weight, 2) }} kg</span>
                </div>
                <div class="p-4 text-center">
                    <span class="block text-sm font-medium text-gray-500 dark:text-gray-400 uppercase">Volume Total</span>
                    <span class="block text-lg font-bold text-gray-900 dark:text-white">{{ number_format($shipment->total_volume, 3) }} m³</span>
                </div>
            </div>
        </div>

        <!-- Requirements Card -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200/60 dark:border-gray-700 shadow-sm overflow-hidden flex flex-col">
            <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700 flex items-center bg-gray-50/50 dark:bg-gray-800/50">
                <svg class="w-5 h-5 text-indigo-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" /></svg>
                <h2 class="text-lg font-bold text-gray-900 dark:text-white">Exigences Spéciales</h2>
            </div>
            
            <div class="p-6 flex-1 overflow-y-auto">
                @php
                    $reqs = $shipment->requirements ?? [];
                    
                    // Simple helper to map key to visual styling
                    $specialFlags = [
                        ['key' => 'isUrgent', 'label' => 'Urgent', 'icon' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z', 'color' => 'red', 'descKey' => 'urgentDescription'],
                        ['key' => 'isDangerous', 'label' => 'Marchandise Dangereuse', 'icon' => 'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z', 'color' => 'orange', 'descKey' => 'dangerousGoodsDescription'],
                        ['key' => 'hasFragileGoods', 'label' => 'Fragile', 'icon' => 'M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z', 'color' => 'blue', 'descKey' => 'fragileGoodsDescription'],
                        ['key' => 'needsTemperatureControlledTransport', 'label' => 'Température Contrôlée', 'icon' => 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z', 'color' => 'cyan', 'descKey' => 'temperatureControlledDescription'],
                        ['key' => 'hasInsuranceOption', 'label' => 'Assurance', 'icon' => 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z', 'color' => 'green', 'descKey' => 'insuranceDescription'],
                        ['key' => 'hasSpecialHandlingInstructions', 'label' => 'Manutention Spéciale', 'icon' => 'M7 11.5V14m0-2.5v-6a1.5 1.5 0 113 0m-3 6a1.5 1.5 0 00-3 0v2a7.5 7.5 0 0015 0v-5a1.5 1.5 0 00-3 0m-6-3V11m0-5.5v-1a1.5 1.5 0 013 0v1m0 0V11m0-5.5a1.5 1.5 0 013 0v3m0 0V11', 'color' => 'purple', 'descKey' => 'specialHandlingDescription'],
                        ['key' => 'hasAdditionalRequirements', 'label' => 'Exigences Complémentaires', 'icon' => 'M4 6h16M4 10h16M4 14h16M4 18h16', 'color' => 'gray', 'descKey' => 'additionalRequirementsDescription'],
                    ];
                    
                    $hasAnyRequirements = false;
                @endphp

                <ul class="space-y-4">
                    @foreach($specialFlags as $flag)
                        @if(isset($reqs[$flag['key']]) && $reqs[$flag['key']])
                            @php $hasAnyRequirements = true; @endphp
                            <li class="flex items-start">
                                <div class="flex-shrink-0 w-8 h-8 rounded-full bg-{{ $flag['color'] }}-100 text-{{ $flag['color'] }}-600 dark:bg-{{ $flag['color'] }}-900/30 dark:text-{{ $flag['color'] }}-400 flex items-center justify-center mt-0.5">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $flag['icon'] }}" /></svg>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm font-bold text-gray-900 dark:text-white">{{ $flag['label'] }}</p>
                                    @if(isset($reqs[$flag['descKey']]) && $reqs[$flag['descKey']])
                                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">{{ $reqs[$flag['descKey']] }}</p>
                                    @endif
                                </div>
                            </li>
                        @endif
                    @endforeach
                </ul>

                @if(!$hasAnyRequirements)
                    <div class="h-full flex flex-col items-center justify-center text-center py-6 text-gray-400 dark:text-gray-500">
                        <svg class="w-12 h-12 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        <p class="text-sm">Aucune exigence ou restriction particulière pour cette expédition.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>


    <!-- Merchandise (Lots) Section -->
    <div class="mb-6">
        <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Marchandises ({{ $shipment->lots->count() }} lots)</h2>
        <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200/60 dark:border-gray-700 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead>
                        <tr class="bg-gray-50/50 dark:bg-gray-800/50">
                            <th scope="col" class="px-6 py-3 text-left text-sm font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Type
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-sm font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Quantité
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-sm font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Poids Unitaire
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-sm font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Dimensions / Détails
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700 bg-white dark:bg-gray-800">
                        @foreach($shipment->lots as $lot)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="h-8 w-8 rounded-md bg-gray-100 dark:bg-gray-700 flex items-center justify-center text-gray-500 mr-3">
                                            <!-- Simple icon based on type -->
                                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" /></svg>
                                        </div>
                                        <div class="text-sm font-semibold text-gray-900 dark:text-white uppercase">{{ $lot->type }}</div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-300 font-medium">
                                    {{ $lot->quantity }} x
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-300">
                                    @if($lot->weight)
                                        {{ number_format($lot->weight, 2) }} kg
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                                    @if($lot->type === 'palette' && $lot->palette_type)
                                        <div class="mb-1"><span class="font-medium text-gray-700 dark:text-gray-300">Type de palette:</span> {{ $lot->palette_type }}</div>
                                    @endif
                                    
                                    @if($lot->type === 'conteneur' && $lot->container_type)
                                        <div class="mb-1"><span class="font-medium text-gray-700 dark:text-gray-300">Type de conteneur:</span> {{ $lot->container_type }}</div>
                                    @endif

                                    @if($lot->type === 'vehicule')
                                        <div class="mb-1"><span class="font-medium text-gray-700 dark:text-gray-300">Véhicule:</span> {{ $lot->brand }} {{ $lot->model }}</div>
                                        <div class="mb-1"><span class="font-medium text-gray-700 dark:text-gray-300">Roulant:</span> {{ $lot->is_rolling ? 'Oui' : 'Non' }}</div>
                                    @endif

                                    @if($lot->length || $lot->width || $lot->height)
                                        <div>
                                            L: {{ $lot->length ?? 0 }}m × l: {{ $lot->width ?? 0 }}m × H: {{ $lot->height ?? 0 }}m
                                        </div>
                                    @endif
                                    
                                        <div class="text-xs mt-1 bg-gray-100 dark:bg-gray-700 inline-block px-2 py-0.5 rounded text-gray-600 dark:text-gray-300">
                                            Vol: {{ number_format($lot->volume, 3) }} m³
                                        </div>
                                    @endif

                                    @if($lot->is_stackable)
                                        <div class="text-[10px] mt-1 bg-blue-50 dark:bg-blue-900/20 inline-block px-2 py-0.5 rounded text-blue-600 dark:text-blue-400 font-bold border border-blue-100 dark:border-blue-800/50 uppercase">
                                            Gérable / Superposable
                                        </div>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
