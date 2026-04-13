<div>
    <div class="mx-auto space-y-6">

        @if (!$isSubmitted)
            <div class="space-y-4">

                <!-- MERCHANDISES -->
                <div
                    class="bg-white rounded-[24px] shadow-[0_2px_10px_-3px_rgba(6,81,237,0.1)] border border-gray-100 p-6 md:p-10 animate-fadeIn">

                    <!-- SECTION HEADER -->
                    <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-4 mb-8">
                        <div>
                            <h2 class="text-lg font-bold text-gray-900 tracking-tight">
                                {{ $editingLotIndex !== null ? 'Edit lot ' . ($editingLotIndex + 1) : 'Add goods' }}
                            </h2>
                            <p class="text-sm text-gray-500 mt-1">
                                Enter the details of each shipment lot.
                            </p>
                        </div>

                        @if ($editingLotIndex !== null)
                            <button wire:click="cancelEdit"
                                class="inline-flex items-center justify-center px-5 py-2.5 text-sm font-semibold text-gray-700 bg-white border border-gray-200 rounded-xl hover:bg-gray-50 hover:border-gray-300 hover:text-gray-900 transition-all focus:outline-none focus:ring-2 focus:ring-gray-200">
                                Cancel Edit
                            </button>
                        @endif
                    </div>

                    <!-- LOT EDITOR -->
                    <div class="rounded-2xl border border-gray-100 bg-gray-50/50 p-6 md:p-8 relative overflow-hidden">
                        <!-- Decorative subtle gradient -->
                        <div
                            class="absolute top-0 right-0 -mt-4 -mr-4 w-32 h-32 bg-gradient-to-br from-blue-50 to-transparent rounded-full opacity-50 pointer-events-none">
                        </div>

                        <div
                            class="flex flex-col md:flex-row md:items-center md:justify-between gap-3 mb-8 relative z-10">
                            <div class="flex items-center gap-3">
                                <span
                                    class="inline-flex items-center justify-center h-9 min-w-[76px] px-3 rounded-lg bg-blue-50 text-blue-700 border border-blue-100/50 text-sm font-bold tracking-wide">
                                    LOT {{ $editingLotIndex !== null ? $editingLotIndex + 1 : count($lots) + 1 }}
                                </span>
                                <span class="hidden md:block text-base">
                                    {{ $editingLotIndex !== null ? 'Update the details for this lot' : 'Configure your new shipment unit' }}
                                </span>
                            </div>
                        </div>

                        <!-- MAIN FIELDS -->
                        <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                            <!-- Type -->
                            <div>
                                <label class="block text-sm font-bold text-gray-800 mb-2">Packaging type *</label>
                                <select wire:model.live="type"
                                    class="w-full h-11 bg-white border border-gray-300 rounded-xl px-3 text-sm text-gray-700 outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-colors shadow-[0_1px_2px_0_rgba(0,0,0,0.02)]">
                                    <option value="colis">Parcel (custom quote)</option>
                                    <option value="palette_standard">Standard pallet(s)</option>
                                    <option value="palette_non_standard">Non-standard pallet(s)</option>
                                    <option value="caisse">Wood or metal crate(s)</option>
                                    <option value="oeuvre_art">Artwork / art object</option>
                                    <option value="hors_gabarit">Out of gauge</option>
                                    <option value="conteneur_groupage">Partial container (groupage)</option>
                                    <option value="conteneur_complet">Full container(s)</option>
                                    <option value="vehicule">Vehicle(s)</option>
                                </select>
                            </div>

                            @if ($type === 'palette_standard')
                                <div class="md:col-span-2">
                                    <label class="block text-sm font-bold text-gray-800 mb-2">Pallet type *</label>
                                    <select wire:model.live="palette_type"
                                        class="w-full h-11 bg-white border border-gray-300 rounded-xl px-3 text-sm text-gray-700 outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-colors shadow-[0_1px_2px_0_rgba(0,0,0,0.02)]">
                                        @foreach (array_keys($palette_dimensions) as $p_type)
                                            <option value="{{ $p_type }}">{{ str_replace('_', 'x', $p_type) }} cm
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            @elseif($type === 'conteneur_complet')
                                <div class="md:col-span-3">
                                    <label class="block text-sm font-bold text-gray-800 mb-2">Type / Capacity /
                                        Dimensions *</label>
                                    <select wire:model.live="container_type"
                                        class="w-full h-11 bg-white border border-gray-300 rounded-xl px-3 text-sm text-gray-700 outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-colors shadow-[0_1px_2px_0_rgba(0,0,0,0.02)]">
                                        @foreach ($container_options as $c_type => $c_label)
                                            <option value="{{ $c_type }}">{{ $c_label }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            @elseif($type === 'vehicule')
                                <div class="md:col-span-2">
                                    <label class="block text-sm font-bold text-gray-800 mb-2">Brand *</label>
                                    <input type="text" wire:model.live="brand" placeholder="Peugeot, Honda..."
                                        class="w-full h-11 bg-white border @error('brand') border-red-500 @else border-gray-300 @enderror rounded-xl px-3 text-sm text-gray-700 outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-colors shadow-[0_1px_2px_0_rgba(0,0,0,0.02)]">
                                    @error('brand')
                                        <span class="text-xs text-red-500 mt-1 block font-bold">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div>
                                    <label class="block text-sm font-bold text-gray-800 mb-2">Model *</label>
                                    <input type="text" wire:model.live="model" placeholder="308, CBR1000RR"
                                        class="w-full h-11 bg-white border @error('model') border-red-500 @else border-gray-300 @enderror rounded-xl px-3 text-sm text-gray-700 outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-colors shadow-[0_1px_2px_0_rgba(0,0,0,0.02)]">
                                    @error('model')
                                        <span class="text-xs text-red-500 mt-1 block font-bold">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div>
                                    <label class="block text-sm font-bold text-gray-800 mb-2">Weight *</label>
                                    <select wire:model.live="vehicle_weight"
                                        class="w-full h-11 bg-white border @error('vehicle_weight') border-red-500 @else border-gray-300 @enderror rounded-xl px-3 text-sm text-gray-700 outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-colors shadow-[0_1px_2px_0_rgba(0,0,0,0.02)]">
                                        <option value="1t">1t</option>
                                        <option value="1.5t">1.5t</option>
                                        <option value="2t">2t</option>
                                        <option value="3t">3t</option>
                                    </select>
                                    @error('vehicle_weight')
                                        <span class="text-xs text-red-500 mt-1 block font-bold">{{ $message }}</span>
                                    @enderror
                                </div>
                            @else
                                <div>
                                    <label class="block text-sm font-bold text-gray-800 mb-2">Length (cm) *</label>
                                    <input type="number" wire:model.live="length" placeholder="L"
                                        class="w-full h-11 bg-white border @error('length') border-red-500 @else border-gray-300 @enderror rounded-xl px-3 text-sm text-gray-700 outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-colors shadow-[0_1px_2px_0_rgba(0,0,0,0.02)]">
                                    @error('length')
                                        <span class="text-xs text-red-500 mt-1 block font-bold">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div>
                                    <label class="block text-sm font-bold text-gray-800 mb-2">Width (cm) *</label>
                                    <input type="number" wire:model.live="width" placeholder="W"
                                        class="w-full h-11 bg-white border @error('width') border-red-500 @else border-gray-300 @enderror rounded-xl px-3 text-sm text-gray-700 outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-colors shadow-[0_1px_2px_0_rgba(0,0,0,0.02)]">
                                    @error('width')
                                        <span class="text-xs text-red-500 mt-1 block font-bold">{{ $message }}</span>
                                    @enderror
                                </div>
                            @endif

                            @if ($type !== 'vehicule')
                                @if ($type !== 'conteneur_complet')
                                    <div>
                                        <label class="block text-sm font-bold text-gray-800 mb-2">Height (cm)
                                            *</label>
                                        <input type="number" wire:model.live="height" placeholder="H"
                                            class="w-full h-11 bg-white border @error('height') border-red-500 @else border-gray-300 @enderror rounded-xl px-3 text-sm text-gray-700 outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-colors shadow-[0_1px_2px_0_rgba(0,0,0,0.02)]">
                                        @error('height')
                                            <span
                                                class="text-xs text-red-500 mt-1 block font-bold">{{ $message }}</span>
                                        @enderror
                                    </div>
                                @endif

                                <div>
                                    <label class="block text-sm font-bold text-gray-800 mb-2">Quantity *</label>
                                    <input type="number" wire:model.live="quantity" min="1"
                                        class="w-full h-11 bg-white border @error('quantity') border-red-500 @else border-gray-300 @enderror rounded-xl px-3 text-sm text-gray-700 outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-colors shadow-[0_1px_2px_0_rgba(0,0,0,0.02)]">
                                    @error('quantity')
                                        <span class="text-xs text-red-500 mt-1 block font-bold">{{ $message }}</span>
                                    @enderror
                                </div>
                            @endif
                        </div>

                        <!-- SECONDARY FIELDS -->
                        <div class="mt-6">
                            @if ($type !== 'vehicule')
                                <div class="grid grid-cols-1 xl:grid-cols-12 gap-4 items-start">
                                    <div class="xl:col-span-4">
                                        <label class="block text-sm font-bold text-gray-800 mb-2">
                                            Unit weight (kg) *
                                        </label>
                                        <div class="relative">
                                            <input type="number" wire:model.live="weight" step="0.01"
                                                class="w-full h-12 bg-white border @error('weight') border-red-500 @else border-gray-300 @enderror rounded-xl pl-4 pr-12 text-sm text-gray-800 font-semibold outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-colors shadow-[0_1px_2px_0_rgba(0,0,0,0.02)]">
                                            <div
                                                class="absolute inset-y-0 right-0 flex items-center pr-4 pointer-events-none">
                                                <span class="text-gray-400 font-bold text-sm">kg</span>
                                            </div>
                                        </div>
                                        @error('weight')
                                            <span
                                                class="text-xs text-red-500 mt-1 block font-bold">{{ $message }}</span>
                                        @enderror

                                        <div class="mt-4">
                                            <label
                                                class="h-11 bg-white border border-gray-300 rounded-xl px-4 flex items-center gap-3 cursor-pointer shadow-[0_1px_2px_0_rgba(0,0,0,0.02)] hover:border-gray-400 transition-colors">
                                                <input type="checkbox" wire:model.live="is_stackable"
                                                    class="h-4.5 w-4.5 rounded border-gray-300 text-blue-500 focus:ring-blue-500 transition-colors">
                                                <span class="text-sm font-semibold text-gray-800">Stackable (superposable)</span>
                                            </label>
                                            <p class="text-[10px] text-gray-400 mt-1 leading-tight">
                                                Check this if other pallets can be placed on top to reduce costs.
                                            </p>
                                        </div>
                                    </div>

                                    <div class="xl:col-span-8">
                                        <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                                            <!-- Summary Cards -->
                                            <div
                                                class="rounded-xl border border-gray-100 bg-white p-4 shadow-[0_1px_3px_0_rgba(0,0,0,0.02)] flex flex-col justify-center relative overflow-hidden group">
                                                <div
                                                    class="absolute top-0 right-0 w-12 h-12 bg-gray-50 rounded-bl-full -z-10 transition-transform group-hover:scale-110">
                                                </div>
                                                <div
                                                    class="text-xs uppercase tracking-wider text-gray-500 font-bold mb-1">
                                                    Total weight
                                                </div>
                                                <div class="flex items-baseline gap-1">
                                                    <div class="text-base font-bold text-gray-900">
                                                        {{ number_format((float) ($weight ?: 0) * (int) ($quantity ?: 1), 2, '.', '') }}
                                                    </div>
                                                    <div class="text-xs font-bold text-gray-400">kg</div>
                                                </div>
                                            </div>

                                            <div
                                                class="rounded-xl border border-gray-100 bg-white p-4 shadow-[0_1px_3px_0_rgba(0,0,0,0.02)] flex flex-col justify-center relative overflow-hidden group">
                                                <div
                                                    class="absolute top-0 right-0 w-12 h-12 bg-gray-50 rounded-bl-full -z-10 transition-transform group-hover:scale-110">
                                                </div>
                                                <div
                                                    class="text-xs uppercase tracking-wider text-gray-500 font-bold mb-1">
                                                    Unit volume
                                                </div>
                                                <div class="flex items-baseline gap-1">
                                                    <div class="text-base font-bold text-gray-900">
                                                        {{ number_format($this->unit_volume, 4, '.', '') }}
                                                    </div>
                                                    <div class="text-xs font-bold text-gray-400">m³</div>
                                                </div>
                                            </div>

                                            <div
                                                class="rounded-xl border border-indigo-100 bg-indigo-50/30 p-4 shadow-[0_1px_3px_0_rgba(79,70,229,0.05)] flex flex-col justify-center relative overflow-hidden group">
                                                <div
                                                    class="absolute -right-4 -top-4 w-16 h-16 bg-indigo-500/5 rounded-full blur-xl">
                                                </div>
                                                <div
                                                    class="text-xs uppercase tracking-wider text-indigo-600 font-bold mb-1">
                                                    Total volume
                                                </div>
                                                <div class="flex items-baseline gap-1 relative z-10">
                                                    <div class="text-base font-bold text-indigo-900">
                                                        {{ number_format($this->current_lot_volume, 4, '.', '') }}
                                                    </div>
                                                    <div class="text-xs font-bold text-indigo-500">m³</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @else
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-bold text-gray-800 mb-2">Quantity
                                            *</label>
                                        <input type="number" wire:model.live="quantity" min="1"
                                            class="w-full h-11 bg-white border @error('quantity') border-red-500 @else border-gray-300 @enderror rounded-xl px-3 text-sm text-gray-700 outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-colors shadow-[0_1px_2px_0_rgba(0,0,0,0.02)]">
                                        @error('quantity')
                                            <span
                                                class="text-xs text-red-500 mt-1 block font-bold">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div>
                                        <label class="block text-sm font-bold text-gray-800 mb-2">Vehicle
                                            condition</label>
                                        <div class="flex flex-col gap-2">
                                            <label
                                                class="h-11 bg-white border border-gray-300 rounded-xl px-4 flex items-center gap-3 cursor-pointer shadow-[0_1px_2px_0_rgba(0,0,0,0.02)] hover:border-gray-400 transition-colors">
                                                <input type="checkbox" wire:model.live="is_rolling"
                                                    class="h-4.5 w-4.5 rounded border-gray-300 text-green-500 focus:ring-green-500 transition-colors">
                                                <span class="text-sm font-semibold text-gray-700">Rolling vehicle</span>
                                            </label>
                                            <label
                                                class="h-11 bg-white border border-gray-300 rounded-xl px-4 flex items-center gap-3 cursor-pointer shadow-[0_1px_2px_0_rgba(0,0,0,0.02)] hover:border-gray-400 transition-colors">
                                                <input type="checkbox" wire:model.live="is_stackable"
                                                    class="h-4.5 w-4.5 rounded border-gray-300 text-blue-500 focus:ring-blue-500 transition-colors">
                                                <span class="text-sm font-semibold text-gray-700">Stackable (superposable)</span>
                                            </label>
                                        </div>
                                        @error('is_rolling')
                                            <span
                                                class="text-xs text-red-500 mt-1 block font-bold">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            @endif
                        </div>

                        <div class="mt-8 pt-6 border-t border-gray-200/60 flex justify-end">
                            <button wire:click="addLot"
                                class="inline-flex items-center justify-center px-6 py-2.5 bg-gray-900 hover:bg-gray-800 text-white font-bold text-sm rounded-xl transition-all shadow-[0_4px_10px_rgba(0,0,0,0.1)] hover:shadow-[0_4px_14px_rgba(0,0,0,0.15)] focus:outline-none focus:ring-2 focus:ring-gray-900/50 active:scale-[0.98]">
                                {{ $editingLotIndex !== null ? 'Save lot modifications' : 'Add this lot' }}
                                <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                        d="M12 4v16m8-8H4" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- LOTS -->
                    @if (count($lots) > 0)
                        <div class="mt-8 pt-8 border-t border-gray-100">
                            <div class="mb-5">
                                <h3 class="text-lg font-bold text-gray-900">My goods</h3>
                                <p class="text-sm text-gray-500 mt-1">Review the lots already added.</p>
                            </div>

                            @error('lots')
                                <div
                                    class="mb-4 p-3 rounded-xl bg-red-50 border border-red-200 text-red-700 text-sm font-semibold">
                                    You must add at least one lot.
                                </div>
                            @enderror

                            <div
                                class="overflow-hidden rounded-xl border border-gray-100 bg-white shadow-[0_1px_3px_0_rgba(0,0,0,0.02)]">
                                <div class="overflow-x-auto">
                                    <table class="w-full text-left text-sm text-gray-700">
                                        <thead
                                            class="bg-gray-50/50 border-b border-gray-100 text-sm uppercase tracking-wider text-gray-500 font-bold">
                                            <tr>
                                                <th class="px-4 py-3 font-semibold">Lot</th>
                                                <th class="px-4 py-3 font-semibold">Packaging</th>
                                                <th class="px-4 py-3 font-semibold">Qty</th>
                                                <th class="px-4 py-3 font-semibold">Unit Wt</th>
                                                <th class="px-4 py-3 font-semibold">Total Wt</th>
                                                <th class="px-4 py-3 font-semibold text-center">Stackable</th>
                                                <th class="px-4 py-3 font-semibold">Total Vol</th>
                                                <th class="px-4 py-3"></th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-gray-100">
                                            @foreach ($lots as $index => $lot)
                                                <tr class="hover:bg-gray-50 transition-colors">
                                                    <td class="px-4 py-3 font-bold text-gray-900">{{ $index + 1 }}
                                                    </td>
                                                    <td class="px-4 py-3">
                                                        {{ $lot['type'] === 'colis' ? 'Parcel (custom quote)' : str_replace('_', ' ', $lot['type']) }}
                                                    </td>
                                                    <td class="px-4 py-3 font-bold text-gray-900">
                                                        {{ $lot['quantity'] }}</td>
                                                    <td class="px-4 py-3 font-bold text-gray-900">
                                                        {{ number_format($lot['weight'], 2, ',', ' ') }} kg
                                                    </td>
                                                    <td class="px-4 py-3 font-bold text-gray-900">
                                                        {{ number_format($lot['weight'] * $lot['quantity'], 2, ',', ' ') }}
                                                        kg
                                                    </td>
                                                    <td class="px-4 py-3 text-center">
                                                        @if ($lot['is_stackable'] ?? true)
                                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-blue-50 text-blue-600 border border-blue-100">YES</span>
                                                        @else
                                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-gray-50 text-gray-400 border border-gray-100">NO</span>
                                                        @endif
                                                    </td>
                                                    <td class="px-4 py-3 font-bold text-gray-900">
                                                        {{ number_format($lot['volume'] ?? 0, 4, ',', ' ') }} m³
                                                    </td>
                                                    <td class="px-4 py-3 text-right whitespace-nowrap">
                                                        <button wire:click="editLot({{ $index }})"
                                                            class="text-blue-600 hover:underline text-sm font-semibold mr-3">
                                                            Edit
                                                        </button>
                                                        <button wire:click="removeLot({{ $index }})"
                                                            class="text-red-600 hover:underline text-sm font-semibold">
                                                            Delete
                                                        </button>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-5">
                                <div
                                    class="rounded-xl border border-gray-100 bg-gray-50/50 p-4 shadow-[0_1px_2px_0_rgba(0,0,0,0.01)]">
                                    <div class="text-sm uppercase tracking-wider text-gray-400 font-bold">Lots
                                    </div>
                                    <div class="text-lg font-bold text-gray-900 mt-1">{{ count($lots) }}</div>
                                </div>

                                <div
                                    class="rounded-xl border border-gray-100 bg-gray-50/50 p-4 shadow-[0_1px_2px_0_rgba(0,0,0,0.01)]">
                                    <div class="text-sm uppercase tracking-wider text-gray-400 font-bold">Total
                                        weight
                                    </div>
                                    <div class="text-lg font-bold text-gray-900 mt-1">
                                        {{ number_format($total_weight, 0, ',', ' ') }}
                                        <span class="text-sm font-bold text-gray-400">kg</span>
                                    </div>
                                </div>

                                <div
                                    class="rounded-xl border border-gray-100 bg-gray-50/50 p-4 shadow-[0_1px_2px_0_rgba(0,0,0,0.01)]">
                                    <div class="text-sm uppercase tracking-wider text-gray-400 font-bold">Total
                                        volume
                                    </div>
                                    <div class="text-lg font-bold text-gray-900 mt-1">
                                        {{ number_format($total_volume, 4, '.', '') }}
                                        <span class="text-sm font-bold text-gray-400">m³</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

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
                                            <div>
                                                <input type="date" wire:model.live="latestPickupDate"
                                                    class="block w-full h-11 bg-white border @error('latestPickupDate') border-red-500 @else border-gray-300 @enderror rounded-lg px-4 text-sm focus:ring-1 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all">
                                                @error('latestPickupDate')
                                                    <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span>
                                                @enderror
                                            </div>
                                            <div>
                                                <input type="time" wire:model.live="latestPickupTime"
                                                    class="block w-full h-11 bg-white border @error('latestPickupTime') border-red-500 @else border-gray-300 @enderror rounded-lg px-4 text-sm focus:ring-1 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all">
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
                                                <div class="pt-2 mt-2 border-t border-gray-100">
                                                    <label class="flex items-center space-x-2 cursor-pointer">
                                                        <input type="checkbox" wire:model.live="pickupNotify"
                                                            class="h-4 w-4 rounded border-gray-300 text-brand-600 focus:ring-brand-500">
                                                        <span class="text-sm font-bold text-brand-600 uppercase tracking-tight">Appeler avant passage</span>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="border border-gray-200 rounded-xl p-4">
                                            <div class="text-xs uppercase tracking-wider text-gray-400 font-bold mb-3">
                                                Type d’adresse
                                            </div>
                                            <select wire:model.live="pickupType"
                                                class="block w-full h-11 bg-white border border-gray-300 rounded-xl px-3 text-sm focus:ring-1 focus:ring-blue-500 focus:border-blue-500 outline-none">
                                                <option value="domicile">Domicile privé</option>
                                                <option value="pro_quai">Pro avec quai de chargement</option>
                                                <option value="pro_sans_quai">Pro sans quai de chargement</option>
                                                <option value="pro_difficile">Pro accès camion difficile</option>
                                                <option value="salon">Salon / exposition</option>
                                                <option value="port">Zone portuaire</option>
                                                <option value="aeroport">Zone aéroportuaire</option>
                                            </select>
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
                                            <div>
                                                <input type="date" wire:model.live="latestDeliveryDate"
                                                    class="block w-full h-11 bg-white border @error('latestDeliveryDate') border-red-500 @else border-gray-300 @enderror rounded-lg px-4 text-sm focus:ring-1 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all">
                                                @error('latestDeliveryDate')
                                                    <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span>
                                                @enderror
                                            </div>
                                            <div>
                                                <input type="time" wire:model.live="latestDeliveryTime"
                                                    class="block w-full h-11 bg-white border @error('latestDeliveryTime') border-red-500 @else border-gray-300 @enderror rounded-lg px-4 text-sm focus:ring-1 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all">
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
                                                <div class="pt-2 mt-2 border-t border-gray-100">
                                                    <label class="flex items-center space-x-2 cursor-pointer">
                                                        <input type="checkbox" wire:model.live="deliveryNotify"
                                                            class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                                        <span class="text-sm font-bold text-blue-600 uppercase tracking-tight">Appeler avant passage</span>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="border border-gray-200 rounded-xl p-4">
                                            <div class="text-xs uppercase tracking-wider text-gray-400 font-bold mb-3">
                                                Type d’adresse
                                            </div>
                                            <select wire:model.live="deliveryType"
                                                class="block w-full h-11 bg-white border border-gray-300 rounded-xl px-3 text-sm focus:ring-1 focus:ring-blue-500 focus:border-blue-500 outline-none">
                                                <option value="domicile">Domicile privé</option>
                                                <option value="pro_quai">Pro avec quai de chargement</option>
                                                <option value="pro_sans_quai">Pro sans quai de chargement</option>
                                                <option value="pro_difficile">Pro accès camion difficile</option>
                                                <option value="salon">Salon / exposition</option>
                                                <option value="port">Zone portuaire</option>
                                                <option value="aeroport">Zone aéroportuaire</option>
                                            </select>
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
