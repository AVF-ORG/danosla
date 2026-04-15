@extends('layouts.app')

@section('content')
    <div class="py-6">
        <!-- Header & Breadcrumbs -->
        <div class="mb-8">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h1 class="text-xl font-bold text-gray-900 dark:text-white tracking-tight">
                        Mes Expéditions
                    </h1>
                    <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                        Gérez vos demandes de transport et suivez leur statut en temps réel.
                    </p>
                </div>
                
                <!-- Action Button -->
                <div class="flex items-center">
                    <a href="{{ route('transport-firm-bid.create') }}" 
                       class="group inline-flex items-center justify-center px-6 py-2.5 bg-gradient-to-r from-brand-600 to-brand-500 hover:from-brand-500 hover:to-brand-400 text-white font-semibold rounded-xl shadow-md hover:shadow-lg transform hover:-translate-y-0.5 transition-all duration-200">
                        <svg class="w-5 h-5 mr-2 -ml-1 text-white/80 group-hover:text-white transition-colors" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
                        </svg>
                        Lancer une demande
                    </a>
                </div>
            </div>
        </div>

        <!-- Shipments Table Card -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200/60 dark:border-gray-700 shadow-sm overflow-hidden backdrop-blur-xl transition-all">
            @if ($shipments->isEmpty())
                <!-- Empty State -->
                <div class="flex flex-col items-center justify-center py-16 px-4 text-center">
                    <div class="w-20 h-20 mb-6 bg-brand-50 dark:bg-brand-900/20 text-brand-500 rounded-full flex items-center justify-center ring-8 ring-brand-50/50 dark:ring-brand-900/10">
                        <svg class="w-10 h-10" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">Aucune expédition en cours</h3>
                    <p class="text-gray-500 dark:text-gray-400 mb-8 max-w-sm mx-auto">Vous n'avez pas encore créé de demande de transport. Commencez dès maintenant en lançant une nouvelle demande.</p>
                    <a href="{{ route('transport-firm-bid.create') }}" class="text-brand-600 dark:text-brand-400 font-semibold hover:text-brand-500 hover:underline inline-flex items-center transition-colors">
                        Créer ma première demande
                        <svg class="ml-1.5 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                    </a>
                </div>
            @else
                <!-- Data Table -->
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead>
                            <tr class="bg-gray-50/50 dark:bg-gray-800/50">
                                <th scope="col" class="px-6 py-4 text-left text-sm font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    Récapitulatif
                                </th>
                                <th scope="col" class="px-6 py-4 text-left text-sm font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    Trajet
                                </th>
                                <th scope="col" class="px-6 py-4 text-left text-sm font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    Volume / Poids
                                </th>
                                <th scope="col" class="px-6 py-4 text-left text-sm font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    Date limite
                                </th>
                                <th scope="col" class="px-6 py-4 text-right text-sm font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    Statut
                                </th>
                                <th scope="col" class="px-6 py-4 text-right text-sm font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    Action
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700 bg-white dark:bg-gray-800">
                            @foreach ($shipments as $shipment)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors group cursor-pointer" onclick="window.location='{{ route('transport-firm-bid.show', $shipment) }}'">
                                    <!-- Recap -->
                                    <td class="px-6 py-5 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-10 w-10 bg-gray-100 dark:bg-gray-700 rounded-lg flex items-center justify-center text-gray-500 dark:text-gray-400 group-hover:bg-brand-50 group-hover:text-brand-500 transition-colors">
                                                <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                                </svg>
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-bold text-gray-900 dark:text-white group-hover:text-brand-600 dark:group-hover:text-brand-400 transition-colors">
                                                    Demande #{{ str_pad($shipment->id, 5, '0', STR_PAD_LEFT) }}
                                                </div>
                                                <div class="text-sm text-gray-500 dark:text-gray-400 mt-1 truncate max-w-[200px]" title="{{ $shipment->description }}">
                                                    {{ Str::limit($shipment->description, 30) }}
                                                </div>
                                            </div>
                                        </div>
                                    </td>

                                    <!-- Route -->
                                    <td class="px-6 py-5">
                                        <div class="flex flex-col text-sm">
                                            <div class="flex items-center text-gray-900 dark:text-white font-medium">
                                                <div class="w-2 h-2 rounded-full border-2 border-brand-500 mr-2 flex-shrink-0"></div>
                                                <span class="truncate max-w-[150px]" title="{{ $shipment->pickup_address }}">{{ Str::limit($shipment->pickup_address, 20) }}</span>
                                            </div>
                                            <div class="ml-1 w-0.5 h-3 bg-gray-200 dark:bg-gray-600 my-0.5"></div>
                                            <div class="flex items-center text-gray-500 dark:text-gray-400 font-medium">
                                                <div class="w-2 h-2 rounded-full border-2 border-gray-400 dark:border-gray-500 mr-2 flex-shrink-0"></div>
                                                <span class="truncate max-w-[150px]" title="{{ $shipment->delivery_address }}">{{ Str::limit($shipment->delivery_address, 20) }}</span>
                                            </div>
                                        </div>
                                    </td>

                                    <!-- Volume / Weight -->
                                    <td class="px-6 py-5 whitespace-nowrap">
                                        <div class="text-sm text-gray-900 dark:text-white font-semibold flex items-center">
                                            <svg class="w-4 h-4 mr-1 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3" /></svg>
                                            {{ number_format($shipment->total_weight, 2) }} kg
                                        </div>
                                        <div class="text-sm text-gray-500 dark:text-gray-400 mt-1 pl-5">
                                            {{ number_format($shipment->total_volume, 3) }} m³
                                        </div>
                                    </td>

                                    <!-- Dates -->
                                    <td class="px-6 py-5 whitespace-nowrap">
                                        <div class="text-sm text-gray-900 dark:text-white">
                                            {{ $shipment->latest_pickup_date ? \Carbon\Carbon::parse($shipment->latest_pickup_date)->format('d M Y') : '-' }}
                                        </div>
                                        <div class="text-sm text-brand-600 dark:text-brand-400 font-medium mt-1 flex items-center">
                                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                            Livraison: {{ $shipment->latest_delivery_date ? \Carbon\Carbon::parse($shipment->latest_delivery_date)->format('d M') : '-' }}
                                        </div>
                                    </td>

                                    <!-- Status -->
                                    <td class="px-6 py-5 whitespace-nowrap text-right">
                                        @if($shipment->status === 'pending')
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-sm font-medium bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-400 border border-amber-200 dark:border-amber-800/50 shadow-sm">
                                                <span class="w-1.5 h-1.5 mr-1.5 bg-amber-500 rounded-full animate-pulse"></span>
                                                En attente
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-sm font-medium bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-300 border border-gray-200 dark:border-gray-700 shadow-sm">
                                                {{ ucfirst($shipment->status) }}
                                            </span>
                                        @endif
                                    </td>

                                    <td class="px-6 py-5 whitespace-nowrap text-right">
                                        <div x-data="{ dropdownOpen: false, openDeleteModal: false }" class="relative inline-block text-left" @click.away="dropdownOpen = false">
                                            <!-- Dropdown Trigger -->
                                            <button @click.stop.prevent="dropdownOpen = !dropdownOpen" type="button" class="inline-flex p-2 items-center justify-center text-gray-400 hover:text-brand-600 hover:bg-brand-50 dark:hover:bg-gray-700 rounded-lg transition-colors focus:outline-none focus:ring-2 focus:ring-brand-500" title="Actions">
                                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                                    <path d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z" />
                                                </svg>
                                            </button>

                                            <!-- Dropdown Menu -->
                                            <div x-show="dropdownOpen" 
                                                 x-transition:enter="transition ease-out duration-100"
                                                 x-transition:enter-start="transform opacity-0 scale-95"
                                                 x-transition:enter-end="transform opacity-100 scale-100"
                                                 x-transition:leave="transition ease-in duration-75"
                                                 x-transition:leave-start="transform opacity-100 scale-100"
                                                 x-transition:leave-end="transform opacity-0 scale-95"
                                                 class="absolute right-0 z-50 mt-2 w-40 rounded-xl bg-white dark:bg-gray-800 shadow-xl border border-gray-100 dark:border-gray-700 focus:outline-none"
                                                 style="display: none;">
                                                <div class="py-1">
                                                    <a href="{{ route('transport-firm-bid.show', $shipment) }}" class="flex items-center px-4 py-2.5 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors" onclick="event.stopPropagation();">
                                                        Voir plus
                                                    </a>
                                                    <a href="{{ route('transport-firm-bid.edit', $shipment) }}" class="flex items-center px-4 py-2.5 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors" onclick="event.stopPropagation();">
                                                        Éditer
                                                    </a>
                                                    <button @click.stop.prevent="dropdownOpen = false; openDeleteModal = true" type="button" class="flex w-full items-center px-4 py-2.5 text-sm text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors">
                                                        Supprimer
                                                    </button>
                                                </div>
                                            </div>

                                            <!-- Delete Modal for List Item -->
                                            <template x-teleport="body">
                                                <div @click.stop>
                                                    <x-ui.modal model="openDeleteModal" title="Supprimer la demande" maxWidth="max-w-md">
                                                        <div class="sm:flex sm:items-start whitespace-normal">
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
                                                            <button @click.stop.prevent="openDeleteModal = false" type="button" class="w-full inline-flex justify-center rounded-xl border border-gray-300 dark:border-gray-600 shadow-sm px-4 py-2 bg-white dark:bg-gray-800 text-base font-semibold text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 sm:w-auto sm:text-sm transition-colors">
                                                                Annuler
                                                            </button>
                                                            <form action="{{ route('transport-firm-bid.destroy', $shipment) }}" method="POST" class="inline">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="w-full inline-flex justify-center rounded-xl border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-semibold text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:w-auto sm:text-sm transition-colors" @click.stop>
                                                                    Supprimer définitivement
                                                                </button>
                                                            </form>
                                                        </x-slot:footer>
                                                    </x-ui.modal>
                                                </div>
                                            </template>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
@endsection
