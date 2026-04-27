<div>
    <div class="mx-auto space-y-6">

        @if (!$isSubmitted)
            <div class="space-y-6">

                <!-- MERCHANDISES -->
                <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03] animate-fadeIn">
                    <div class="px-6 py-5 border-b border-gray-100 dark:border-gray-800 flex flex-col md:flex-row md:items-center justify-between gap-4">
                        <div>
                            <h2 class="text-base font-medium text-gray-800 dark:text-white/90">
                                {{ $editingLotIndex !== null ? 'Modifier le lot ' . ($editingLotIndex + 1) : 'Ajouter des marchandises' }}
                            </h2>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Renseignez les détails de chaque lot de votre expédition.</p>
                        </div>

                        @if ($editingLotIndex !== null)
                            <button wire:click="cancelEdit"
                                class="inline-flex items-center justify-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 hover:text-gray-900 transition-all focus:outline-none focus:ring-2 focus:ring-gray-200 shadow-sm">
                                Annuler la modification
                            </button>
                        @endif
                    </div>

                    <div class="p-4 sm:p-6">

                        <!-- LOT EDITOR -->
                        <div class="space-y-6">
                            <div class="bg-gray-50/50 dark:bg-white/[0.02] rounded-xl p-6 border border-gray-100 dark:border-gray-800 relative overflow-hidden">
                                <div class="flex items-center gap-3 mb-6">
                                    <span class="inline-flex items-center justify-center h-8 px-3 rounded-lg bg-blue-50 dark:bg-blue-500/10 text-blue-700 dark:text-blue-400 border border-blue-100 dark:border-blue-500/20 text-xs font-bold uppercase tracking-wider">
                                        Lot {{ $editingLotIndex !== null ? $editingLotIndex + 1 : count($lots) + 1 }}
                                    </span>
                                    <span class="text-sm text-gray-600 dark:text-gray-400">
                                        {{ $editingLotIndex !== null ? 'Mise à jour des détails' : 'Configurez votre nouvelle unité' }}
                                    </span>
                                </div>

                                <!-- MAIN FIELDS -->
                                <div class="grid grid-cols-1 md:grid-cols-5 gap-6">
                                    <!-- Type -->
                                    <div class="md:col-span-1">
                                        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Type de conditionnement *</label>
                                        <div x-data="{ isOptionSelected: @entangle('type').live ? true : false }" class="relative z-20">
                                            <select wire:model.live="type"
                                                class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 pr-11 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30"
                                                :class="isOptionSelected ? 'text-gray-800 dark:text-white/90' : 'text-gray-400'"
                                                @change="isOptionSelected = true">
                                                <option value="" class="text-gray-400">Choisir un type *</option>
                                                <option value="colis">Colis (devis personnalisé)</option>
                                                <option value="palette_standard">Palette(s) standard</option>
                                                <option value="palette_non_standard">Palette(s) hors standard</option>
                                                <option value="caisse">Caisse bois ou métal</option>
                                                <option value="oeuvre_art">Oeuvre / objet d'art</option>
                                                <option value="hors_gabarit">Hors gabarit</option>
                                                <option value="conteneur_groupage">Conteneur partiel (groupage)</option>
                                                <option value="conteneur_complet">Conteneur(s) complet</option>
                                                <option value="vehicule">Véhicule(s)</option>
                                            </select>
                                            <span class="pointer-events-none absolute top-1/2 right-4 z-30 -translate-y-1/2 text-gray-500 dark:text-gray-400">
                                                <svg class="stroke-current" width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M4.79175 7.396L10.0001 12.6043L15.2084 7.396" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                                </svg>
                                            </span>
                                        </div>
                                    </div>

                                    @if ($type === 'palette_standard')
                                        <div class="md:col-span-2">
                                            <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Type de palette *</label>
                                            <div x-data="{ isOptionSelected: @entangle('palette_type').live ? true : false }" class="relative z-20">
                                                <select wire:model.live="palette_type"
                                                    class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 pr-11 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30"
                                                    :class="isOptionSelected ? 'text-gray-800 dark:text-white/90' : 'text-gray-400'"
                                                    @change="isOptionSelected = true">
                                                    @foreach (array_keys($palette_dimensions) as $p_type)
                                                        <option value="{{ $p_type }}">{{ str_replace('_', 'x', $p_type) }} cm</option>
                                                    @endforeach
                                                </select>
                                                <span class="pointer-events-none absolute top-1/2 right-4 z-30 -translate-y-1/2 text-gray-500 dark:text-gray-400">
                                                    <svg class="stroke-current" width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                        <path d="M4.79175 7.396L10.0001 12.6043L15.2084 7.396" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                                    </svg>
                                                </span>
                                            </div>
                                        </div>
                                    @elseif($type === 'conteneur_complet')
                                        <div class="md:col-span-3">
                                            <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Type / Capacité / Dimensions *</label>
                                            <div x-data="{ isOptionSelected: @entangle('container_type').live ? true : false }" class="relative z-20">
                                                <select wire:model.live="container_type"
                                                    class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 pr-11 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30"
                                                    :class="isOptionSelected ? 'text-gray-800 dark:text-white/90' : 'text-gray-400'"
                                                    @change="isOptionSelected = true">
                                                    @foreach ($container_options as $c_type => $c_label)
                                                        <option value="{{ $c_type }}">{{ $c_label }}</option>
                                                    @endforeach
                                                </select>
                                                <span class="pointer-events-none absolute top-1/2 right-4 z-30 -translate-y-1/2 text-gray-500 dark:text-gray-400">
                                                    <svg class="stroke-current" width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                        <path d="M4.79175 7.396L10.0001 12.6043L15.2084 7.396" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                                    </svg>
                                                </span>
                                            </div>
                                        </div>
                                    @elseif($type === 'vehicule')
                                        <div class="md:col-span-2">
                                            <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Marque *</label>
                                            <input type="text" wire:model.live="brand" placeholder="Peugeot, Honda..."
                                                class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border @error('brand') border-red-500 @else border-gray-300 @enderror bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30" />
                                            @error('brand')
                                                <span class="text-xs text-red-500 mt-1 block font-bold">{{ $message }}</span>
                                            @enderror
                                        </div>

                                        <div>
                                            <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Modèle *</label>
                                            <input type="text" wire:model.live="model" placeholder="308, CBR1000RR"
                                                class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border @error('model') border-red-500 @else border-gray-300 @enderror bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30" />
                                            @error('model')
                                                <span class="text-xs text-red-500 mt-1 block font-bold">{{ $message }}</span>
                                            @enderror
                                        </div>

                                <div>
                                    <label class="block text-sm font-bold text-gray-800 mb-2">Weight *</label>
                                    <div x-data="{ isOptionSelected: @entangle('vehicle_weight').live ? true : false }" class="relative z-20">
                                        <select wire:model.live="vehicle_weight"
                                            class="w-full h-11 bg-white border border-gray-300 rounded-xl px-4 appearance-none text-sm outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all shadow-sm"
                                            :class="isOptionSelected ? 'text-gray-900 font-semibold' : 'text-gray-400'"
                                            @change="isOptionSelected = true">
                                            <option value="1t">1t</option>
                                            <option value="1.5t">1.5t</option>
                                            <option value="2t">2t</option>
                                            <option value="3t">3t</option>
                                        </select>
                                        <span class="pointer-events-none absolute top-1/2 right-4 z-30 -translate-y-1/2 text-gray-500">
                                            <svg class="stroke-current" width="18" height="18" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M4.79175 7.396L10.0001 12.6043L15.2084 7.396" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" />
                                            </svg>
                                        </span>
                                    </div>
                                    @error('vehicle_weight')
                                        <span class="text-xs text-red-500 mt-1 block font-bold">{{ $message }}</span>
                                    @enderror
                                </div>
                            @else
                                    @endif

                                    @if ($type !== 'vehicule')
                                        <div>
                                            <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Longueur (cm) *</label>
                                            <input type="number" wire:model.live="length" placeholder="L"
                                                class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border @error('length') border-red-500 @else border-gray-300 @enderror bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30" />
                                            @error('length')
                                                <span class="text-xs text-red-500 mt-1 block font-bold">{{ $message }}</span>
                                            @enderror
                                        </div>

                                        <div>
                                            <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Largeur (cm) *</label>
                                            <input type="number" wire:model.live="width" placeholder="W"
                                                class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border @error('width') border-red-500 @else border-gray-300 @enderror bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30" />
                                            @error('width')
                                                <span class="text-xs text-red-500 mt-1 block font-bold">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    @endif

                                    @if ($type !== 'vehicule')
                                        @if ($type !== 'conteneur_complet')
                                            <div>
                                                <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Hauteur (cm) *</label>
                                                <input type="number" wire:model.live="height" placeholder="H"
                                                    class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border @error('height') border-red-500 @else border-gray-300 @enderror bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30" />
                                                @error('height')
                                                    <span class="text-xs text-red-500 mt-1 block font-bold">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        @endif

                                        <div>
                                            <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Quantité *</label>
                                            <input type="number" wire:model.live="quantity" min="1"
                                                class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border @error('quantity') border-red-500 @else border-gray-300 @enderror bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30" />
                                            @error('quantity')
                                                <span class="text-xs text-red-500 mt-1 block font-bold">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    @endif
                                </div>

                                <!-- SECONDARY FIELDS -->
                                <div class="mt-8">
                                    @if ($type !== 'vehicule')
                                        <div class="grid grid-cols-1 xl:grid-cols-12 gap-6 items-start">
                                            <div class="xl:col-span-4 space-y-6">
                                                <div>
                                                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Poids unitaire (kg) *</label>
                                                    <div class="relative">
                                                        <input type="number" wire:model.live="weight" step="0.01"
                                                            class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border @error('weight') border-red-500 @else border-gray-300 @enderror bg-transparent pl-4 pr-12 text-sm text-gray-800 font-medium placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30" />
                                                        <div class="absolute inset-y-0 right-0 flex items-center pr-4 pointer-events-none">
                                                            <span class="text-gray-400 text-xs font-bold uppercase">kg</span>
                                                        </div>
                                                    </div>
                                                    @error('weight')
                                                        <span class="text-xs text-red-500 mt-1 block font-bold">{{ $message }}</span>
                                                    @enderror
                                                </div>

                                                <div class="flex items-center gap-3">
                                                    <label class="relative inline-flex items-center cursor-pointer">
                                                        <input type="checkbox" wire:model.live="is_stackable" class="sr-only peer">
                                                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-brand-500/10 dark:peer-focus:ring-brand-500/20 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-brand-500"></div>
                                                        <span class="ml-3 text-sm font-medium text-gray-700 dark:text-gray-300">Superposable</span>
                                                    </label>
                                                </div>
                                            </div>

                                            <div class="xl:col-span-8">
                                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                                    <!-- Summary Cards -->
                                                    <div class="p-4 rounded-xl border border-gray-100 dark:border-gray-800 bg-white dark:bg-white/[0.02] shadow-sm">
                                                        <div class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Poids total</div>
                                                        <div class="flex items-baseline gap-1">
                                                            <div class="text-lg font-bold text-gray-900 dark:text-white">
                                                                {{ number_format((float) ($weight ?: 0) * (int) ($quantity ?: 1), 2, '.', '') }}
                                                            </div>
                                                            <div class="text-xs font-bold text-gray-400 uppercase">kg</div>
                                                        </div>
                                                    </div>

                                                    <div class="p-4 rounded-xl border border-gray-100 dark:border-gray-800 bg-white dark:bg-white/[0.02] shadow-sm">
                                                        <div class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Volume unitaire</div>
                                                        <div class="flex items-baseline gap-1">
                                                            <div class="text-lg font-bold text-gray-900 dark:text-white">
                                                                {{ number_format($this->unit_volume, 4, '.', '') }}
                                                            </div>
                                                            <div class="text-xs font-bold text-gray-400">m³</div>
                                                        </div>
                                                    </div>

                                                    <div class="p-4 rounded-xl border border-brand-100 dark:border-brand-500/20 bg-brand-50/50 dark:bg-brand-500/5 shadow-sm">
                                                        <div class="text-xs font-bold text-brand-600 dark:text-brand-400 uppercase tracking-wider mb-1">Volume total</div>
                                                        <div class="flex items-baseline gap-1">
                                                            <div class="text-lg font-bold text-brand-900 dark:text-brand-200">
                                                                {{ number_format($this->current_lot_volume, 4, '.', '') }}
                                                            </div>
                                                            <div class="text-xs font-bold text-brand-600/60">m³</div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @else
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                            <div>
                                                <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Quantité *</label>
                                                <input type="number" wire:model.live="quantity" min="1"
                                                    class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border @error('quantity') border-red-500 @else border-gray-300 @enderror bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30" />
                                                @error('quantity')
                                                    <span class="text-xs text-red-500 mt-1 block font-bold">{{ $message }}</span>
                                                @enderror
                                            </div>

                                            <div class="space-y-4">
                                                <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">État du véhicule</label>
                                                <div class="flex flex-col gap-3">
                                                    <label class="relative inline-flex items-center cursor-pointer">
                                                        <input type="checkbox" wire:model.live="is_rolling" class="sr-only peer">
                                                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-brand-500/10 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-brand-500"></div>
                                                        <span class="ml-3 text-sm font-medium text-gray-700 dark:text-gray-300">Véhicule roulant</span>
                                                    </label>
                                                    <label class="relative inline-flex items-center cursor-pointer">
                                                        <input type="checkbox" wire:model.live="is_stackable" class="sr-only peer">
                                                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-brand-500/10 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-brand-500"></div>
                                                        <span class="ml-3 text-sm font-medium text-gray-700 dark:text-gray-300">Superposable</span>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                </div>

                                <div class="mt-8 flex justify-end">
                                    <button wire:click="addLot"
                                        class="inline-flex items-center justify-center gap-2 px-6 py-3 bg-gray-900 dark:bg-brand-500 hover:bg-gray-800 dark:hover:bg-brand-600 text-white font-bold text-sm rounded-lg transition-all shadow-sm active:scale-[0.98]">
                                        {{ $editingLotIndex !== null ? 'Sauvegarder les modifications' : 'Ajouter ce lot' }}
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
                                        </svg>
                                    </button>
                                </div>
                            </div>

                            <!-- LOTS TABLE -->
                            @if (count($lots) > 0)
                                <div class="mt-8">
                                    <div class="flex items-center justify-between mb-4">
                                        <h3 class="text-sm font-bold text-gray-900 dark:text-white uppercase tracking-wider">Mes marchandises</h3>
                                        @error('lots')
                                            <span class="text-xs text-red-500 font-bold">Veuillez ajouter au moins un lot.</span>
                                        @enderror
                                    </div>

                                    <div class="overflow-hidden rounded-xl border border-gray-100 dark:border-gray-800">
                                        <div class="overflow-x-auto">
                                            <table class="w-full text-left text-sm text-gray-700 dark:text-gray-300">
                                                <thead class="bg-gray-50 dark:bg-white/[0.02] border-b border-gray-100 dark:border-gray-800 text-xs font-bold uppercase tracking-wider text-gray-500">
                                                    <tr>
                                                        <th class="px-4 py-3">Lot</th>
                                                        <th class="px-4 py-3">Type</th>
                                                        <th class="px-4 py-3">Qté</th>
                                                        <th class="px-4 py-3">Poids Unitaire</th>
                                                        <th class="px-4 py-3">Poids Total</th>
                                                        <th class="px-4 py-3 text-center">Superpo.</th>
                                                        <th class="px-4 py-3">Volume</th>
                                                        <th class="px-4 py-3 text-right">Actions</th>
                                                    </tr>
                                                </thead>
                                                <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                                                    @foreach ($lots as $index => $lot)
                                                        <tr class="hover:bg-gray-50 dark:hover:bg-white/[0.01] transition-colors">
                                                            <td class="px-4 py-3 font-bold text-gray-900 dark:text-white">{{ $index + 1 }}</td>
                                                            <td class="px-4 py-3">{{ $lot['type'] === 'colis' ? 'Colis' : str_replace('_', ' ', $lot['type']) }}</td>
                                                            <td class="px-4 py-3 font-bold">{{ $lot['quantity'] }}</td>
                                                            <td class="px-4 py-3">{{ number_format($lot['weight'], 2, ',', ' ') }} kg</td>
                                                            <td class="px-4 py-3 font-bold">{{ number_format($lot['weight'] * $lot['quantity'], 2, ',', ' ') }} kg</td>
                                                            <td class="px-4 py-3 text-center">
                                                                @if ($lot['is_stackable'] ?? true)
                                                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-green-50 dark:bg-green-500/10 text-green-600 dark:text-green-400 border border-green-100 dark:border-green-500/20">OUI</span>
                                                                @else
                                                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-gray-50 dark:bg-white/5 text-gray-400 border border-gray-100 dark:border-white/10">NON</span>
                                                                @endif
                                                            </td>
                                                            <td class="px-4 py-3 font-bold">{{ number_format($lot['volume'] ?? 0, 4, ',', ' ') }} m³</td>
                                                            <td class="px-4 py-3 text-right whitespace-nowrap">
                                                                <button wire:click="editLot({{ $index }})" class="text-brand-500 hover:text-brand-600 font-bold mr-3">Modifier</button>
                                                                <button wire:click="removeLot({{ $index }})" class="text-red-500 hover:text-red-600 font-bold">Supprimer</button>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>

                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-6">
                                        <div class="p-4 rounded-xl bg-gray-50 dark:bg-white/[0.02] border border-gray-100 dark:border-gray-800">
                                            <div class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Nombre de lots</div>
                                            <div class="text-xl font-bold text-gray-900 dark:text-white">{{ count($lots) }}</div>
                                        </div>
                                        <div class="p-4 rounded-xl bg-gray-50 dark:bg-white/[0.02] border border-gray-100 dark:border-gray-800">
                                            <div class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Poids total cumulé</div>
                                            <div class="text-xl font-bold text-gray-900 dark:text-white">{{ number_format($total_weight, 0, ',', ' ') }} <span class="text-sm font-bold opacity-50">kg</span></div>
                                        </div>
                                        <div class="p-4 rounded-xl bg-gray-50 dark:bg-white/[0.02] border border-gray-100 dark:border-gray-800">
                                            <div class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Volume total cumulé</div>
                                            <div class="text-xl font-bold text-gray-900 dark:text-white">{{ number_format($total_volume, 4, '.', '') }} <span class="text-sm font-bold opacity-50">m³</span></div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>

                    <!-- DETAILS -->
                    <div class="mt-8 pt-8 border-t border-gray-100 space-y-5">
                        <div class="max-w-sm">
                            <label class="block text-sm font-semibold text-gray-800 mb-2">
                                Total value excl. tax (optional)
                            </label>
                            <div class="relative">
                                <div
                                    class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-gray-500 font-bold">
                                    €
                                </div>
                                <input type="number" wire:model.live="totalValue"
                                    class="block w-full h-11 pl-8 pr-3 border border-gray-300 rounded-xl outline-none text-sm focus:ring-1 focus:ring-blue-500 focus:border-blue-500 text-gray-800 bg-white shadow-sm">
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-gray-800 mb-2">Goods description *</label>
                            <textarea wire:model.live="description" rows="4"
                                class="block w-full border border-gray-300 rounded-2xl focus:border-blue-500 focus:ring-1 focus:ring-blue-500 p-4 text-sm resize-y outline-none transition-colors shadow-sm"></textarea>
                            @error('description')
                                <span class="text-xs text-red-500 mt-1 block font-bold">{{ $message }}</span>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm mb-2">
                                <span class="font-bold text-gray-800">Comment</span>
                                <span class="text-[#e27357] font-semibold">Do not include your contact details
                                    here.</span>
                            </label>
                            <textarea wire:model.live="comment" rows="3"
                                class="block w-full border border-gray-300 rounded-2xl focus:border-blue-500 focus:ring-1 focus:ring-blue-500 p-4 text-sm resize-y outline-none transition-colors shadow-sm"></textarea>
                        </div>
                        </div>
                    </div>

                </div>

                <!-- TERMS AND CONDITIONS -->
                <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-7 animate-fadeIn">
                    <div class="mb-6">
                        <h2 class="text-lg font-bold text-gray-900 mt-1">Terms and Conditions</h2>
                        <p class="text-sm text-gray-500 mt-1">
                            Specify any transport conditions, handling constraints, or additional requirements.
                        </p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

                        <!-- Insurance -->
                        <div class="border border-gray-200 rounded-2xl p-5 bg-white transition-all">
                            <label class="flex items-start gap-4 cursor-pointer select-none">
                                <input type="checkbox" wire:model.live="hasInsuranceOption"
                                    class="mt-1 h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                <div class="flex-1">
                                    <div class="flex items-center gap-3">
                                        <div
                                            class="w-10 h-10 rounded-xl bg-blue-50 border border-blue-100 flex items-center justify-center text-blue-600">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    stroke-width="1.8"
                                                    d="M12 3l7 4v5c0 5-3.5 8-7 9-3.5-1-7-4-7-9V7l7-4z" />
                                            </svg>
                                        </div>
                                        <div>
                                            <div class="text-sm font-bold text-gray-900">Insurance</div>
                                            <div class="text-sm">Coverage for transport-related risks.
                                            </div>
                                        </div>
                                    </div>

                                    @if ($hasInsuranceOption)
                                        <div class="mt-4">
                                            <textarea wire:model.live="insuranceDescription" rows="3" placeholder="Provide insurance-related details..."
                                                class="block w-full border border-gray-300 rounded-xl focus:border-blue-500 focus:ring-1 focus:ring-blue-500 p-3 text-sm resize-y outline-none transition-colors shadow-sm"></textarea>
                                            @error('insuranceDescription')
                                                <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    @endif
                                </div>
                            </label>
                        </div>

                        <!-- Dangerous Goods -->
                        <div class="border border-gray-200 rounded-2xl p-5 bg-white transition-all">
                            <label class="flex items-start gap-4 cursor-pointer select-none">
                                <input type="checkbox" wire:model.live="isDangerous"
                                    class="mt-1 h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                <div class="flex-1">
                                    <div class="flex items-center gap-3">
                                        <div
                                            class="w-10 h-10 rounded-xl bg-red-50 border border-red-100 flex items-center justify-center text-red-600">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    stroke-width="1.8" d="M12 9v4" />
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    stroke-width="1.8" d="M12 17h.01" />
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    stroke-width="1.8"
                                                    d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z" />
                                            </svg>
                                        </div>
                                        <div>
                                            <div class="text-sm font-bold text-gray-900">Dangerous Goods</div>
                                            <div class="text-sm">Hazardous materials or regulated cargo.
                                            </div>
                                        </div>
                                    </div>

                                    @if ($isDangerous)
                                        <div class="mt-4">
                                            <textarea wire:model.live="dangerousGoodsDescription" rows="3"
                                                placeholder="Specify the type of dangerous goods, classification, or handling precautions..."
                                                class="block w-full border border-gray-300 rounded-xl focus:border-blue-500 focus:ring-1 focus:ring-blue-500 p-3 text-sm resize-y outline-none transition-colors shadow-sm"></textarea>
                                            @error('dangerousGoodsDescription')
                                                <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    @endif
                                </div>
                            </label>
                        </div>

                        <!-- Urgent -->
                        <div class="border border-gray-200 rounded-2xl p-5 bg-white transition-all">
                            <label class="flex items-start gap-4 cursor-pointer select-none">
                                <input type="checkbox" wire:model.live="isUrgent"
                                    class="mt-1 h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                <div class="flex-1">
                                    <div class="flex items-center gap-3">
                                        <div
                                            class="w-10 h-10 rounded-xl bg-amber-50 border border-amber-100 flex items-center justify-center text-amber-600">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <circle cx="12" cy="12" r="9" stroke-width="1.8" />
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    stroke-width="1.8" d="M12 7v5l3 3" />
                                            </svg>
                                        </div>
                                        <div>
                                            <div class="text-sm font-bold text-gray-900">Urgent</div>
                                            <div class="text-sm">Priority handling and accelerated
                                                transport.</div>
                                        </div>
                                    </div>

                                    @if ($isUrgent)
                                        <div class="mt-4">
                                            <textarea wire:model.live="urgentDescription" rows="3"
                                                placeholder="Describe the urgency, deadline, or priority requirements..."
                                                class="block w-full border border-gray-300 rounded-xl focus:border-blue-500 focus:ring-1 focus:ring-blue-500 p-3 text-sm resize-y outline-none transition-colors shadow-sm"></textarea>
                                            @error('urgentDescription')
                                                <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    @endif
                                </div>
                            </label>
                        </div>

                        <!-- Special handling instructions -->
                        <div class="border border-gray-200 rounded-2xl p-5 bg-white transition-all">
                            <label class="flex items-start gap-4 cursor-pointer select-none">
                                <input type="checkbox" wire:model.live="hasSpecialHandlingInstructions"
                                    class="mt-1 h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                <div class="flex-1">
                                    <div class="flex items-center gap-3">
                                        <div
                                            class="w-10 h-10 rounded-xl bg-violet-50 border border-violet-100 flex items-center justify-center text-violet-600">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    stroke-width="1.8" d="M12 8v4l3 3" />
                                                <circle cx="12" cy="12" r="9" stroke-width="1.8" />
                                            </svg>
                                        </div>
                                        <div>
                                            <div class="text-sm font-bold text-gray-900">Special Handling Instructions
                                            </div>
                                            <div class="text-sm">Any special loading, unloading, or
                                                handling needs.</div>
                                        </div>
                                    </div>

                                    @if ($hasSpecialHandlingInstructions)
                                        <div class="mt-4">
                                            <textarea wire:model.live="specialHandlingDescription" rows="3"
                                                placeholder="Describe any special handling instructions..."
                                                class="block w-full border border-gray-300 rounded-xl focus:border-blue-500 focus:ring-1 focus:ring-blue-500 p-3 text-sm resize-y outline-none transition-colors shadow-sm"></textarea>
                                            @error('specialHandlingDescription')
                                                <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    @endif
                                </div>
                            </label>
                        </div>

                        <!-- Temperature-controlled transportation needed -->
                        <div class="border border-gray-200 rounded-2xl p-5 bg-white transition-all">
                            <label class="flex items-start gap-4 cursor-pointer select-none">
                                <input type="checkbox" wire:model.live="needsTemperatureControlledTransport"
                                    class="mt-1 h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                <div class="flex-1">
                                    <div class="flex items-center gap-3">
                                        <div
                                            class="w-10 h-10 rounded-xl bg-cyan-50 border border-cyan-100 flex items-center justify-center text-cyan-600">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    stroke-width="1.8" d="M12 3v18" />
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    stroke-width="1.8"
                                                    d="M8 7a4 4 0 118 0c0 2-1.5 3-2.5 4S12 13 12 15" />
                                            </svg>
                                        </div>
                                        <div>
                                            <div class="text-sm font-bold text-gray-900">Temperature-Controlled
                                                Transport</div>
                                            <div class="text-sm">Refrigerated or temperature-sensitive
                                                shipment.</div>
                                        </div>
                                    </div>

                                    @if ($needsTemperatureControlledTransport)
                                        <div class="mt-4">
                                            <textarea wire:model.live="temperatureControlledDescription" rows="3"
                                                placeholder="Specify the required temperature range or conditions..."
                                                class="block w-full border border-gray-300 rounded-xl focus:border-blue-500 focus:ring-1 focus:ring-blue-500 p-3 text-sm resize-y outline-none transition-colors shadow-sm"></textarea>
                                            @error('temperatureControlledDescription')
                                                <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    @endif
                                </div>
                            </label>
                        </div>

                        <!-- Fragile goods -->
                        <div class="border border-gray-200 rounded-2xl p-5 bg-white transition-all">
                            <label class="flex items-start gap-4 cursor-pointer select-none">
                                <input type="checkbox" wire:model.live="hasFragileGoods"
                                    class="mt-1 h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                <div class="flex-1">
                                    <div class="flex items-center gap-3">
                                        <div
                                            class="w-10 h-10 rounded-xl bg-pink-50 border border-pink-100 flex items-center justify-center text-pink-600">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    stroke-width="1.8"
                                                    d="M12 21c4-4 6-7 6-10a6 6 0 10-12 0c0 3 2 6 6 10z" />
                                            </svg>
                                        </div>
                                        <div>
                                            <div class="text-sm font-bold text-gray-900">Fragile Goods</div>
                                            <div class="text-sm">Items requiring extra care during
                                                transport.</div>
                                        </div>
                                    </div>

                                    @if ($hasFragileGoods)
                                        <div class="mt-4">
                                            <textarea wire:model.live="fragileGoodsDescription" rows="3"
                                                placeholder="Describe the fragile goods and any precautions..."
                                                class="block w-full border border-gray-300 rounded-xl focus:border-blue-500 focus:ring-1 focus:ring-blue-500 p-3 text-sm resize-y outline-none transition-colors shadow-sm"></textarea>
                                            @error('fragileGoodsDescription')
                                                <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    @endif
                                </div>
                            </label>
                        </div>

                        <!-- Additional Requirements - full width -->
                        <div class="md:col-span-2 border border-gray-200 rounded-2xl p-5 bg-white transition-all">
                            <label class="flex items-start gap-4 cursor-pointer select-none">
                                <input type="checkbox" wire:model.live="hasAdditionalRequirements"
                                    class="mt-1 h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                <div class="flex-1">
                                    <div class="flex items-center gap-3">
                                        <div
                                            class="w-10 h-10 rounded-xl bg-gray-50 border border-gray-200 flex items-center justify-center text-gray-600">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    stroke-width="1.8" d="M12 5v14M5 12h14" />
                                            </svg>
                                        </div>
                                        <div>
                                            <div class="text-sm font-bold text-gray-900">Additional Requirements</div>
                                            <div class="text-sm">Any other transport-related notes or
                                                requests.</div>
                                        </div>
                                    </div>

                                    @if ($hasAdditionalRequirements)
                                        <div class="mt-4">
                                            <textarea wire:model.live="additionalRequirementsDescription" rows="4"
                                                placeholder="Provide any additional requirements..."
                                                class="block w-full border border-gray-300 rounded-xl focus:border-blue-500 focus:ring-1 focus:ring-blue-500 p-3 text-sm resize-y outline-none transition-colors shadow-sm"></textarea>
                                            @error('additionalRequirementsDescription')
                                                <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    @endif
                                </div>
                            </label>
                        </div>

                    </div>
                </div>
                <!-- COLLECTE + LIVRAISON  -->
                <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm animate-fadeIn">
                    <div class="mb-5">
                        <h2 class="text-base font-bold text-gray-900">Collecte & livraison</h2>
                        <p class="text-sm text-gray-500 mt-1">Renseignez les deux adresses dans la même section.</p>
                    </div>

                    <!-- Validity Date & Time and Delivery Price -->
                    <div class="mb-8 grid grid-cols-1 md:grid-cols-3 gap-6 p-4 bg-indigo-50/30 rounded-xl border border-indigo-100/50">
                        <div>
                            <label class="block text-sm font-bold text-gray-800 mb-2">Validité de la demande (Date) *</label>
                            <div x-data="{ value: @entangle('validityDate').live }" 
                                 x-init="flatpickr($refs.datepicker, { dateFormat: 'Y-m-d', defaultDate: value, onChange: function(selectedDates, dateStr, instance) { value = dateStr; } })">
                                <div class="relative">
                                    <input type="text" x-ref="datepicker" x-model="value"
                                        placeholder="AAAA-MM-JJ"
                                        class="h-11 w-full appearance-none rounded-xl border @error('validityDate') border-red-500 @else border-gray-300 @enderror bg-white px-4 pr-11 text-sm text-gray-800 outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all shadow-sm">
                                    <span class="absolute top-1/2 right-3 -translate-y-1/2 text-gray-400 pointer-events-none">
                                        <svg class="w-[18px] h-[18px]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                    </span>
                                </div>
                                @error('validityDate')
                                    <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-800 mb-2">Heure de validité *</label>
                            <div class="relative">
                                <input type="time" wire:model.live="validityTime"
                                    onclick="this.showPicker()"
                                    class="h-11 w-full appearance-none rounded-xl border @error('validityTime') border-red-500 @else border-gray-300 @enderror bg-white px-4 pr-11 text-sm text-gray-800 outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all shadow-sm">
                                <span class="absolute top-1/2 right-3 -translate-y-1/2 text-gray-400 pointer-events-none">
                                    <svg class="fill-current" width="18" height="18" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path fill-rule="evenodd" clip-rule="evenodd" d="M3.04175 9.99984C3.04175 6.15686 6.1571 3.0415 10.0001 3.0415C13.8431 3.0415 16.9584 6.15686 16.9584 9.99984C16.9584 13.8428 13.8431 16.9582 10.0001 16.9582C6.1571 16.9582 3.04175 13.8428 3.04175 9.99984ZM10.0001 1.5415C5.32867 1.5415 1.54175 5.32843 1.54175 9.99984C1.54175 14.6712 5.32867 18.4582 10.0001 18.4582C14.6715 18.4582 18.4584 14.6712 18.4584 9.99984C18.4584 5.32843 14.6715 1.5415 10.0001 1.5415ZM9.99998 10.7498C9.58577 10.7498 9.24998 10.4141 9.24998 9.99984V5.4165C9.24998 5.00229 9.58577 4.6665 9.99998 4.6665C10.4142 4.6665 10.75 5.00229 10.75 5.4165V9.24984H13.3334C13.7476 9.24984 14.0834 9.58562 14.0834 9.99984C14.0834 10.4141 13.7476 10.7498 13.3334 10.7498H10.0001H9.99998Z" fill="currentColor" />
                                    </svg>
                                </span>
                            </div>
                            @error('validityTime')
                                <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-800 mb-2">Prix de livraison (€)</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-gray-500 font-bold">€</div>
                                <input type="number" step="0.01" wire:model.live="deliveryPrice"
                                    placeholder="0.00"
                                    class="h-11 w-full pl-8 rounded-xl border @error('deliveryPrice') border-red-500 @else border-gray-300 @enderror bg-white px-4 text-sm text-gray-800 outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all shadow-sm">
                            </div>
                            @error('deliveryPrice')
                                <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <!-- DESKTOP / TABLET TIMELINE -->
                    <div class="hidden md:block mb-8">
                        <div class="flex items-center w-full gap-4 md:gap-6">
                            <!-- Pickup -->
                            <div class="flex flex-col items-center shrink-0 min-w-[64px]">
                                <div
                                    class="w-12 h-12 rounded-full bg-[#17b99f]/10 border border-[#17b99f]/20 flex items-center justify-center text-[#17b99f] shadow-sm">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                            d="M12 3l8 4-8 4-8-4 8-4z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                            d="M4 7v10l8 4 8-4V7" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                            d="M12 11v10" />
                                    </svg>
                                </div>
                                <span class="mt-2 text-sm uppercase tracking-[0.18em] text-gray-400 font-bold">
                                    Départ
                                </span>
                            </div>

                            <!-- Line -->
                            <div class="flex-1 relative">
                                <div class="h-[3px] rounded-full bg-gray-100 overflow-hidden">
                                    <div
                                        class="h-full w-full rounded-full bg-gradient-to-r from-[#17b99f] via-sky-400 to-blue-500">
                                    </div>
                                </div>

                                <div class="absolute inset-0 flex items-center justify-center">
                                    <div class="bg-white px-2">
                                        <svg class="w-5 h-5 text-sky-500" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M13 7l5 5m0 0l-5 5m5-5H6" />
                                        </svg>
                                    </div>
                                </div>
                            </div>

                            <!-- Delivery -->
                            <div class="flex flex-col items-center shrink-0 min-w-[64px]">
                                <div
                                    class="w-12 h-12 rounded-full bg-blue-50 border border-blue-100 flex items-center justify-center text-blue-500 shadow-sm">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <circle cx="12" cy="12" r="9" stroke-width="1.8" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                            d="M9 12l2 2 4-4" />
                                    </svg>
                                </div>
                                <span class="mt-2 text-sm uppercase tracking-[0.18em] text-gray-400 font-bold">
                                    Arrivée
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- CONTENT -->
                    <div class="flex gap-4 md:gap-8">
                        <!-- MOBILE LEFT TIMELINE -->
                        <div class="flex md:hidden shrink-0 pt-2">
                            <div class="flex flex-col items-center">
                                <!-- pickup -->
                                <div class="flex flex-col items-center">
                                    <div
                                        class="w-10 h-10 rounded-full bg-[#17b99f]/10 border border-[#17b99f]/20 flex items-center justify-center text-[#17b99f] shadow-sm">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                                d="M12 3l8 4-8 4-8-4 8-4z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                                d="M4 7v10l8 4 8-4V7" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                                d="M12 11v10" />
                                        </svg>
                                    </div>
                                    <span class="mt-1 text-xs uppercase tracking-[0.16em] text-gray-400 font-bold">
                                        Départ
                                    </span>
                                </div>

                                <!-- connector -->
                                <div class="relative my-3 flex-1 min-h-[220px] w-8 flex justify-center">
                                    <div
                                        class="w-[3px] h-full rounded-full bg-gradient-to-b from-[#17b99f] via-sky-400 to-blue-500">
                                    </div>

                                    <div class="absolute inset-0 flex items-center justify-center">
                                        <div class="bg-white py-1">
                                            <svg class="w-4 h-4 text-sky-500" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M7 11l5 5m0 0l5-5m-5 5V6" />
                                            </svg>
                                        </div>
                                    </div>
                                </div>

                                <!-- delivery -->
                                <div class="flex flex-col items-center">
                                    <div
                                        class="w-10 h-10 rounded-full bg-blue-50 border border-blue-100 flex items-center justify-center text-blue-500 shadow-sm">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <circle cx="12" cy="12" r="9" stroke-width="1.8" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                                d="M9 12l2 2 4-4" />
                                        </svg>
                                    </div>
                                    <span class="mt-1 text-xs uppercase tracking-[0.16em] text-gray-400 font-bold">
                                        Arrivée
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- 2 SECTIONS -->
                        <div class="flex-1 flex flex-col md:flex-row gap-8">
                            <!-- LEFT: PICKUP -->
                            <div class="flex-1 min-w-0">
                                <div class="space-y-4">
                                    <div class="relative">
                                        <input type="text" wire:model.live="pickupAddress"
                                            placeholder="Lieu de collecte *"
                                            class="block w-full h-11 bg-white border @error('pickupAddress') border-red-500 @else border-gray-300 @enderror rounded-xl px-4 text-sm focus:ring-1 focus:ring-blue-500 focus:border-blue-500 outline-none transition-colors pl-12">
                                        <div class="absolute left-4 top-1/2 -translate-y-1/2 text-[#17b99f]">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    stroke-width="1.8"
                                                    d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    stroke-width="1.8" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                            </svg>
                                        </div>
                                    </div>
                                    @error('pickupAddress')
                                        <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span>
                                    @enderror

                                    <div class="border border-gray-200 rounded-xl p-4">
                                        <div class="text-xs uppercase tracking-wider text-gray-400 font-bold mb-3">
                                            Date et Heure d'enlèvement *
                                        </div>
                                        <div class="grid grid-cols-2 gap-3">
                                            <div x-data="{ value: @entangle('latestPickupDate').live }" 
                                                 x-init="flatpickr($refs.datepicker, { dateFormat: 'Y-m-d', defaultDate: value, onChange: function(selectedDates, dateStr, instance) { value = dateStr; } })">
                                                <div class="relative">
                                                    <input type="text" x-ref="datepicker" x-model="value"
                                                        placeholder="AAAA-MM-JJ"
                                                        class="h-11 w-full appearance-none rounded-xl border @error('latestPickupDate') border-red-500 @else border-gray-300 @enderror bg-white px-4 pr-11 text-sm text-gray-800 outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all shadow-sm">
                                                    <span class="absolute top-1/2 right-3 -translate-y-1/2 text-gray-400 pointer-events-none">
                                                        <svg class="w-[18px] h-[18px]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                        </svg>
                                                    </span>
                                                </div>
                                                @error('latestPickupDate')
                                                    <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span>
                                                @enderror
                                            </div>
                                            <div>
                                                <div class="relative">
                                                    <input type="time" wire:model.live="latestPickupTime"
                                                        onclick="this.showPicker()"
                                                        class="h-11 w-full appearance-none rounded-xl border @error('latestPickupTime') border-red-500 @else border-gray-300 @enderror bg-white px-4 pr-11 text-sm text-gray-800 outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all shadow-sm">
                                                    <span class="absolute top-1/2 right-3 -translate-y-1/2 text-gray-400 pointer-events-none">
                                                        <svg class="fill-current" width="18" height="18" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                            <path fill-rule="evenodd" clip-rule="evenodd" d="M3.04175 9.99984C3.04175 6.15686 6.1571 3.0415 10.0001 3.0415C13.8431 3.0415 16.9584 6.15686 16.9584 9.99984C16.9584 13.8428 13.8431 16.9582 10.0001 16.9582C6.1571 16.9582 3.04175 13.8428 3.04175 9.99984ZM10.0001 1.5415C5.32867 1.5415 1.54175 5.32843 1.54175 9.99984C1.54175 14.6712 5.32867 18.4582 10.0001 18.4582C14.6715 18.4582 18.4584 14.6712 18.4584 9.99984C18.4584 5.32843 14.6715 1.5415 10.0001 1.5415ZM9.99998 10.7498C9.58577 10.7498 9.24998 10.4141 9.24998 9.99984V5.4165C9.24998 5.00229 9.58577 4.6665 9.99998 4.6665C10.4142 4.6665 10.75 5.00229 10.75 5.4165V9.24984H13.3334C13.7476 9.24984 14.0834 9.58562 14.0834 9.99984C14.0834 10.4141 13.7476 10.7498 13.3334 10.7498H10.0001H9.99998Z" fill="currentColor" />
                                                        </svg>
                                                    </span>
                                                </div>
                                                @error('latestPickupTime')
                                                    <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>

                                    <div class="grid grid-cols-1 gap-4">
                                        <div class="border border-gray-200 rounded-xl p-4">
                                            <div class="text-xs uppercase tracking-wider text-gray-400 font-bold mb-3">
                                                Options
                                            </div>
                                            <div class="space-y-2">
                                                <label class="flex items-center space-x-2 cursor-pointer">
                                                    <input type="checkbox" wire:model.live="pickupOptions"
                                                        value="hayon"
                                                        class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                                    <span class="text-sm font-semibold text-gray-800">Hayon +
                                                        transpalette</span>
                                                </label>
                                                <label class="flex items-center space-x-2 cursor-pointer">
                                                    <input type="checkbox" wire:model.live="pickupOptions"
                                                        value="grue"
                                                        class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                                    <span class="text-sm font-semibold text-gray-800">Grue</span>
                                                </label>
                                                </label>
                                             </div>
                                         </div>

                                    </div>
                                    </div>
                                </div>
                            </div>

                            <!-- RIGHT: DELIVERY -->
                            <div class="flex-1 min-w-0">
                                <div class="space-y-4">
                                    <div class="relative">
                                        <input type="text" wire:model.live="deliveryAddress"
                                            placeholder="Lieu de livraison *"
                                            class="block w-full h-11 bg-white border @error('deliveryAddress') border-red-500 @else border-gray-300 @enderror rounded-xl px-4 text-sm focus:ring-1 focus:ring-blue-500 focus:border-blue-500 outline-none transition-colors pl-12">
                                        <div class="absolute left-4 top-1/2 -translate-y-1/2 text-blue-500">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    stroke-width="1.8" d="M3 10.5l9-7 9 7" />
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    stroke-width="1.8"
                                                    d="M5 9.5V20a1 1 0 001 1h4v-6h4v6h4a1 1 0 001-1V9.5" />
                                            </svg>
                                        </div>
                                    </div>
                                    @error('deliveryAddress')
                                        <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span>
                                    @enderror

                                    <div class="border border-gray-200 rounded-xl p-4">
                                        <div class="text-xs uppercase tracking-wider text-gray-400 font-bold mb-3">
                                            Date et Heure de livraison *
                                        </div>
                                        <div class="grid grid-cols-2 gap-3">
                                            <div x-data="{ value: @entangle('latestDeliveryDate').live }" 
                                                 x-init="flatpickr($refs.datepicker, { dateFormat: 'Y-m-d', defaultDate: value, onChange: function(selectedDates, dateStr, instance) { value = dateStr; } })">
                                                <div class="relative">
                                                    <input type="text" x-ref="datepicker" x-model="value"
                                                        placeholder="AAAA-MM-JJ"
                                                        class="h-11 w-full appearance-none rounded-xl border @error('latestDeliveryDate') border-red-500 @else border-gray-300 @enderror bg-white px-4 pr-11 text-sm text-gray-800 outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all shadow-sm">
                                                    <span class="absolute top-1/2 right-3 -translate-y-1/2 text-gray-400 pointer-events-none">
                                                        <svg class="w-[18px] h-[18px]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                        </svg>
                                                    </span>
                                                </div>
                                                @error('latestDeliveryDate')
                                                    <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span>
                                                @enderror
                                            </div>
                                            <div>
                                                <div class="relative">
                                                    <input type="time" wire:model.live="latestDeliveryTime"
                                                        onclick="this.showPicker()"
                                                        class="h-11 w-full appearance-none rounded-xl border @error('latestDeliveryTime') border-red-500 @else border-gray-300 @enderror bg-white px-4 pr-11 text-sm text-gray-800 outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all shadow-sm">
                                                    <span class="absolute top-1/2 right-3 -translate-y-1/2 text-gray-400 pointer-events-none">
                                                        <svg class="fill-current" width="18" height="18" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                            <path fill-rule="evenodd" clip-rule="evenodd" d="M3.04175 9.99984C3.04175 6.15686 6.1571 3.0415 10.0001 3.0415C13.8431 3.0415 16.9584 6.15686 16.9584 9.99984C16.9584 13.8428 13.8431 16.9582 10.0001 16.9582C6.1571 16.9582 3.04175 13.8428 3.04175 9.99984ZM10.0001 1.5415C5.32867 1.5415 1.54175 5.32843 1.54175 9.99984C1.54175 14.6712 5.32867 18.4582 10.0001 18.4582C14.6715 18.4582 18.4584 14.6712 18.4584 9.99984C18.4584 5.32843 14.6715 1.5415 10.0001 1.5415ZM9.99998 10.7498C9.58577 10.7498 9.24998 10.4141 9.24998 9.99984V5.4165C9.24998 5.00229 9.58577 4.6665 9.99998 4.6665C10.4142 4.6665 10.75 5.00229 10.75 5.4165V9.24984H13.3334C13.7476 9.24984 14.0834 9.58562 14.0834 9.99984C14.0834 10.4141 13.7476 10.7498 13.3334 10.7498H10.0001H9.99998Z" fill="currentColor" />
                                                        </svg>
                                                    </span>
                                                </div>
                                                @error('latestDeliveryTime')
                                                    <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>

                                    <div class="grid grid-cols-1 gap-4">
                                        <div class="border border-gray-200 rounded-xl p-4">
                                            <div class="text-xs uppercase tracking-wider text-gray-400 font-bold mb-3">
                                                Options
                                            </div>
                                            <div class="space-y-2">
                                                <label class="flex items-center space-x-2 cursor-pointer">
                                                    <input type="checkbox" wire:model.live="deliveryOptions"
                                                        value="hayon"
                                                        class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                                    <span class="text-sm font-semibold text-gray-800">Hayon +
                                                        transpalette</span>
                                                </label>
                                                <label class="flex items-center space-x-2 cursor-pointer">
                                                    <input type="checkbox" wire:model.live="deliveryOptions"
                                                        value="grue"
                                                        class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                                    <span class="text-sm font-semibold text-gray-800">Grue</span>
                                                </label>
                                                </label>
                                             </div>
                                         </div>

                                    </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                <div class="mt-10 flex justify-end">
                    <button wire:click="submit"
                        class="inline-flex items-center justify-center px-10 py-4 bg-blue-600 hover:bg-blue-700 text-white font-black text-lg rounded-2xl transition-all shadow-[0_8px_20px_-5px_rgba(37,99,235,0.3)] hover:shadow-[0_10px_25px_-5px_rgba(37,99,235,0.4)] focus:outline-none focus:ring-4 focus:ring-blue-500/20 active:scale-[0.98]">
                        Envoyer ma demande
                        <svg class="w-5 h-5 ml-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                d="M14 5l7 7m0 0l-7 7m7-7H3" />
                        </svg>
                    </button>
                </div>
            </div>
        @endif

    <!-- SUCCESS VIEW -->
    @if ($isSubmitted)
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-14 text-center animate-fadeIn">
            <div class="mb-10 flex justify-center">
                <div
                    class="w-24 h-24 bg-green-50 text-green-600 rounded-full flex items-center justify-center shadow-inner border border-green-100">
                    <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
                    </svg>
                </div>
            </div>

            <h2 class="text-2xl font-black text-gray-900 mb-4">
                {{ $shipmentRecord ? 'Modifications enregistrées !' : 'Demande envoyée !' }}
            </h2>
            <p class="text-lg text-gray-500 max-w-2xl mx-auto mb-8">
                {{ $shipmentRecord ? 'Votre demande a bien été mise à jour.' : 'Votre demande a bien été prise en compte. Nous vous répondrons dans les plus brefs délais.' }}
            </p>
            {{-- Quick Actions --}}
            <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
                @if($shipmentRecord)
                    <a href="{{ route('transport-firm-bid.show', $shipmentRecord) }}" 
                       class="inline-flex items-center justify-center px-6 py-2.5 bg-gray-900 border border-transparent rounded-xl text-sm font-semibold text-white hover:bg-gray-800 transition-colors shadow-sm w-full sm:w-auto">
                        Retour aux détails
                    </a>
                @else
                    <a href="{{ route('transport-firm-bid.index') }}" 
                       class="inline-flex items-center justify-center px-6 py-2.5 bg-gray-900 border border-transparent rounded-xl text-sm font-semibold text-white hover:bg-gray-800 transition-colors shadow-sm w-full sm:w-auto">
                        Voir mes expéditions
                    </a>
                    <button onclick="window.location.reload()" 
                            class="inline-flex items-center justify-center px-6 py-2.5 bg-white border border-gray-300 rounded-xl text-sm font-semibold text-gray-700 hover:bg-gray-50 transition-colors shadow-sm w-full sm:w-auto">
                        Créer une nouvelle demande
                    </button>
                @endif
            </div>
        </div>
    @endif

    <style>
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-fadeIn {
            animation: fadeIn 0.35s ease-out forwards;
        }
    </style>

</div>
</div>
