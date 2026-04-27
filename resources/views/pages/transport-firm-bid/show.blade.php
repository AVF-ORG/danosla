@extends('layouts.app')

@section('content')
<div class="px-4 py-4 md:px-4 md:py-4">
    @php
        $statusConfig = match($shipment->status) {
            'pending' => ['label' => 'En Attente', 'color' => 'bg-amber-50 text-amber-700 border-amber-100', 'icon' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z'],
            'active' => ['label' => 'En Cours', 'color' => 'bg-blue-50 text-blue-700 border-blue-100', 'icon' => 'M13 10V3L4 14h7v7l9-11h-7z'],
            'completed' => ['label' => 'Livré', 'color' => 'bg-green-50 text-green-700 border-green-100', 'icon' => 'M5 13l4 4L19 7'],
            'cancelled', 'canceled' => ['label' => 'Annulé', 'color' => 'bg-red-50 text-red-700 border-red-100', 'icon' => 'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z'],
            default => ['label' => ucfirst($shipment->status), 'color' => 'bg-gray-50 text-gray-700 border-gray-100', 'icon' => 'M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z'],
        };
    @endphp
    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-4">
                <a href="{{ route('transport-firm-bid.index') }}" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                </a>
                <div class="flex items-center gap-2">
                    <span class="text-xs font-medium text-gray-400">{{ $shipment->created_at->format('M d, Y') }}</span>
                    <span class="text-xs text-gray-300">•</span>
                    <span class="text-xs font-medium text-gray-400">EXP-{{ str_pad($shipment->id, 6, '0', STR_PAD_LEFT) }}</span>
                </div>
            </div>

            <!-- Management Toolbar (Actions, Status, Price) -->
            <div class="flex items-center gap-6">
                <!-- Actions -->
                <div class="flex items-center gap-2 border-r border-gray-100 dark:border-gray-800 pr-6 mr-3" x-data="{ openDeleteModal: false }">
                    <a href="{{ route('transport-firm-bid.edit', $shipment) }}" class="inline-flex items-center gap-2 px-4 py-2 bg-gray-900 dark:bg-white text-white dark:text-gray-900 rounded-xl text-xs font-black uppercase tracking-widest hover:bg-brand-600 hover:text-white transition-all shadow-sm">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" /></svg>
                        Edit
                    </a>
                    <button @click="openDeleteModal = true" class="inline-flex items-center gap-2 px-4 py-2 border border-red-100 dark:border-red-900/30 text-red-600 dark:text-red-400 rounded-xl text-xs font-black uppercase tracking-widest hover:bg-red-50 transition-all">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                        Delete
                    </button>

                    <!-- Delete Modal -->
                    <x-ui.modal model="openDeleteModal" title="Delete Shipment" maxWidth="max-w-md">
                        <div class="p-8 text-center">
                            <div class="w-16 h-16 bg-red-50 rounded-full flex items-center justify-center mx-auto mb-4">
                                <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                            </div>
                            <h3 class="text-lg font-black text-gray-900 dark:text-white mb-2 uppercase">Permanently Delete?</h3>
                            <p class="text-sm text-gray-500 mb-6">This action cannot be undone. All lots associated with this shipment will be removed.</p>
                            <div class="flex gap-3">
                                <button @click="openDeleteModal = false" class="flex-1 py-3 bg-gray-100 text-gray-700 rounded-xl font-black uppercase text-[10px] tracking-widest">Cancel</button>
                                <form action="{{ route('transport-firm-bid.destroy', $shipment) }}" method="POST" class="flex-1">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="w-full py-3 bg-red-600 text-white rounded-xl font-black uppercase text-[10px] tracking-widest shadow-lg shadow-red-500/30">Delete</button>
                                </form>
                            </div>
                        </div>
                    </x-ui.modal>
                </div>

                <!-- Status & Price Badges -->
                <div class="flex items-center gap-3">
                    <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full border {{ $statusConfig['color'] }} shadow-sm">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="{{ $statusConfig['icon'] }}" /></svg>
                        <span class="text-[10px] font-black uppercase tracking-widest">{{ $statusConfig['label'] }}</span>
                    </div>
                    
                    @if($shipment->delivery_price)
                    <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full border border-gray-100 dark:border-gray-800 bg-gray-50/50 dark:bg-gray-800/50 shadow-sm">
                        <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        <span class="text-[10px] font-black uppercase tracking-widest text-gray-600 dark:text-gray-400">
                            {{ number_format($shipment->delivery_price, 2, ',', ' ') }} €
                        </span>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>


    <!-- Main Shipment Card (Merged Recap & Details) -->
    <div class="bg-white dark:bg-gray-900 rounded-3xl border border-gray-200 dark:border-gray-800 shadow-xl shadow-gray-200/40 dark:shadow-none overflow-hidden mb-8">
        
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
                    <p class="text-xs font-bold text-gray-600 dark:text-gray-400">{{ $shipment->latest_pickup_date ? $shipment->latest_pickup_date->translatedFormat('M d, Y') : '-' }}</p>
                </div>

                <!-- Path Visual -->
                <div class="flex flex-col items-center gap-2 px-4 flex-none group">
                    <div class="flex items-center gap-3">
                        <div class="w-2.5 h-2.5 rounded-full border-2 border-brand-500 shadow-[0_0_8px_rgba(var(--brand-600-rgb),0.3)]"></div>
                        <div class="flex gap-1 items-center">
                            @for($i=0; $i<6; $i++) <div class="w-1 h-0.5 bg-gray-200 dark:bg-gray-700 rounded-full"></div> @endfor
                            <div class="px-2 py-1 rounded-lg bg-gray-50 dark:bg-gray-800 border border-gray-100 dark:border-gray-700 group-hover:scale-110 transition-transform cursor-help">
                                <svg class="w-4 h-4 text-brand-500 transform rotate-90" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M13 10V3L4 14h7v7l9-11h-7z" /></svg>
                            </div>
                            @for($i=0; $i<6; $i++) <div class="w-1 h-0.5 bg-gray-200 dark:bg-gray-700 rounded-full"></div> @endfor
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
                    <p class="text-xs font-bold text-gray-600 dark:text-gray-400">{{ $shipment->latest_delivery_date ? $shipment->latest_delivery_date->translatedFormat('M d, Y') : '-' }}</p>
                </div>
            </div>
        </div>

        <!-- Horizontal Separator -->
        <div class="h-px bg-gradient-to-r from-transparent via-gray-100 to-transparent dark:via-gray-800"></div>

        <!-- Detailed Locations Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 divide-y md:divide-y-0 md:divide-x divide-gray-100 dark:divide-gray-800">
            <div class="p-8 group hover:bg-gray-50/50 dark:hover:bg-gray-800/20 transition-colors">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-10 h-10 rounded-xl bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400 flex items-center justify-center shadow-sm">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" /></svg>
                    </div>
                    <h3 class="text-sm font-black text-gray-900 dark:text-white uppercase tracking-tight">Pickup Location</h3>
                </div>
                <p class="text-sm text-gray-700 dark:text-gray-300 leading-relaxed font-medium mb-2">{{ $shipment->pickup_address }}</p>
            </div>

            <div class="p-8 group hover:bg-gray-50/50 dark:hover:bg-gray-800/20 transition-colors">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-10 h-10 rounded-xl bg-green-50 dark:bg-green-900/20 text-green-600 dark:text-green-400 flex items-center justify-center shadow-sm">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" /></svg>
                    </div>
                    <h3 class="text-sm font-black text-gray-900 dark:text-white uppercase tracking-tight">Delivery Location</h3>
                </div>
                <p class="text-sm text-gray-700 dark:text-gray-300 leading-relaxed font-medium mb-2">{{ $shipment->delivery_address }}</p>
            </div>
        </div>

        <!-- Notes & Information Section -->
        @if($shipment->description || $shipment->comment)
        <div class="bg-gray-50/50 dark:bg-gray-800/20 border-t border-gray-100 dark:border-gray-800 p-8">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                @if($shipment->description)
                <div class="space-y-3">
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                        <h4 class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Description des Marchandises</h4>
                    </div>
                    <p class="text-sm text-gray-600 dark:text-gray-400 leading-relaxed bg-white dark:bg-gray-900/50 p-4 rounded-xl border border-gray-100 dark:border-gray-800/50 italic">
                        "{{ $shipment->description }}"
                    </p>
                </div>
                @endif

                @if($shipment->comment)
                <div class="space-y-3">
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" /></svg>
                        <h4 class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Commentaires Additionnels</h4>
                    </div>
                    <p class="text-sm text-gray-600 dark:text-gray-400 leading-relaxed bg-white dark:bg-gray-900/50 p-4 rounded-xl border border-gray-100 dark:border-gray-800/50 italic">
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
                'isDangerous' => ['label' => 'Matières Dangereuses', 'descKey' => 'dangerousGoodsDescription', 'icon' => 'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z', 'color' => 'red'],
                'isUrgent' => ['label' => 'Transport Urgent', 'descKey' => 'urgentDescription', 'icon' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z', 'color' => 'orange'],
                'hasInsuranceOption' => ['label' => 'Assurance Requise', 'descKey' => 'insuranceDescription', 'icon' => 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z', 'color' => 'blue'],
                'hasSpecialHandlingInstructions' => ['label' => 'Manipulation Spéciale', 'descKey' => 'specialHandlingDescription', 'icon' => 'M7 11.5V14m0-2.5v-6a1.5 1.5 0 113 0V12m-3-0.5a3 3 0 00-6 0v2.5m6-2.5c.348 0 .678.046 1 .132V3.5a1.5 1.5 0 013 0V12m-3-0.5a3 3 0 00-6 0v2.5m6-2.5c.348 0 .678.046 1 .132V1.5a1.5 1.5 0 013 0V12', 'color' => 'purple'],
                'needsTemperatureControlledTransport' => ['label' => 'Température Contrôlée', 'descKey' => 'temperatureControlledDescription', 'icon' => 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z', 'color' => 'cyan'],
                'hasFragileGoods' => ['label' => 'Marchandises Fragiles', 'descKey' => 'fragileGoodsDescription', 'icon' => 'M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z', 'color' => 'rose'],
                'hasAdditionalRequirements' => ['label' => 'Exigences Additionnelles', 'descKey' => 'additionalRequirementsDescription', 'icon' => 'M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4', 'color' => 'slate'],
            ];

            $activeRequirements = [];
            if(!empty($shipment->requirements)) {
                foreach($requirementsMap as $key => $config) {
                    if(!empty($shipment->requirements[$key])) {
                        $activeRequirements[] = [
                            'label' => $config['label'],
                            'description' => $shipment->requirements[$config['descKey']] ?? null,
                            'icon' => $config['icon'],
                            'color' => $config['color']
                        ];
                    }
                }
            }
        @endphp

        @if(count($activeRequirements) > 0)
        <div class="border-t border-gray-100 dark:border-gray-800 p-8">
            <h4 class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-6">Conditions & Exigences Spéciales</h4>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($activeRequirements as $req)
                <div class="flex items-start gap-4 p-4 rounded-2xl bg-{{ $req['color'] }}-50/50 dark:bg-{{ $req['color'] }}-900/10 border border-{{ $req['color'] }}-100/50 dark:border-{{ $req['color'] }}-800/30">
                    <div class="w-8 h-8 rounded-lg bg-{{ $req['color'] }}-100 dark:bg-{{ $req['color'] }}-900/30 text-{{ $req['color'] }}-600 dark:text-{{ $req['color'] }}-400 flex items-center justify-center flex-none">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="{{ $req['icon'] }}" /></svg>
                    </div>
                    <div>
                        <p class="text-[11px] font-black text-{{ $req['color'] }}-700 dark:text-{{ $req['color'] }}-300 uppercase tracking-tight mb-1">{{ $req['label'] }}</p>
                        <p class="text-xs text-{{ $req['color'] }}-600/80 dark:text-{{ $req['color'] }}-400/80 leading-relaxed font-medium">
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
                <span class="text-[10px] font-black bg-gray-100 dark:bg-gray-800 text-gray-500 px-3 py-1 rounded-full uppercase tracking-widest">{{ $shipment->lots->count() }} Lot(s)</span>
                <span class="text-[10px] font-black bg-brand-50 dark:bg-brand-900/30 text-brand-600 dark:text-brand-400 px-3 py-1 rounded-full uppercase tracking-widest">{{ number_format($shipment->total_weight, 2) }} kg total</span>
            </div>
        </div>
        
        <div class="bg-white dark:bg-gray-900 rounded-3xl border border-gray-100 dark:border-gray-800 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-50/50 dark:bg-gray-800/50 border-b border-gray-100 dark:border-gray-800">
                            <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest w-16">#</th>
                            <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Type & Description</th>
                            <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center">Qty</th>
                            <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Dimensions</th>
                            <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest text-right">Total Weight</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50 dark:divide-gray-800">
                        @foreach($shipment->lots as $index => $lot)
                        <tr class="group hover:bg-gray-50/30 dark:hover:bg-gray-800/30 transition-colors">
                            <td class="px-6 py-5">
                                <span class="w-8 h-8 rounded-lg bg-gray-50 dark:bg-gray-800 flex items-center justify-center text-[10px] font-black text-gray-400 group-hover:bg-brand-50 group-hover:text-brand-600 transition-colors">
                                    {{ $index + 1 }}
                                </span>
                            </td>
                            <td class="px-6 py-5">
                                <p class="text-sm font-black text-gray-900 dark:text-white uppercase tracking-tight mb-1">{{ str_replace('_', ' ', $lot->type) }}</p>
                                <div class="flex gap-2">
                                    @if($lot->is_stackable)
                                        <span class="px-1.5 py-0.5 rounded bg-green-50 dark:bg-green-900/20 text-green-600 dark:text-green-400 text-[9px] font-black uppercase">Stackable</span>
                                    @endif
                                    @if($lot->is_rolling)
                                        <span class="px-1.5 py-0.5 rounded bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400 text-[9px] font-black uppercase">Rolling</span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-5 text-center">
                                <span class="text-sm font-black text-gray-900 dark:text-white bg-gray-50 dark:bg-gray-800 px-2 py-1 rounded-md">x{{ $lot->quantity }}</span>
                            </td>
                            <td class="px-6 py-5">
                                <div class="flex items-center gap-1.5 text-xs font-bold text-gray-600 dark:text-gray-400">
                                    <span>{{ $lot->length }}m</span>
                                    <span class="text-gray-300">×</span>
                                    <span>{{ $lot->width }}m</span>
                                    <span class="text-gray-300">×</span>
                                    <span>{{ $lot->height }}m</span>
                                </div>
                            </td>
                            <td class="px-6 py-5 text-right">
                                <p class="text-sm font-black text-gray-900 dark:text-white">{{ number_format($lot->weight * $lot->quantity, 2) }} <span class="text-[10px] text-gray-400 ml-0.5">kg</span></p>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Negotiation & Discussion Section -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 mt-12">
        <!-- Left: Proposition Details (4 cols) -->
        <div class="lg:col-span-4 space-y-6" x-data="{ openBidModal: false }">
            <h2 class="text-sm font-black uppercase tracking-[0.2em] text-gray-400 px-1">Votre Proposition</h2>
            
            @if($myBid)
            <div class="bg-white dark:bg-gray-900 rounded-3xl border border-gray-100 dark:border-gray-800 shadow-xl shadow-gray-200/20 p-8 relative overflow-hidden group">
                <!-- Status Background Decor -->
                <div class="absolute -right-4 -top-4 w-24 h-24 bg-brand-500/5 rounded-full blur-2xl group-hover:bg-brand-500/10 transition-colors"></div>
                
                <div class="relative">
                    <div class="flex items-center justify-between mb-8">
                        <div class="px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-widest 
                            {{ $myBid->status === 'accepted' ? 'bg-green-50 text-green-600 border border-green-100' : 'bg-amber-50 text-amber-600 border border-amber-100' }}">
                            {{ $myBid->status === 'accepted' ? 'Proposition Acceptée' : 'Proposition en cours' }}
                        </div>
                        <span class="text-[10px] font-bold text-gray-400">Màj {{ $myBid->updated_at->diffForHumans() }}</span>
                    </div>

                    <div class="space-y-6">
                        <div>
                            <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Prix Proposé</p>
                            <h3 class="text-3xl font-black text-gray-900 dark:text-white">
                                {{ number_format($myBid->price, 2, ',', ' ') }} <span class="text-lg font-bold text-gray-300">€</span>
                            </h3>
                        </div>

                        <div class="grid grid-cols-1 gap-4">
                            <div class="p-4 rounded-2xl bg-gray-50 dark:bg-gray-800/50 border border-gray-100 dark:border-gray-800">
                                <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2">Collecte Proposée</p>
                                <div class="flex items-center gap-2">
                                    <svg class="w-4 h-4 text-brand-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                                    <span class="text-xs font-bold text-gray-700 dark:text-gray-300">
                                        {{ $myBid->latest_pickup_date->format('d/m/Y') }} à {{ \Carbon\Carbon::parse($myBid->latest_pickup_time)->format('H:i') }}
                                    </span>
                                </div>
                            </div>
                            <div class="p-4 rounded-2xl bg-gray-50 dark:bg-gray-800/50 border border-gray-100 dark:border-gray-800">
                                <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2">Livraison Proposée</p>
                                <div class="flex items-center gap-2">
                                    <svg class="w-4 h-4 text-brand-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                                    <span class="text-xs font-bold text-gray-700 dark:text-gray-300">
                                        {{ $myBid->latest_delivery_date->format('d/m/Y') }} à {{ \Carbon\Carbon::parse($myBid->latest_delivery_time)->format('H:i') }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <button @click="openBidModal = true" class="w-full py-4 bg-gray-900 dark:bg-white text-white dark:text-gray-900 rounded-2xl font-black uppercase text-[11px] tracking-[0.2em] hover:bg-brand-600 hover:text-white transition-all shadow-lg shadow-gray-200 dark:shadow-none">
                            Modifier l'offre
                        </button>
                    </div>
                </div>
            </div>
            @else
            <div class="bg-gradient-to-br from-brand-500 to-brand-600 rounded-3xl p-8 text-white shadow-xl shadow-brand-500/20">
                <h3 class="text-xl font-black mb-4">Aucune offre soumise</h3>
                <p class="text-sm text-brand-50 font-medium mb-6 leading-relaxed">Proposez vos tarifs et vos dates pour commencer la négociation avec le client.</p>
                <button @click="openBidModal = true" class="w-full py-4 bg-white text-brand-600 rounded-2xl font-black uppercase text-[11px] tracking-[0.2em] hover:bg-brand-50 transition-all">
                    Faire une proposition
                </button>
            </div>
            @endif

            <!-- Proposition Modal -->
            <x-ui.modal model="openBidModal" title="{{ $myBid ? 'Modifier votre offre' : 'Faire une proposition' }}" maxWidth="max-w-xl">
                <form action="{{ route('transport-firm-bid.store-bid', $shipment) }}" method="POST" class="p-8 space-y-6">
                    @csrf
                    <div class="grid grid-cols-1 gap-6">
                        <!-- Price -->
                        <div>
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Votre Prix (€)</label>
                            <input type="number" name="price" step="0.01" value="{{ $myBid ? $myBid->price : $shipment->delivery_price }}" class="w-full bg-gray-50 border-gray-100 rounded-xl px-4 py-3 text-sm font-bold focus:ring-brand-500">
                        </div>

                        <!-- Pickup -->
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Date Collecte</label>
                                <input type="date" name="latest_pickup_date" value="{{ $myBid ? $myBid->latest_pickup_date->format('Y-m-d') : $shipment->latest_pickup_date->format('Y-m-d') }}" class="w-full bg-gray-50 border-gray-100 rounded-xl px-4 py-3 text-sm font-bold focus:ring-brand-500">
                            </div>
                            <div>
                                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Heure Collecte</label>
                                <input type="time" name="latest_pickup_time" value="{{ $myBid ? $myBid->latest_pickup_time : $shipment->latest_pickup_time }}" class="w-full bg-gray-50 border-gray-100 rounded-xl px-4 py-3 text-sm font-bold focus:ring-brand-500">
                            </div>
                        </div>

                        <!-- Delivery -->
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Date Livraison</label>
                                <input type="date" name="latest_delivery_date" value="{{ $myBid ? $myBid->latest_delivery_date->format('Y-m-d') : $shipment->latest_delivery_date->format('Y-m-d') }}" class="w-full bg-gray-50 border-gray-100 rounded-xl px-4 py-3 text-sm font-bold focus:ring-brand-500">
                            </div>
                            <div>
                                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Heure Livraison</label>
                                <input type="time" name="latest_delivery_time" value="{{ $myBid ? $myBid->latest_delivery_time : $shipment->latest_delivery_time }}" class="w-full bg-gray-50 border-gray-100 rounded-xl px-4 py-3 text-sm font-bold focus:ring-brand-500">
                            </div>
                        </div>

                        <!-- Message -->
                        <div>
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Message (Optionnel)</label>
                            <textarea name="message" rows="3" class="w-full bg-gray-50 border-gray-100 rounded-xl px-4 py-3 text-sm font-medium focus:ring-brand-500" placeholder="Ajoutez un commentaire à votre offre..."></textarea>
                        </div>
                    </div>

                    <div class="flex gap-3 pt-6">
                        <button type="button" @click="openBidModal = false" class="flex-1 py-4 bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300 rounded-xl font-black uppercase text-[10px] tracking-widest hover:bg-gray-200 transition-all">Annuler</button>
                        <button type="submit" class="flex-1 py-4 bg-brand-500 text-white rounded-xl font-black uppercase text-[10px] tracking-widest shadow-lg shadow-brand-500/30 hover:bg-brand-600 transition-all">Envoyer l'offre</button>
                    </div>
                </form>
            </x-ui.modal>
        </div>

        <!-- Right: Discussion Thread (8 cols) -->
        <div class="lg:col-span-8 space-y-6">
            <h2 class="text-sm font-black uppercase tracking-[0.2em] text-gray-400 px-1">Discussion avec le client</h2>
            
            <div class="bg-white dark:bg-gray-900 rounded-3xl border border-gray-100 dark:border-gray-800 shadow-xl shadow-gray-200/20 flex flex-col min-h-[600px]">
                <!-- Chat Header -->
                <div class="p-6 border-b border-gray-50 dark:border-gray-800 flex items-center justify-between bg-gray-50/30 dark:bg-gray-800/20">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-brand-100 dark:bg-brand-900/30 text-brand-600 dark:text-brand-400 flex items-center justify-center font-black text-sm">
                            C
                        </div>
                        <div>
                            <p class="text-xs font-black text-gray-900 dark:text-white uppercase tracking-tight">Client</p>
                            <p class="text-[10px] font-bold text-green-500 uppercase tracking-widest flex items-center gap-1.5">
                                <span class="w-1.5 h-1.5 rounded-full bg-green-500 animate-pulse"></span> Client en ligne
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Messages area -->
                <div class="flex-1 p-8 space-y-8 overflow-y-auto max-h-[500px]">
                    @if($myBid && $myBid->messages->count() > 0)
                        @foreach($myBid->messages as $message)
                            @php $isMe = $message->user_id === auth()->id(); @endphp
                            <div class="flex {{ $isMe ? 'justify-end' : 'justify-start' }} group">
                                <div class="max-w-[80%] space-y-1.5">
                                    <div class="flex items-center gap-3 px-1 {{ $isMe ? 'flex-row-reverse' : '' }}">
                                        <span class="text-[9px] font-black text-gray-400 uppercase tracking-widest">{{ $isMe ? 'Vous' : 'Client' }}</span>
                                        <span class="text-[9px] text-gray-300 font-bold">{{ $message->created_at->format('H:i') }}</span>
                                    </div>
                                    <div class="px-5 py-4 rounded-3xl shadow-sm border 
                                        {{ $isMe 
                                            ? 'bg-brand-500 text-white border-brand-400 rounded-tr-none' 
                                            : 'bg-gray-50 dark:bg-gray-800 text-gray-700 dark:text-gray-300 border-gray-100 dark:border-gray-700 rounded-tl-none' }}">
                                        <p class="text-sm font-medium leading-relaxed">{{ $message->message }}</p>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="h-full flex flex-col items-center justify-center text-center opacity-40">
                            <svg class="w-16 h-16 text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" /></svg>
                            <p class="text-sm font-bold text-gray-400 uppercase tracking-widest">Aucun message pour le moment</p>
                        </div>
                    @endif
                </div>

                <!-- Input area -->
                <div class="p-6 bg-gray-50/50 dark:bg-gray-800/20 border-t border-gray-50 dark:border-gray-800">
                    @if($myBid)
                        <form action="{{ route('transport-firm-bid.store-message', $myBid) }}" method="POST" class="flex items-center gap-4">
                            @csrf
                            <div class="relative flex-1">
                                <input type="text" name="message" required placeholder="Écrire un message..." class="w-full bg-white dark:bg-gray-900 border-gray-100 dark:border-gray-800 rounded-2xl px-6 py-4 text-sm font-medium focus:ring-brand-500 focus:border-brand-500 transition-all pr-12 shadow-sm">
                                <button type="button" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-300 hover:text-brand-500 transition-colors p-2">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" /></svg>
                                </button>
                            </div>
                            <button type="submit" class="w-14 h-14 bg-brand-500 text-white rounded-2xl flex items-center justify-center hover:bg-brand-600 shadow-lg shadow-brand-500/30 transition-all flex-none group">
                                <svg class="w-6 h-6 transform group-hover:translate-x-0.5 group-hover:-translate-y-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" /></svg>
                            </button>
                        </form>
                    @else
                        <div class="flex items-center justify-center p-4 bg-amber-50 dark:bg-amber-900/10 border border-amber-100 dark:border-amber-800 rounded-2xl">
                            <p class="text-xs font-bold text-amber-600 dark:text-amber-400 uppercase tracking-widest">Veuillez faire une proposition pour commencer la discussion.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection