@extends('layouts.app')

@section('content')
    <div class="px-4 py-4 md:px-4 md:py-4">
        @php
            $statusConfig = match ($shipment->status) {
                'pending' => [
                    'label' => 'En Attente',
                    'color' => 'bg-amber-50 text-amber-700 border-amber-100',
                    'icon' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z',
                ],
                'active' => [
                    'label' => 'En Cours',
                    'color' => 'bg-blue-50 text-blue-700 border-blue-100',
                    'icon' => 'M13 10V3L4 14h7v7l9-11h-7z',
                ],
                'completed' => [
                    'label' => 'Livré',
                    'color' => 'bg-success-50 text-success-700 border-success-100',
                    'icon' => 'M5 13l4 4L19 7',
                ],
                'cancelled', 'canceled' => [
                    'label' => 'Annulé',
                    'color' => 'bg-red-50 text-red-700 border-red-100',
                    'icon' =>
                        'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z',
                ],
                default => [
                    'label' => ucfirst($shipment->status),
                    'color' => 'bg-gray-50 text-gray-700 border-gray-100',
                    'icon' => 'M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
                ],
            };

            $now = now();
            $validityDate = $shipment->validity_date;
            $diffInHours = $validityDate ? $now->diffInHours($validityDate, false) : 999;
            $isUrgentValidity = $diffInHours >= 0 && $diffInHours <= 3;
        @endphp
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <a href="{{ route('transport-firm-bid.index') }}"
                        class="text-gray-400 hover:text-gray-600 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                        </svg>
                    </a>
                    <div class="flex items-center gap-2">
                        <span class="text-xs font-medium text-gray-400">{{ $shipment->created_at->format('M d, Y') }}</span>
                        <span class="text-xs text-gray-300">•</span>
                        <span
                            class="text-xs font-medium text-gray-400">EXP-{{ str_pad($shipment->id, 6, '0', STR_PAD_LEFT) }}</span>
                    </div>
                </div>

                <!-- Management Toolbar (Actions, Status, Price) -->
                <div class="flex items-center gap-6">
                    <!-- Actions -->
                    <div class="flex items-center gap-2 border-r border-gray-100 dark:border-gray-800 pr-6 mr-3"
                        x-data="{ openDeleteModal: false }">
                        <a href="{{ route('transport-firm-bid.edit', $shipment) }}"
                            class="inline-flex items-center gap-2 px-4 py-2 bg-gray-900 dark:bg-white text-white dark:text-gray-900 rounded-xl text-xs font-black uppercase tracking-widest hover:bg-brand-600 hover:text-white transition-all shadow-sm">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                    d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                            </svg>
                            Edit
                        </a>
                        <button @click="openDeleteModal = true"
                            class="inline-flex items-center gap-2 px-4 py-2 border border-red-100 dark:border-red-900/30 text-red-600 dark:text-red-400 rounded-xl text-xs font-black uppercase tracking-widest hover:bg-red-50 transition-all">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                            Delete
                        </button>

                        <!-- Delete Modal -->
                        <x-ui.modal model="openDeleteModal" title="Delete Shipment" maxWidth="max-w-md">
                            <div class="p-8 text-center">
                                <div class="w-16 h-16 bg-red-50 rounded-full flex items-center justify-center mx-auto mb-4">
                                    <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                    </svg>
                                </div>
                                <h3 class="text-lg font-black text-gray-900 dark:text-white mb-2 uppercase">Permanently
                                    Delete?</h3>
                                <p class="text-sm text-gray-500 mb-6">This action cannot be undone. The lot associated with
                                    this shipment will be removed.</p>
                                <div class="flex gap-3">
                                    <button @click="openDeleteModal = false"
                                        class="flex-1 py-3 bg-gray-100 text-gray-700 rounded-xl font-black uppercase text-[10px] tracking-widest">Cancel</button>
                                    <form action="{{ route('transport-firm-bid.destroy', $shipment) }}" method="POST"
                                        class="flex-1">
                                        @csrf @method('DELETE')
                                        <button type="submit"
                                            class="w-full py-3 bg-red-600 text-white rounded-xl font-black uppercase text-[10px] tracking-widest shadow-lg shadow-red-500/30">Delete</button>
                                    </form>
                                </div>
                            </div>
                        </x-ui.modal>
                    </div>

                    <!-- Status & Price Badges -->
                    <div class="flex items-center gap-3">
                        <div
                            class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full border {{ $statusConfig['color'] }} shadow-sm">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                    d="{{ $statusConfig['icon'] }}" />
                            </svg>
                            <span
                                class="text-[10px] font-black uppercase tracking-widest">{{ $statusConfig['label'] }}</span>
                        </div>

                        @if ($shipment->delivery_price)
                            <div
                                class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full border border-gray-100 dark:border-gray-800 bg-gray-50/50 dark:bg-gray-800/50 shadow-sm">
                                <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span
                                    class="text-[10px] font-black uppercase tracking-widest text-gray-600 dark:text-gray-400">
                                    {{ number_format($shipment->delivery_price, 2, ',', ' ') }} €
                                </span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>


        <!-- Main Shipment Card (Merged Recap & Details) -->
        <div
            class="bg-white dark:bg-gray-900 rounded-3xl border border-gray-200 dark:border-gray-800 shadow-xl shadow-gray-200/40 dark:shadow-none overflow-hidden mb-8">

            <!-- Route Overview Section -->
            <div class="px-8 py-10">
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-8 relative">
                    <!-- Departure -->
                    <div class="flex-1">
                        <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Pickup</p>
                        <h2 class="text-4xl font-black text-gray-900 dark:text-white leading-none mb-1">
                            {{ $shipment->latest_pickup_time ? \Carbon\Carbon::parse($shipment->latest_pickup_time)->format('H:i') : '00:00' }}
                            <span class="text-xl font-bold text-gray-300 ml-1">am</span>
                        </h2>
                        <p class="text-xs font-bold text-gray-600 dark:text-gray-400">
                            {{ $shipment->latest_pickup_date ? $shipment->latest_pickup_date->translatedFormat('M d, Y') : '-' }}
                        </p>
                    </div>

                    <!-- Path Visual -->
                    <div class="flex flex-col items-center gap-2 px-4 flex-none group">
                        <div class="flex items-center gap-3">
                            <div
                                class="w-2.5 h-2.5 rounded-full border-2 border-brand-500 shadow-[0_0_8px_rgba(var(--brand-600-rgb),0.3)]">
                            </div>
                            <div class="flex gap-1 items-center">
                                @for ($i = 0; $i < 6; $i++)
                                    <div class="w-1 h-0.5 bg-gray-200 dark:bg-gray-700 rounded-full"></div>
                                @endfor
                                <div
                                    class="px-2 py-1 rounded-lg bg-gray-50 dark:bg-gray-800 border border-gray-100 dark:border-gray-700 group-hover:scale-110 transition-transform cursor-help">
                                    <svg class="w-4 h-4 text-brand-500 transform rotate-90" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3"
                                            d="M13 10V3L4 14h7v7l9-11h-7z" />
                                    </svg>
                                </div>
                                @for ($i = 0; $i < 6; $i++)
                                    <div class="w-1 h-0.5 bg-gray-200 dark:bg-gray-700 rounded-full"></div>
                                @endfor
                            </div>
                            <div class="w-2.5 h-2.5 rounded-full border-2 border-gray-300 dark:border-gray-600"></div>
                        </div>
                        <span class="text-[9px] font-black text-gray-400 uppercase tracking-[0.2em]">Est. distance</span>
                    </div>

                    <!-- Arrival -->
                    <div class="flex-1 text-right">
                        <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Delivery</p>
                        <h2 class="text-4xl font-black text-gray-900 dark:text-white leading-none mb-1">
                            {{ $shipment->latest_delivery_time ? \Carbon\Carbon::parse($shipment->latest_delivery_time)->format('H:i') : '00:00' }}
                            <span class="text-xl font-bold text-gray-300 ml-1">pm</span>
                        </h2>
                        <p class="text-xs font-bold text-gray-600 dark:text-gray-400">
                            {{ $shipment->latest_delivery_date ? $shipment->latest_delivery_date->translatedFormat('M d, Y') : '-' }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- Horizontal Separator -->
            <div class="h-px bg-gradient-to-r from-transparent via-gray-100 to-transparent dark:via-gray-800"></div>

            <!-- Detailed Locations Grid -->
            <div
                class="grid grid-cols-1 md:grid-cols-2 divide-y md:divide-y-0 md:divide-x divide-gray-100 dark:divide-gray-800">
                <div class="p-8 group hover:bg-gray-50/50 dark:hover:bg-gray-800/20 transition-colors">
                    <div class="flex items-center gap-3 mb-4">
                        <div
                            class="w-10 h-10 rounded-xl bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400 flex items-center justify-center shadow-sm">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                            </svg>
                        </div>
                        <h3 class="text-sm font-black text-gray-900 dark:text-white uppercase tracking-tight">Pickup
                            Location</h3>
                    </div>
                    <p class="text-sm text-gray-700 dark:text-gray-300 leading-relaxed font-medium mb-2">
                        {{ $shipment->pickup_address }}</p>
                </div>

                <div class="p-8 group hover:bg-gray-50/50 dark:hover:bg-gray-800/20 transition-colors">
                    <div class="flex items-center gap-3 mb-4">
                        <div
                            class="w-10 h-10 rounded-xl bg-green-50 dark:bg-green-900/20 text-green-600 dark:text-green-400 flex items-center justify-center shadow-sm">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                            </svg>
                        </div>
                        <h3 class="text-sm font-black text-gray-900 dark:text-white uppercase tracking-tight">Delivery
                            Location</h3>
                    </div>
                    <p class="text-sm text-gray-700 dark:text-gray-300 leading-relaxed font-medium mb-2">
                        {{ $shipment->delivery_address }}</p>
                </div>
            </div>

            <!-- Notes & Information Section -->
            @if ($shipment->description || $shipment->comment)
                <div class="bg-gray-50/50 dark:bg-gray-800/20 border-t border-gray-100 dark:border-gray-800 p-8">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        @if ($shipment->description)
                            <div class="space-y-3">
                                <div class="flex items-center gap-2">
                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                    <h4 class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Description
                                        des Marchandises</h4>
                                </div>
                                <p
                                    class="text-sm text-gray-600 dark:text-gray-400 leading-relaxed bg-white dark:bg-gray-900/50 p-4 rounded-xl border border-gray-100 dark:border-gray-800/50 italic">
                                    "{{ $shipment->description }}"
                                </p>
                            </div>
                        @endif

                        @if ($shipment->comment)
                            <div class="space-y-3">
                                <div class="flex items-center gap-2">
                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
                                    </svg>
                                    <h4 class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Commentaires
                                        Additionnels</h4>
                                </div>
                                <p
                                    class="text-sm text-gray-600 dark:text-gray-400 leading-relaxed bg-white dark:bg-gray-900/50 p-4 rounded-xl border border-gray-100 dark:border-gray-800/50 italic">
                                    "{{ $shipment->comment }}"
                                </p>
                            </div>
                        @endif
                    </div>
                </div>
            @endif

            <!-- Special Conditions Section -->
            @php
                $requirementsMap = [
                    'isDangerous' => [
                        'label' => 'Matières Dangereuses',
                        'descKey' => 'dangerousGoodsDescription',
                        'icon' =>
                            'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z',
                        'color' => 'red',
                    ],
                    'isUrgent' => [
                        'label' => 'Transport Urgent',
                        'descKey' => 'urgentDescription',
                        'icon' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z',
                        'color' => 'orange',
                    ],
                    'hasInsuranceOption' => [
                        'label' => 'Assurance Requise',
                        'descKey' => 'insuranceDescription',
                        'icon' =>
                            'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z',
                        'color' => 'blue',
                    ],
                    'hasSpecialHandlingInstructions' => [
                        'label' => 'Manipulation Spéciale',
                        'descKey' => 'specialHandlingDescription',
                        'icon' =>
                            'M7 11.5V14m0-2.5v-6a1.5 1.5 0 113 0V12m-3-0.5a3 3 0 00-6 0v2.5m6-2.5c.348 0 .678.046 1 .132V3.5a1.5 1.5 0 013 0V12m-3-0.5a3 3 0 00-6 0v2.5m6-2.5c.348 0 .678.046 1 .132V1.5a1.5 1.5 0 013 0V12',
                        'color' => 'purple',
                    ],
                    'needsTemperatureControlledTransport' => [
                        'label' => 'Température Contrôlée',
                        'descKey' => 'temperatureControlledDescription',
                        'icon' =>
                            'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z',
                        'color' => 'cyan',
                    ],
                    'hasFragileGoods' => [
                        'label' => 'Marchandises Fragiles',
                        'descKey' => 'fragileGoodsDescription',
                        'icon' =>
                            'M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z',
                        'color' => 'rose',
                    ],
                    'hasAdditionalRequirements' => [
                        'label' => 'Exigences Additionnelles',
                        'descKey' => 'additionalRequirementsDescription',
                        'icon' =>
                            'M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4',
                        'color' => 'slate',
                    ],
                ];

                $activeRequirements = [];
                if (!empty($shipment->requirements)) {
                    foreach ($requirementsMap as $key => $config) {
                        if (!empty($shipment->requirements[$key])) {
                            $activeRequirements[] = [
                                'label' => $config['label'],
                                'description' => $shipment->requirements[$config['descKey']] ?? null,
                                'icon' => $config['icon'],
                                'color' => $config['color'],
                            ];
                        }
                    }
                }
            @endphp

            @if (count($activeRequirements) > 0)
                <div class="border-t border-gray-100 dark:border-gray-800 p-8">
                    <h4 class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-6">Conditions & Exigences
                        Spéciales</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        @foreach ($activeRequirements as $req)
                            <div
                                class="flex items-start gap-4 p-4 rounded-2xl bg-{{ $req['color'] }}-50/50 dark:bg-{{ $req['color'] }}-900/10 border border-{{ $req['color'] }}-100/50 dark:border-{{ $req['color'] }}-800/30">
                                <div
                                    class="w-8 h-8 rounded-lg bg-{{ $req['color'] }}-100 dark:bg-{{ $req['color'] }}-900/30 text-{{ $req['color'] }}-600 dark:text-{{ $req['color'] }}-400 flex items-center justify-center flex-none">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                            d="{{ $req['icon'] }}" />
                                    </svg>
                                </div>
                                <div>
                                    <p
                                        class="text-[11px] font-black text-{{ $req['color'] }}-700 dark:text-{{ $req['color'] }}-300 uppercase tracking-tight mb-1">
                                        {{ $req['label'] }}</p>
                                    <p
                                        class="text-xs text-{{ $req['color'] }}-600/80 dark:text-{{ $req['color'] }}-400/80 leading-relaxed font-medium">
                                        {{ $req['description'] ?: 'Aucune instruction spécifique fournie.' }}
                                    </p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>



        <!-- Merchandise Section -->
        <div class="mb-12">
            <div class="flex items-center justify-between mb-6 px-1">
                <h2 class="text-sm font-black uppercase tracking-[0.2em] text-gray-400">Merchandise Details</h2>
                <div class="flex items-center gap-4">
                    <span
                        class="text-[10px] font-black bg-gray-100 dark:bg-gray-800 text-gray-500 px-3 py-1 rounded-full uppercase tracking-widest">1
                        Lot</span>
                    <span
                        class="text-[10px] font-black bg-brand-50 dark:bg-brand-900/30 text-brand-600 dark:text-brand-400 px-3 py-1 rounded-full uppercase tracking-widest">{{ number_format($shipment->total_weight, 2) }}
                        kg total</span>
                </div>
            </div>

            <div
                class="bg-white dark:bg-gray-900 rounded-3xl border border-gray-100 dark:border-gray-800 shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-50/50 dark:bg-gray-800/50 border-b border-gray-100 dark:border-gray-800">
                                <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest w-16">#
                                </th>
                                <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Type &
                                    Description</th>
                                <th
                                    class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center">
                                    Qty</th>
                                <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">
                                    Dimensions</th>
                                <th
                                    class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest text-right">
                                    Total Weight</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50 dark:divide-gray-800">
                            @if ($shipment->lot)
                                @php $lot = $shipment->lot; @endphp
                                <tr class="group hover:bg-gray-50/30 dark:hover:bg-gray-800/30 transition-colors">
                                    <td class="px-6 py-5">
                                        <span
                                            class="w-8 h-8 rounded-lg bg-gray-50 dark:bg-gray-800 flex items-center justify-center text-[10px] font-black text-gray-400 group-hover:bg-brand-50 group-hover:text-brand-600 transition-colors">
                                            1
                                        </span>
                                    </td>
                                    <td class="px-6 py-5">
                                        <p
                                            class="text-sm font-black text-gray-900 dark:text-white uppercase tracking-tight mb-1">
                                            {{ str_replace('_', ' ', $lot->type) }}</p>
                                        <div class="flex gap-2">
                                            @if ($lot->is_stackable)
                                                <span
                                                    class="px-1.5 py-0.5 rounded bg-green-50 dark:bg-green-900/20 text-green-600 dark:text-green-400 text-[9px] font-black uppercase">Stackable</span>
                                            @endif
                                            @if ($lot->is_rolling)
                                                <span
                                                    class="px-1.5 py-0.5 rounded bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400 text-[9px] font-black uppercase">Rolling</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-6 py-5 text-center">
                                        <span
                                            class="text-sm font-black text-gray-900 dark:text-white bg-gray-50 dark:bg-gray-800 px-2 py-1 rounded-md">x{{ $lot->quantity }}</span>
                                    </td>
                                    <td class="px-6 py-5">
                                        <div
                                            class="flex items-center gap-1.5 text-xs font-bold text-gray-600 dark:text-gray-400">
                                            <span>{{ $lot->length }}m</span>
                                            <span class="text-gray-300">×</span>
                                            <span>{{ $lot->width }}m</span>
                                            <span class="text-gray-300">×</span>
                                            <span>{{ $lot->height }}m</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-5 text-right">
                                        <p class="text-sm font-black text-gray-900 dark:text-white">
                                            {{ number_format($lot->weight * $lot->quantity, 2) }} <span
                                                class="text-[10px] text-gray-400 ml-0.5">kg</span></p>
                                    </td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Alpine Manager -->
        <!-- Alpine Manager -->
        <script>
            window.transportFirmBidManager = (config) => ({
                shipment: config.shipment,
                myBid: config.myBid,
                allBids: config.allBids || [],
                userId: config.userId,
                isUrgentValidity: config.isUrgentValidity,
                submittingBid: false,
                acceptingBid: false,
                openBidModal: false,
                messages: config.messages || [],
                newMessage: '',
                sending: false,

                isEchoSetup: false,
                activeChannels: new Set(),

                init() {
                    if (window.Echo) {
                        // Link Echo's socket ID with Axios to enable toOthers() broadcasting
                        if (window.Echo.connector && window.Echo.connector.pusher) {
                            window.Echo.connector.pusher.connection.bind('connected', () => {
                                window.axios.defaults.headers.common['X-Socket-Id'] = window.Echo.socketId();
                            });
                        }
                    }
                    this.setupEcho();
                    this.scrollToBottom();
                    if (this.myBid) this.markAsRead(this.myBid.id);
                },

                setupEcho() {
                    if (this.isEchoSetup) return;
                    if (this.shipment.id && window.Echo) {
                        console.log('--- Initializing Echo for Shipment:', this.shipment.id);
                        this.isEchoSetup = true;
                        window.Echo.private(`shipment.${this.shipment.id}`)
                            .listen('.BidUpdated', (e) => {
                                console.log('Received BidUpdated:', e);
                                // 1. Update shipment status from the event
                                if (e.shipment_status) {
                                    this.shipment.status = e.shipment_status;
                                }

                                // 2. Update myBid if this event is for the current user's bid
                                if (this.myBid && String(this.myBid.id) === String(e.bid_id)) {
                                    this.myBid = {
                                        ...this.myBid,
                                        status: e.status,
                                        price: e.price,
                                        updated_at_human: e.updated_at
                                    };
                                }

                                // 2. Update the bid in the sidebar list (for shippers/admins)
                                const index = this.allBids.findIndex(b => String(b.id) === String(e.bid_id));
                                if (index !== -1) {
                                    this.allBids[index].status = e.status;
                                    this.allBids[index].price = e.price;
                                    this.allBids[index].updated_at_human = e.updated_at;
                                } else {
                                    this.allBids.unshift({
                                        id: e.bid_id,
                                        price: e.price,
                                        status: e.status,
                                        updated_at_human: e.updated_at,
                                        unread_count: 0,
                                        last_message: 'Nouveau'
                                    });
                                    // Listen to chat for this new bid too
                                    this.listenToBidChannel(e.bid_id);
                                }

                                // 3. Mass rejection logic: If a bid is accepted, all others are rejected
                                if (e.status === 'accepted' && e.shipment_status === 'active') {
                                    console.log('--- Bid accepted, rejecting all others in UI');
                                    this.allBids.forEach(b => {
                                        if (String(b.id) !== String(e.bid_id)) {
                                            b.status = 'rejected';
                                        }
                                    });
                                    if (this.myBid && String(this.myBid.id) !== String(e.bid_id)) {
                                        this.myBid.status = 'rejected';
                                    }
                                }
                            });

                        // 2. Bid Channels (Chat)
                        this.setupBidChannels();
                    }
                },

                setupBidChannels() {
                    if (!window.Echo) return;

                    const isShipper =
                        {{ auth()->user()->hasRole('shipper') || auth()->user()->hasRole('admin') ? 'true' : 'false' }};
                    const bidIds = isShipper ? this.allBids.map(b => b.id) : (this.myBid ? [this.myBid.id] : []);

                    bidIds.forEach(bidId => {
                        this.listenToBidChannel(bidId);
                    });
                },

                activeChannels: new Set(),

                listenToBidChannel(bidId) {
                    if (!window.Echo || !bidId || this.activeChannels.has(String(bidId))) return;

                    console.log('--- Listening to Bid Channel:', bidId);
                    const isShipper =
                        {{ auth()->user()->hasRole('shipper') || auth()->user()->hasRole('admin') ? 'true' : 'false' }};

                    window.Echo.private(`bid.${bidId}`)
                        .listen('.NewBidMessage', (e) => {
                            console.log('--- Incoming Message for Bid ' + bidId + ':', e);

                            const bid = this.allBids.find(b => String(b.id) === String(e.bid_id));
                            if (bid) {
                                bid.last_message = e.message;
                                bid.updated_at_human = 'À l\'instant';

                                const isCurrentlyViewing = this.myBid && String(this.myBid.id) === String(e.bid_id);
                                const isFromMe = String(this.userId) === String(e.user_id);

                                if (!isCurrentlyViewing && !isFromMe) {
                                    bid.unread_count = (bid.unread_count || 0) + 1;
                                }
                            }

                            if (this.myBid && String(this.myBid.id) === String(e.bid_id)) {
                                const messageExists = this.messages.some(m => String(m.id) === String(e.id));
                                if (!messageExists) {
                                    console.log('--- Adding new message to UI');
                                    this.messages.push({
                                        id: e.id,
                                        message: e.message,
                                        user_id: e.user_id,
                                        user_name: String(this.userId) === String(e.user_id) ? 'Vous' : (
                                            isShipper ? 'Transporteur' : 'Client'),
                                        created_at: e.created_at
                                    });
                                    this.scrollToBottom();
                                    if (String(this.userId) !== String(e.user_id)) {
                                        this.markAsRead(e.bid_id);
                                    }
                                } else {
                                    console.log('--- Message already exists in UI, skipping');
                                }
                            }
                        });

                    this.activeChannels.add(String(bidId));
                },

                async submitBid(isNegotiable, formData = {}) {
                    if (this.submittingBid) return;
                    this.submittingBid = true;
                    try {
                        const response = await axios.post(@js(route('transport-firm-bid.store-bid', $shipment)), {
                            is_negotiable: isNegotiable,
                            ...formData
                        });
                        this.myBid = response.data.bid;
                        this.messages = this.myBid.messages || [];
                        this.setupEcho();
                        Swal.fire({
                            icon: 'success',
                            title: 'Succès',
                            text: response.data.message,
                            timer: 2000,
                            showConfirmButton: false
                        });
                    } catch (error) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Erreur',
                            text: error.response?.data?.message || 'Une erreur est survenue.'
                        });
                    } finally {
                        this.submittingBid = false;
                    }
                },

                async acceptBid(bidId) {
                    if (this.acceptingBid) return;
                    this.acceptingBid = true;
                    try {
                        const url = "{{ route('transport-firm-bid.accept-bid', ':bidId') }}".replace(':bidId',
                            bidId);
                        const response = await axios.post(url);

                        // 1. Update this specific bid
                        if (this.myBid && this.myBid.id == bidId) this.myBid.status = 'accepted';

                        // 2. Update shipment status
                        this.shipment.status = response.data.shipment_status;

                        // 3. Update local bids list (Reject all others)
                        this.allBids.forEach(b => {
                            if (b.id == bidId) b.status = 'accepted';
                            else b.status = 'rejected';
                        });

                        Swal.fire({
                            icon: 'success',
                            title: 'Succès',
                            text: response.data.message,
                            timer: 2000,
                            showConfirmButton: false
                        });
                    } catch (error) {
                        console.error('Accept error:', error);
                        Swal.fire({
                            icon: 'error',
                            title: 'Erreur',
                            text: error.response?.data?.message || 'Une erreur est survenue'
                        });
                    } finally {
                        this.acceptingBid = false;
                    }
                },

                async sendMessage() {
                    if (!this.newMessage.trim() || this.sending || !this.myBid) return;
                    this.sending = true;
                    try {
                        const url = "{{ route('transport-firm-bid.store-message', ':bidId') }}".replace(':bidId',
                            this.myBid.id);
                        const response = await axios.post(url, {
                            message: this.newMessage
                        });
                        this.messages.push({
                            id: response.data.bid_message.id,
                            message: response.data.bid_message.message,
                            user_id: this.userId,
                            user_name: 'Vous',
                            created_at: new Date().toLocaleTimeString([], {
                                hour: '2-digit',
                                minute: '2-digit'
                            })
                        });
                        this.newMessage = '';

                        const bid = this.allBids.find(b => b.id == this.myBid.id);
                        if (bid) bid.last_message = response.data.bid_message.message;

                        this.scrollToBottom();
                    } catch (error) {
                        console.error('Message error:', error);
                        Swal.fire({
                            icon: 'error',
                            title: 'Erreur',
                            text: 'Impossible d\'envoyer le message.'
                        });
                    } finally {
                        this.sending = false;
                    }
                },

                async completeShipment(shipmentId) {
                    try {
                        const url = "{{ route('transport-firm-bid.complete-shipment', ':shipmentId') }}".replace(
                            ':shipmentId', shipmentId);
                        const response = await axios.post(url);
                        this.shipment.status = response.data.shipment_status;
                        Swal.fire({
                            icon: 'success',
                            title: 'Succès',
                            text: response.data.message,
                            timer: 2000,
                            showConfirmButton: false
                        }).then(() => {
                            window.location.reload();
                        });
                    } catch (error) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Erreur',
                            text: 'Impossible de terminer l\'expédition.'
                        });
                    }
                },

                switching: false,

                async selectBid(bidId) {
                    if (this.switching || (this.myBid && this.myBid.id == bidId)) return;
                    this.switching = true;
                    try {
                        const url = "{{ route('transport-firm-bid.show', ':shipmentId') }}".replace(':shipmentId',
                            this.shipment.id) + '?bid_id=' + bidId;
                        const response = await axios.get(url, {
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        });

                        this.myBid = response.data.bid;
                        this.messages = response.data.messages;

                        const bid = this.allBids.find(b => b.id == bidId);
                        if (bid) bid.unread_count = 0;

                        history.pushState(null, '', url);
                        this.scrollToBottom();
                        this.markAsRead(bidId);
                    } catch (error) {
                        console.error('Switch error:', error);
                        Swal.fire({
                            icon: 'error',
                            title: 'Erreur',
                            text: 'Impossible de charger la conversation.'
                        });
                    } finally {
                        this.switching = false;
                    }
                },

                async markAsRead(bidId) {
                    try {
                        const url = "{{ route('transport-firm-bid.mark-as-read', ':bidId') }}".replace(':bidId',
                            bidId);
                        await axios.post(url);
                    } catch (error) {
                        console.error('Mark as read error:', error);
                    }
                },

                scrollToBottom() {
                    this.$nextTick(() => {
                        const c = this.$refs.messagesContainer;
                        if (c) c.scrollTop = c.scrollHeight;
                    });
                }
            });
        </script>

        <!-- Negotiation & Discussion Section -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 mt-12" x-data="transportFirmBidManager({
            shipment: @js($shipment),
            myBid: @js($myBid),
            allBids: @js($allBids ?? []),
            userId: @js(auth()->id()),
            isUrgentValidity: @js($isUrgentValidity),
            messages: @js(
    $myBid
        ? $myBid->messages->map(
            fn($m) => [
                'id' => $m->id,
                'message' => $m->message,
                'user_id' => $m->user_id,
                'user_name' => $m->user_id === auth()->id() ? 'Vous' : (auth()->user()->hasRole('carrier') ? 'Client' : 'Transporteur'),
                'created_at' => $m->created_at->format('H:i'),
            ],
        )
        : [],
)
        })">
            <!-- Left: Proposition Details (4 cols) -->
            <div class="lg:col-span-4 space-y-6">
                <h2 class="text-sm font-black uppercase tracking-[0.2em] text-gray-400 px-1"
                    x-text="shipment.user_id === {{ auth()->id() }} ? 'Offre Sélectionnée' : 'Votre Proposition'"></h2>

                <template x-if="myBid">
                    <div
                        class="bg-white dark:bg-gray-900 rounded-3xl border border-gray-100 dark:border-gray-800 shadow-xl shadow-gray-200/20 p-8 relative overflow-hidden group">
                        <!-- Status Background Decor -->
                        <div
                            class="absolute -right-4 -top-4 w-24 h-24 bg-brand-500/5 rounded-full blur-2xl group-hover:bg-brand-500/10 transition-colors">
                        </div>

                        <div class="relative">
                            <div class="flex flex-wrap items-center gap-2">
                                <div class="px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-widest border"
                                    :class="myBid.status === 'accepted' ? 'bg-success-50 text-success-600 border-success-100' :
                                        (myBid.status === 'rejected' ? 'bg-red-50 text-red-600 border-red-100' :
                                            'bg-amber-50 text-amber-600 border-amber-100')">
                                    <span
                                        x-text="myBid.status === 'accepted' ? (myBid.is_negotiable ? 'Proposition Acceptée' : 'Demande Approuvée') : (myBid.status === 'rejected' ? 'Proposition Refusée' : (myBid.is_negotiable ? 'Proposition en cours' : 'Demande en cours'))"></span>
                                </div>
                                <div x-show="!myBid.is_negotiable"
                                    class="px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-widest bg-blue-50 text-blue-600 border border-blue-100 shadow-sm">
                                    Demande Directe
                                </div>
                                <span class="text-[10px] font-bold text-gray-400 ml-auto whitespace-nowrap"
                                    x-text="'Màj ' + (myBid.updated_at_human || 'À l\'instant')"></span>
                            </div>

                            <div class="space-y-6">
                                <div class="mt-6">
                                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2"
                                        x-text="myBid.is_negotiable ? 'Prix Proposé' : 'Prix de l\'Expédition (Accepté)'">
                                    </p>
                                    <h3 class="text-3xl font-black text-gray-900 dark:text-white">
                                        <span
                                            x-text="parseFloat(myBid.price || shipment.delivery_price).toLocaleString('fr-FR', {minimumFractionDigits: 2})"></span>
                                        <span class="text-lg font-bold text-gray-300">€</span>
                                    </h3>
                                </div>

                                <div class="grid grid-cols-1 gap-4">
                                    <div
                                        class="p-4 rounded-2xl bg-gray-50 dark:bg-gray-800/50 border border-gray-100 dark:border-gray-800">
                                        <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2">
                                            Collecte Proposée</p>
                                        <div class="flex items-center gap-2">
                                            <svg class="w-4 h-4 text-brand-500" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                            </svg>
                                            <span class="text-xs font-bold text-gray-700 dark:text-gray-300">
                                                <span
                                                    x-text="myBid.latest_pickup_date ? new Date(myBid.latest_pickup_date).toLocaleDateString('fr-FR') : (shipment.latest_pickup_date ? new Date(shipment.latest_pickup_date).toLocaleDateString('fr-FR') : '-')"></span>
                                                à <span
                                                    x-text="myBid.latest_pickup_time || (shipment.latest_pickup_time ? shipment.latest_pickup_time.substring(0, 5) : '-')"></span>
                                            </span>
                                        </div>
                                    </div>
                                    <div
                                        class="p-4 rounded-2xl bg-gray-50 dark:bg-gray-800/50 border border-gray-100 dark:border-gray-800">
                                        <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2">
                                            Livraison Proposée</p>
                                        <div class="flex items-center gap-2">
                                            <svg class="w-4 h-4 text-brand-500" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                            </svg>
                                            <span class="text-xs font-bold text-gray-700 dark:text-gray-300">
                                                <span
                                                    x-text="myBid.latest_delivery_date ? new Date(myBid.latest_delivery_date).toLocaleDateString('fr-FR') : (shipment.latest_delivery_date ? new Date(shipment.latest_delivery_date).toLocaleDateString('fr-FR') : '-')"></span>
                                                à <span
                                                    x-text="myBid.latest_delivery_time || (shipment.latest_delivery_time ? shipment.latest_delivery_time.substring(0, 5) : '-')"></span>
                                            </span>
                                        </div>
                                    </div>
                                </div>

                                <template
                                    x-if="shipment.status === 'pending' && shipment.user_id !== {{ auth()->id() }} && myBid.is_negotiable">
                                    <button @click="openBidModal = true"
                                        class="w-full py-4 bg-gray-900 dark:bg-white text-white dark:text-gray-900 rounded-2xl font-black uppercase text-[11px] tracking-[0.2em] hover:bg-brand-600 hover:text-white transition-all shadow-lg shadow-gray-200 dark:shadow-none">
                                        Modifier l'offre
                                    </button>
                                </template>

                                @if (auth()->user()->hasRole('shipper') || auth()->user()->hasRole('admin'))
                                    <template
                                        x-if="shipment.user_id === {{ auth()->id() }} && shipment.status === 'pending'">
                                        <form
                                            :action="'{{ route('transport-firm-bid.accept-bid', ['bid' => ':bidId']) }}'.replace
                                                (':bidId', myBid.id)"
                                            method="POST" class="mt-4" @submit.prevent="acceptBid(myBid.id)">
                                            @csrf
                                            <button type="submit" :disabled="acceptingBid"
                                                class="w-full py-4 bg-success-500 text-white rounded-2xl font-black uppercase text-[11px] tracking-[0.2em] hover:bg-success-600 transition-all shadow-lg shadow-success-500/20 disabled:opacity-50">
                                                <span x-show="!acceptingBid"
                                                    x-text="myBid.is_negotiable ? 'Accepter cette offre' : 'Approuver cette demande'"></span>
                                                <span x-show="acceptingBid"
                                                    class="flex items-center justify-center gap-2">
                                                    <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                                                        <circle class="opacity-25" cx="12" cy="12" r="10"
                                                            stroke="currentColor" stroke-width="4"></circle>
                                                        <path class="opacity-75" fill="currentColor"
                                                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                                        </path>
                                                    </svg>
                                                    Traitement...
                                                </span>
                                            </button>
                                        </form>
                                    </template>
                                @endif
                            </div>
                        </div>
                    </div>
                </template>

                <template x-if="!myBid && shipment.user_id !== {{ auth()->id() }} && shipment.status === 'pending'">
                    <div
                        class="bg-gradient-to-br from-brand-500 to-brand-600 rounded-3xl p-8 text-white shadow-xl shadow-brand-500/20">
                        <h3 class="text-xl font-black mb-4"
                            x-text="isUrgentValidity ? 'Choisissez votre mode' : 'Aucune demande envoyée'"></h3>
                        <p class="text-sm text-brand-50 font-medium mb-6 leading-relaxed"
                            x-text="isUrgentValidity ? 'Vous pouvez négocier vos tarifs ou envoyer une demande directe en acceptant les conditions du client.' : 'Envoyez une demande de transport pour signaler votre intérêt au client.'">
                        </p>
                        <div class="flex flex-col gap-3">
                            <template x-if="isUrgentValidity">
                                <button @click="openBidModal = true"
                                    class="w-full py-4 bg-white text-brand-600 rounded-2xl font-black uppercase text-[11px] tracking-[0.2em] hover:bg-brand-50 transition-all">
                                    Proposer un prix (Négocier)
                                </button>
                            </template>

                            <button @click="submitBid(0)" :disabled="submittingBid"
                                class="w-full py-4 text-white rounded-2xl font-black uppercase text-[11px] tracking-[0.2em] hover:bg-brand-400/50 transition-all border border-brand-300/50"
                                :class="isUrgentValidity ? 'bg-brand-400/30' : 'bg-white/10'">
                                <span x-show="!submittingBid"
                                    x-text="isUrgentValidity ? 'Demande Directe' : 'Envoyer une demande'"></span>
                                <span x-show="submittingBid" class="flex items-center justify-center gap-2">
                                    <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10"
                                            stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor"
                                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                        </path>
                                    </svg>
                                    Envoi...
                                </span>
                            </button>
                        </div>
                    </div>
                </template>

                <!-- Proposition Modal -->
                <x-ui.modal model="openBidModal" title="Faire une proposition de prix" maxWidth="max-w-xl">
                    <form
                        @submit.prevent="submitBid(1, Object.fromEntries(new FormData($event.target))); openBidModal = false"
                        class="p-8 space-y-6">
                        <div class="grid grid-cols-1 gap-6">
                            <!-- Price -->
                            <div>
                                <label
                                    class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Votre
                                    Prix (€)</label>
                                <input type="number" name="price" step="0.01"
                                    :value="myBid ? myBid.price : shipment.delivery_price"
                                    class="w-full bg-gray-50 border-gray-100 rounded-xl px-4 py-3 text-sm font-bold focus:ring-brand-500">
                            </div>

                            <!-- Pickup -->
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label
                                        class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Date
                                        Collecte</label>
                                    <input type="date" name="latest_pickup_date"
                                        :value="myBid && myBid.latest_pickup_date ? myBid.latest_pickup_date.split('T')[0] : (
                                            shipment.latest_pickup_date ? shipment.latest_pickup_date.split('T')[
                                            0] : '')"
                                        class="w-full bg-gray-50 border-gray-100 rounded-xl px-4 py-3 text-sm font-bold focus:ring-brand-500">
                                </div>
                                <div>
                                    <label
                                        class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Heure
                                        Collecte</label>
                                    <input type="time" name="latest_pickup_time"
                                        :value="myBid ? myBid.latest_pickup_time : (shipment.latest_pickup_time ? shipment
                                            .latest_pickup_time.substring(0, 5) : '')"
                                        class="w-full bg-gray-50 border-gray-100 rounded-xl px-4 py-3 text-sm font-bold focus:ring-brand-500">
                                </div>
                            </div>

                            <!-- Delivery -->
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label
                                        class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Date
                                        Livraison</label>
                                    <input type="date" name="latest_delivery_date"
                                        :value="myBid && myBid.latest_delivery_date ? myBid.latest_delivery_date.split('T')[0] :
                                            (shipment.latest_delivery_date ? shipment.latest_delivery_date.split('T')[
                                                0] : '')"
                                        class="w-full bg-gray-50 border-gray-100 rounded-xl px-4 py-3 text-sm font-bold focus:ring-brand-500">
                                </div>
                                <div>
                                    <label
                                        class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Heure
                                        Livraison</label>
                                    <input type="time" name="latest_delivery_time"
                                        :value="myBid ? myBid.latest_delivery_time : (shipment.latest_delivery_time ? shipment
                                            .latest_delivery_time.substring(0, 5) : '')"
                                        class="w-full bg-gray-50 border-gray-100 rounded-xl px-4 py-3 text-sm font-bold focus:ring-brand-500">
                                </div>
                            </div>

                            <!-- Message -->
                            <div>
                                <label
                                    class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Message
                                    (Optionnel)</label>
                                <textarea name="message" rows="3"
                                    class="w-full bg-gray-50 border-gray-100 rounded-xl px-4 py-3 text-sm font-medium focus:ring-brand-500"
                                    placeholder="Ajoutez un commentaire à votre offre..."></textarea>
                            </div>
                        </div>

                        <div class="flex gap-3 pt-6">
                            <button type="button" @click="openBidModal = false"
                                class="flex-1 py-4 bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300 rounded-xl font-black uppercase text-[10px] tracking-widest hover:bg-gray-200 transition-all">Annuler</button>
                            <button type="submit" :disabled="submittingBid"
                                class="flex-1 py-4 bg-brand-500 text-white rounded-xl font-black uppercase text-[10px] tracking-widest shadow-lg shadow-brand-500/30 hover:bg-brand-600 transition-all">
                                <span x-show="!submittingBid">Envoyer l'offre</span>
                                <span x-show="submittingBid">Envoi...</span>
                            </button>
                        </div>
                    </form>
                </x-ui.modal>
            </div>

            <!-- Right: Discussion Thread (8 cols) -->
            <div class="lg:col-span-8 space-y-6">
                <h2 class="text-sm font-black uppercase tracking-[0.2em] text-gray-400 px-1">Discussion avec le client</h2>

                <div
                    class="bg-white dark:bg-gray-900 rounded-3xl border border-gray-100 dark:border-gray-800 shadow-xl shadow-gray-200/20 flex flex-col md:flex-row min-h-[600px] overflow-hidden">

                    <!-- Bids Sidebar (Only for Shippers/Admins) -->
                    @if ((auth()->user()->hasRole('shipper') || auth()->user()->hasRole('admin')) && isset($allBids))
                        <div
                            class="w-full md:w-72 border-b md:border-b-0 md:border-r border-gray-100 dark:border-gray-800 flex flex-col bg-gray-50/20 dark:bg-gray-900/40">
                            <div class="p-6 border-b border-gray-100 dark:border-gray-800">
                                <h3 class="text-[10px] font-black uppercase tracking-widest text-gray-400">
                                    {{ $isUrgentValidity ? 'Offres Reçues' : 'Demandes Reçues' }} (<span
                                        x-text="allBids.length"></span>)
                                </h3>
                            </div>
                            <div class="flex-1 overflow-y-auto custom-scrollbar">
                                <template x-for="bid in allBids" :key="bid.id">
                                    <a @click.prevent="selectBid(bid.id)" href="#"
                                        class="group block p-4 border-b border-gray-50 dark:border-gray-800/50 hover:bg-brand-50/30 dark:hover:bg-gray-800 transition-all relative"
                                        :class="(myBid && bid.id == myBid.id) ? 'bg-brand-50/50 dark:bg-gray-800 shadow-inner' :
                                        ''">

                                        <div x-show="myBid && bid.id == myBid.id"
                                            class="absolute inset-y-0 left-0 w-1 bg-brand-500"></div>

                                        <div class="flex gap-3">
                                            <!-- Avatar -->
                                            <div
                                                class="flex-shrink-0 w-10 h-10 rounded-full bg-gray-100 dark:bg-gray-800 flex items-center justify-center font-black text-gray-400 text-xs uppercase shadow-sm">
                                                <span x-text="'T' + String(bid.id).slice(-1)"></span>
                                            </div>

                                            <!-- Content -->
                                            <div class="flex-1 min-w-0">
                                                <div class="flex justify-between items-baseline mb-0.5">
                                                    <h4 class="text-xs font-black text-gray-900 dark:text-white truncate pr-2 uppercase tracking-tight"
                                                        x-text="'Transporteur #' + bid.id"></h4>
                                                    <span class="text-[9px] font-bold uppercase whitespace-nowrap"
                                                        :class="bid.unread_count > 0 ? 'text-success-500' : 'text-gray-400'"
                                                        x-text="bid.updated_at_human"></span>
                                                </div>

                                                <div class="flex justify-between items-center">
                                                    <p class="text-[11px] truncate pr-4"
                                                        :class="bid.unread_count > 0 ? 'text-success-600 font-bold' :
                                                            'text-gray-500'"
                                                        x-text="bid.last_message || 'Pas de message'"></p>

                                                    <!-- Unread Badge -->
                                                    <div x-show="bid.unread_count > 0"
                                                        class="flex-shrink-0 min-w-[18px] h-[18px] bg-success-500 text-white text-[9px] font-black rounded-full flex items-center justify-center px-1 shadow-sm shadow-success-500/20"
                                                        x-text="bid.unread_count">
                                                    </div>
                                                </div>

                                                <div class="flex items-center justify-between mt-2">
                                                    <span class="text-xs font-black px-2 py-0.5 rounded-md border"
                                                        :class="bid.status === 'accepted' ?
                                                            'bg-success-50 text-success-600 border-success-100' : (bid
                                                                .status === 'rejected' ?
                                                                'bg-red-50 text-red-600 border-red-100' :
                                                                'bg-gray-50 text-gray-500 border-gray-100')"
                                                        x-text="bid.status">
                                                    </span>
                                                    <span class="text-[9px] font-black text-brand-600"
                                                        x-text="(parseFloat(bid.price) || parseFloat(shipment.delivery_price)).toFixed(2) + '€'"></span>
                                                </div>
                                            </div>
                                        </div>
                                    </a>
                                </template>
                            </div>
                        </div>
                    @endif

                    <!-- Chat Area -->
                    <div class="flex-1 flex flex-col bg-white dark:bg-gray-900">
                        <!-- Chat Header -->
                        <div
                            class="p-6 border-b border-gray-50 dark:border-gray-800 flex items-center justify-between bg-gray-50/30 dark:bg-gray-800/20">
                            <div class="flex items-center gap-3">
                                <div
                                    class="w-10 h-10 rounded-full bg-brand-100 dark:bg-brand-900/30 text-brand-600 dark:text-brand-400 flex items-center justify-center font-black text-sm">
                                    <span x-text="{{ auth()->user()->hasRole('carrier') ? "'C'" : "'T'" }}"></span>
                                </div>
                                <div>
                                    <p class="text-xs font-black text-gray-900 dark:text-white uppercase tracking-tight">
                                        <span
                                            x-text="{{ auth()->user()->hasRole('carrier') ? "'Client'" : "'Transporteur #'" }} + (myBid ? myBid.id : '...')"></span>
                                    </p>
                                    <p
                                        class="text-[10px] font-bold text-success-500 uppercase tracking-widest flex items-center gap-1.5">
                                        <span class="w-1.5 h-1.5 rounded-full bg-success-500 animate-pulse"></span>
                                        <span
                                            x-text="{{ auth()->user()->hasRole('carrier') ? "'Client en ligne'" : "'Transporteur en ligne'" }}"></span>
                                    </p>
                                </div>
                            </div>

                            <div class="flex items-center gap-4">
                                @if ((auth()->user()->hasRole('shipper') || auth()->user()->hasRole('admin')) && $shipment->status === 'pending')
                                    <template x-if="myBid && myBid.status !== 'accepted' && shipment.status === 'pending'">
                                        <button @click="acceptBid(myBid.id)" :disabled="acceptingBid"
                                            class="px-6 py-2.5 bg-success-500 text-white rounded-xl font-black uppercase text-[10px] tracking-widest shadow-lg shadow-success-500/20 hover:bg-success-600 transition-all disabled:opacity-50">
                                            <span x-show="!acceptingBid"
                                                x-text="myBid.is_negotiable ? 'Accepter cette offre' : 'Approuver cette demande'"></span>
                                            <span x-show="acceptingBid" class="flex items-center justify-center gap-2">
                                                <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                                                    <circle class="opacity-25" cx="12" cy="12" r="10"
                                                        stroke="currentColor" stroke-width="4"></circle>
                                                    <path class="opacity-75" fill="currentColor"
                                                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                                    </path>
                                                </svg>
                                                ...
                                            </span>
                                        </button>
                                    </template>
                                @endif

                                @if (auth()->user()->hasRole('shipper') || auth()->user()->hasRole('admin'))
                                    <template x-if="shipment.status === 'active' && myBid && myBid.status === 'accepted'">
                                        <button @click="completeShipment(shipment.id)"
                                            class="px-6 py-2.5 bg-brand-500 text-white rounded-xl font-black uppercase text-[10px] tracking-widest shadow-lg shadow-brand-500/30 hover:bg-brand-600 transition-all">
                                            Marquer comme terminé
                                        </button>
                                    </template>
                                @endif
                            </div>
                        </div>

                        <!-- Rating/Review Section -->
                        @if ($shipment->status === 'completed' && $myBid && $myBid->status === 'accepted')
                            @if (!$shipment->review)
                                @if (auth()->id() === $shipment->user_id)
                                    <!-- Rating Form -->
                                    <div class="m-6 p-6 bg-brand-50/50 dark:bg-brand-900/10 border border-brand-100 dark:border-brand-800 rounded-3xl shadow-sm"
                                        x-data="{ rating: 5 }">
                                        <div class="flex items-center gap-4 mb-6">
                                            <div
                                                class="w-12 h-12 rounded-2xl bg-brand-500 text-white flex items-center justify-center shadow-lg shadow-brand-500/20">
                                                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                                                    <path
                                                        d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                                </svg>
                                            </div>
                                            <div>
                                                <h3
                                                    class="text-sm font-black text-gray-900 dark:text-white uppercase tracking-tight">
                                                    Laissez un avis</h3>
                                                <p
                                                    class="text-[10px] font-bold text-gray-500 dark:text-gray-400 uppercase tracking-widest">
                                                    Comment s'est déroulée la livraison ?</p>
                                            </div>
                                        </div>

                                        <form action="{{ route('transport-firm-bid.store-review', $shipment->id) }}"
                                            method="POST" class="space-y-6">
                                            @csrf
                                            <input type="hidden" name="rating" :value="rating">

                                            <div class="flex items-center gap-2">
                                                <template x-for="i in 5">
                                                    <button type="button" @click="rating = i"
                                                        class="transition-all duration-200"
                                                        :class="rating >= i ? 'text-amber-400 scale-110' :
                                                            'text-gray-200 dark:text-gray-700'">
                                                        <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 20 20">
                                                            <path
                                                                d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                                        </svg>
                                                    </button>
                                                </template>
                                                <span
                                                    class="ml-4 text-xs font-black text-brand-600 dark:text-brand-400 uppercase tracking-widest"
                                                    x-text="rating + '/5'"></span>
                                            </div>

                                            <textarea name="comment" rows="3"
                                                class="w-full bg-white dark:bg-gray-800 border-brand-100 dark:border-brand-800 rounded-2xl px-4 py-3 text-sm font-medium focus:ring-brand-500 focus:border-brand-500 placeholder-gray-400 shadow-inner"
                                                placeholder="Partagez votre expérience avec ce transporteur..."></textarea>

                                            <button type="submit"
                                                class="w-full py-4 bg-brand-500 text-white rounded-2xl font-black uppercase text-[10px] tracking-widest shadow-lg shadow-brand-500/20 hover:bg-brand-600 transform hover:-translate-y-0.5 transition-all">
                                                Publier l'avis
                                            </button>
                                        </form>
                                    </div>
                                @endif
                            @else
                                <!-- Display Existing Review -->
                                <div
                                    class="m-6 p-6 bg-success-50/50 dark:bg-success-900/10 border border-success-100 dark:border-success-800 rounded-3xl shadow-sm">
                                    <div class="flex items-center justify-between mb-4">
                                        <div class="flex items-center gap-1.5">
                                            @for ($i = 1; $i <= 5; $i++)
                                                <svg class="w-4 h-4 {{ $shipment->review->rating >= $i ? 'text-amber-400' : 'text-gray-200 dark:text-gray-700' }}"
                                                    fill="currentColor" viewBox="0 0 20 20">
                                                    <path
                                                        d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                                </svg>
                                            @endfor
                                        </div>
                                        <span
                                            class="text-[9px] font-black text-success-600 dark:text-success-400 uppercase tracking-widest bg-success-100 dark:bg-success-900/30 px-2 py-0.5 rounded-full">Avis
                                            publié</span>
                                    </div>
                                    <p class="text-sm font-medium text-gray-700 dark:text-gray-300 italic">
                                        "{{ $shipment->review->comment }}"</p>
                                </div>
                            @endif
                        @endif

                        <!-- Messages area -->
                        <div x-ref="messagesContainer"
                            class="flex-1 p-8 space-y-8 overflow-y-auto max-h-[500px] scroll-smooth">
                            <template x-if="messages.length === 0">
                                <div class="h-full flex flex-col items-center justify-center text-center opacity-40">
                                    <svg class="w-16 h-16 text-gray-300 mb-4" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                            d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                                    </svg>
                                    <p class="text-sm font-bold text-gray-400 uppercase tracking-widest">Aucun message pour
                                        le moment</p>
                                </div>
                            </template>

                            <template x-for="msg in messages" :key="msg.id">
                                <div class="flex group" :class="msg.user_id == userId ? 'justify-end' : 'justify-start'">
                                    <div class="max-w-[80%] space-y-1.5">
                                        <div class="flex items-center gap-3 px-1"
                                            :class="msg.user_id == userId ? 'flex-row-reverse' : ''">
                                            <span class="text-[9px] font-black text-gray-400 uppercase tracking-widest"
                                                x-text="msg.user_id == userId ? 'Vous' : msg.user_name"></span>
                                            <span class="text-[9px] text-gray-300 font-bold"
                                                x-text="msg.created_at"></span>
                                        </div>
                                        <div class="px-5 py-4 rounded-3xl shadow-sm border"
                                            :class="msg.user_id == userId ?
                                                'bg-brand-500 text-white border-brand-400 rounded-tr-none' :
                                                'bg-gray-50 dark:bg-gray-800 text-gray-700 dark:text-gray-300 border-gray-100 dark:border-gray-700 rounded-tl-none'">
                                            <p class="text-sm font-medium leading-relaxed" x-text="msg.message"></p>
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </div>

                        <!-- Input area -->
                        <div class="p-6 bg-gray-50/50 dark:bg-gray-800/20 border-t border-gray-50 dark:border-gray-800">
                            <template x-if="myBid">
                                <div>
                                    <template
                                        x-if="shipment.status === 'pending' && myBid.status !== 'rejected' && ((myBid.is_negotiable && isUrgentValidity) || myBid.status === 'accepted')">
                                        <form @submit.prevent="sendMessage" class="flex items-center gap-4">
                                            @csrf
                                            <div class="relative flex-1">
                                                <input type="text" x-model="newMessage" required
                                                    placeholder="Écrire un message..."
                                                    class="w-full bg-white dark:bg-gray-900 border-gray-100 dark:border-gray-800 rounded-2xl px-6 py-4 text-sm font-medium focus:ring-brand-500 focus:border-brand-500 transition-all pr-12 shadow-sm">
                                                <button type="button"
                                                    class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-300 hover:text-brand-500 transition-colors p-2">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                                                    </svg>
                                                </button>
                                            </div>
                                            <button type="submit" :disabled="sending"
                                                class="w-14 h-14 bg-brand-500 text-white rounded-2xl flex items-center justify-center hover:bg-brand-600 shadow-lg shadow-brand-500/30 transition-all flex-none group disabled:opacity-50">
                                                <svg x-show="!sending"
                                                    class="w-6 h-6 transform group-hover:translate-x-0.5 group-hover:-translate-y-0.5 transition-transform"
                                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2.5" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                                                </svg>
                                                <svg x-show="sending" class="w-6 h-6 animate-spin" fill="none"
                                                    viewBox="0 0 24 24">
                                                    <circle class="opacity-25" cx="12" cy="12" r="10"
                                                        stroke="currentColor" stroke-width="4"></circle>
                                                    <path class="opacity-75" fill="currentColor"
                                                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                                    </path>
                                                </svg>
                                            </button>
                                        </form>
                                    </template>
                                    <template
                                        x-if="shipment.status !== 'pending' || !((myBid.is_negotiable && isUrgentValidity) || myBid.status === 'accepted')">
                                        <div
                                            class="flex items-center justify-center p-4 bg-gray-50 dark:bg-gray-800 rounded-2xl">
                                            <p class="text-xs font-bold text-gray-400 uppercase tracking-widest text-center"
                                                x-text="shipment.status !== 'pending'
                                            ? 'Cette discussion est désormais clôturée car l\'expédition est en cours ou terminée.'
                                            : (myBid.status === 'rejected'
                                                ? 'Cette expédition n\'est plus disponible pour négociation.'
                                                : (!myBid.is_negotiable 
                                                    ? 'La messagerie est désactivée pour les demandes directes (jusqu\'à acceptation).' 
                                                    : 'La messagerie sera disponible lorsque l\'offre sera urgente ou acceptée.'))">
                                            </p>
                                        </div>
                                    </template>
                                </div>
                            </template>
                            <template x-if="!myBid">
                                <div
                                    class="flex items-center justify-center p-4 bg-amber-50 dark:bg-amber-900/10 border border-amber-100 dark:border-amber-800 rounded-2xl">
                                    <p class="text-xs font-bold text-amber-600 dark:text-amber-400 uppercase tracking-widest"
                                        x-text="isUrgentValidity ? 'Veuillez faire une proposition pour commencer la discussion.' : 'Veuillez envoyer une demande pour signaler votre intérêt.'">
                                    </p>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endsection
