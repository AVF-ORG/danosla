<?php

namespace App\Livewire;

use Livewire\Component;

use App\Models\Shipment;

class ShippingLotForm extends Component
{
    public ?Shipment $shipmentRecord = null;

    // Submission State
    public $isSubmitted = false;

    public function mount(Shipment $shipmentRecord = null)
    {
        if ($shipmentRecord && $shipmentRecord->exists) {
            $this->shipmentRecord = $shipmentRecord;

            $this->description = $shipmentRecord->description;
            $this->comment = $shipmentRecord->comment;
            $this->pickupAddress = $shipmentRecord->pickup_address;
            $this->pickupOptions = $shipmentRecord->pickup_options ?? [];
            $this->deliveryAddress = $shipmentRecord->delivery_address;
            $this->deliveryOptions = $shipmentRecord->delivery_options ?? [];
            $this->latestPickupDate = $shipmentRecord->latest_pickup_date ? \Carbon\Carbon::parse($shipmentRecord->latest_pickup_date)->format('Y-m-d') : null;
            $this->latestDeliveryDate = $shipmentRecord->latest_delivery_date ? \Carbon\Carbon::parse($shipmentRecord->latest_delivery_date)->format('Y-m-d') : null;
            $this->validityDate = $shipmentRecord->validity_date ? \Carbon\Carbon::parse($shipmentRecord->validity_date)->format('Y-m-d') : null;
            $this->validityTime = $shipmentRecord->validity_date ? \Carbon\Carbon::parse($shipmentRecord->validity_date)->format('H:i') : null;
            $this->deliveryPrice = $shipmentRecord->delivery_price;
            $this->totalValue = $shipmentRecord->total_value;

            $reqs = $shipmentRecord->requirements ?? [];
            $this->latestPickupTime = $shipmentRecord->latest_pickup_time ?? ($reqs['latestPickupTime'] ?? '');
            $this->latestDeliveryTime = $shipmentRecord->latest_delivery_time ?? ($reqs['latestDeliveryTime'] ?? '');
            $this->pickupNotify = $reqs['pickupNotify'] ?? false;
            $this->pickupNotifyTime = $shipmentRecord->pickup_notify_time ?? ($reqs['pickupNotifyTime'] ?? '');
            $this->deliveryNotify = $reqs['deliveryNotify'] ?? false;
            $this->deliveryNotifyTime = $shipmentRecord->delivery_notify_time ?? ($reqs['deliveryNotifyTime'] ?? '');

            $this->isDangerous = $reqs['isDangerous'] ?? false;
            $this->dangerousGoodsDescription = $reqs['dangerousGoodsDescription'] ?? '';
            $this->isUrgent = $reqs['isUrgent'] ?? false;
            $this->urgentDescription = $reqs['urgentDescription'] ?? '';
            $this->hasInsuranceOption = $reqs['hasInsuranceOption'] ?? false;
            $this->insuranceDescription = $reqs['insuranceDescription'] ?? '';
            $this->hasSpecialHandlingInstructions = $reqs['hasSpecialHandlingInstructions'] ?? false;
            $this->specialHandlingDescription = $reqs['specialHandlingDescription'] ?? '';
            $this->needsTemperatureControlledTransport = $reqs['needsTemperatureControlledTransport'] ?? false;
            $this->temperatureControlledDescription = $reqs['temperatureControlledDescription'] ?? '';
            $this->hasFragileGoods = $reqs['hasFragileGoods'] ?? false;
            $this->fragileGoodsDescription = $reqs['fragileGoodsDescription'] ?? '';
            $this->hasAdditionalRequirements = $reqs['hasAdditionalRequirements'] ?? false;
            $this->additionalRequirementsDescription = $reqs['additionalRequirementsDescription'] ?? '';

            $shipmentRecord->load('lots');
            $this->lots = $shipmentRecord->lots->map(function ($lot) {
                return [
                    'type' => $lot->type,
                    'quantity' => $lot->quantity,
                    'weight' => $lot->weight,
                    'length' => $lot->length,
                    'width' => $lot->width,
                    'height' => $lot->height,
                    'palette_type' => $lot->palette_type,
                    'container_type' => $lot->container_type,
                    'brand' => $lot->brand,
                    'model' => $lot->model,
                    'vehicle_weight' => $lot->vehicle_weight,
                    'is_rolling' => $lot->is_rolling,
                    'is_stackable' => $lot->is_stackable,
                    'volume' => $lot->volume,
                ];
            })->toArray();

            $this->calculateGlobalTotals();
            // Optional: reset lot builder to defaults
            $this->resetLotFields();

            if (count($this->lots) > 0) {
                $this->editLot(0);
            }
        }
    }

    public $lots = [];

    public $editingLotIndex = null;

    // Step 2: Current Lot fields
    public $type = 'colis';

    public $quantity = 1;

    public $weight = '';

    public $length = '';

    public $width = '';

    public $height = '';

    public $palette_type = '80_120';

    public $container_type = '20_dry';

    public $brand = '';

    public $model = '';

    public $vehicle_weight = '1t';

    public $is_rolling = true;

    public $is_stackable = true;

    // Step 2: Global Options (Apply to all lots)
    public $description = '';

    public $comment = '';

    public $hasInsurance = false;

    public $totalValue = '';

    public $total_volume = 0.0;

    public $total_weight = 0.0;

    // Step 3: Itinerary
    public $pickupAddress = '';

    public $pickupOptions = [];

    public $deliveryAddress = '';

    public $deliveryOptions = [];

    // Step 4: Schedule
    public $latestPickupDate = '';

    public $latestPickupTime = '';

    public $latestDeliveryDate = '';

    public $latestDeliveryTime = '';

    public $validityDate = '';
    public $validityTime = '';
    public $deliveryPrice = '';

    // Step: Terms and Conditions / Special requirements

    public $isDangerous = false;

    public $isUrgent = false;

    public $dangerousGoodsDescription = '';

    public $urgentDescription = '';

    public $hasInsuranceOption = false;

    public $insuranceDescription = '';

    public $hasSpecialHandlingInstructions = false;

    public $specialHandlingDescription = '';

    public $needsTemperatureControlledTransport = false;

    public $temperatureControlledDescription = '';

    public $hasFragileGoods = false;

    public $fragileGoodsDescription = '';

    public $hasAdditionalRequirements = false;

    public $additionalRequirementsDescription = '';

    public $palette_dimensions = [
        '80_120' => ['l' => 80, 'L' => 120],
        '100_120' => ['l' => 100, 'L' => 120],
        '60_100' => ['l' => 60, 'L' => 100],
        '60_80' => ['l' => 60, 'L' => 80],
        '120_120' => ['l' => 120, 'L' => 120],
    ];

    public $container_options = [
        '20_dry' => "20' DRY (normal) / 32 m3 / 6 058 x 2 438 x 2 591 mm",
        '40_dry' => "40' DRY (normal) / 64 m3 / 12 192 x 2 438 x 2 591 mm",
        '40_hc' => "40'HC DRY / 76 m3 / 12 192 x 2 438 x 2 896 mm",
        '45_hc' => "45'HC DRY / 86 m3 / 13 556 x 2 352 x 2 698 mm",
        '20_reefer' => "20' REEFER / 28 m3 / 5 456 x 2 294 x 2 273 mm",
        '40_reefer' => "40' REEFER / 68 m3 / 11 584 x 2 294 x 2 557 mm",
        '45_hc_reefer' => "45'HC REEFER / 84 m3 / 13 280 x 2 440 x 2 582 mm",
        '20_open_top' => "20' OPEN TOP / 32 m3 / 5 900 x 2 330 x 2 330 mm",
        '40_open_top' => "40' OPEN TOP / 65 m3 / 12 100 x 2 330 x 2 325 mm",
        '20_flat_rack' => "20' FLAT RACK / 34 m3 / 5 920 x 2 208 x 2 591 mm",
        '40_flat_rack' => "40' FLAT RACK / 73 m3 / 12 054 x 2 350 x 2 591 mm",
        '20_citerne' => "20' CITERNE / 38 m3 / 6 058 x 2 438 x 2 591 mm",
    ];

    public $container_capacities = [
        '20_dry' => 32,
        '40_dry' => 64,
        '40_hc' => 76,
        '45_hc' => 86,
        '20_reefer' => 28,
        '40_reefer' => 68,
        '45_hc_reefer' => 84,
        '20_open_top' => 32,
        '40_open_top' => 65,
        '20_flat_rack' => 34,
        '40_flat_rack' => 73,
        '20_citerne' => 38,
    ];

    protected function rules()
    {
        $rules = [
            // Require at least one lot on final submit:
            'lots' => 'required|array|min:1',

            'description' => 'required|string|min:5',
            'comment' => 'nullable|string|max:2000',

            'pickupAddress' => 'required|string|min:3',
            'deliveryAddress' => 'required|string|min:3',

            'latestPickupDate' => 'required|date',
            'latestPickupTime' => 'required',
            'latestDeliveryDate' => 'required|date|after_or_equal:latestPickupDate',
            'latestDeliveryTime' => 'required',

            'validityDate' => 'required|date',
            'validityTime' => 'required',
            'deliveryPrice' => 'nullable|numeric|min:0',

            'isDangerous' => 'boolean',
            'dangerousGoodsDescription' => 'nullable|string|max:2000',

            'isUrgent' => 'boolean',
            'urgentDescription' => 'nullable|string|max:2000',

            'hasInsuranceOption' => 'boolean',
            'insuranceDescription' => 'nullable|string|max:2000',

            'hasSpecialHandlingInstructions' => 'boolean',
            'specialHandlingDescription' => 'nullable|string|max:2000',

            'needsTemperatureControlledTransport' => 'boolean',
            'temperatureControlledDescription' => 'nullable|string|max:2000',

            'hasFragileGoods' => 'boolean',
            'fragileGoodsDescription' => 'nullable|string|max:2000',

            'hasAdditionalRequirements' => 'boolean',
            'additionalRequirementsDescription' => 'nullable|string|max:2000',
        ];

        return $rules;
    }

    /**
     * ✅ Validate ONLY lot fields when adding/editing a lot
     */
    protected function lotRules(): array
    {
        $rules = [
            'type' => 'required|string',
            'quantity' => 'required|numeric|min:1',
        ];

        if ($this->type === 'vehicule') {
            $rules['brand'] = 'required|string|min:1';
            $rules['model'] = 'required|string|min:1';
            $rules['vehicle_weight'] = 'required|string';
        } elseif ($this->type === 'palette_standard') {
            $rules['palette_type'] = 'required|string';
            $rules['height'] = 'required|numeric|min:1';
            $rules['weight'] = 'required|numeric|min:0.1';
        } elseif ($this->type === 'conteneur_complet') {
            $rules['container_type'] = 'required|string';
            $rules['weight'] = 'required|numeric|min:0.1';
        } else {
            // colis, oeuvre_art, hors_gabarit, etc.
            $rules['length'] = 'required|numeric|min:1';
            $rules['width'] = 'required|numeric|min:1';
            $rules['height'] = 'required|numeric|min:1';
            $rules['weight'] = 'required|numeric|min:0.1';
        }

        return $rules;
    }

    public function addLot()
    {
        $this->validate($this->lotRules());

        $l = $this->length;
        $w = $this->width;
        $h = $this->height;
        $weight_val = $this->weight;

        if ($this->type === 'palette_standard') {
            $dims = $this->palette_dimensions[$this->palette_type] ?? ['l' => 0, 'L' => 0];
            $l = $dims['L'];
            $w = $dims['l'];
        }

        if ($this->type === 'vehicule') {
            $weight_map = [
                '1t' => 1000,
                '1.5t' => 1500,
                '2t' => 2000,
                '3t' => 3000,
            ];
            $weight_val = $weight_map[$this->vehicle_weight] ?? 1000;
        }

        $lotData = [
            'type' => $this->type,
            'quantity' => (int) $this->quantity,
            'weight' => (float) $weight_val,
            'length' => (float) $l,
            'width' => (float) $w,
            'height' => (float) $this->height,
            'palette_type' => $this->palette_type,
            'container_type' => $this->container_type,
            'brand' => $this->brand,
            'model' => $this->model,
            'vehicle_weight' => $this->vehicle_weight,
            'is_rolling' => (bool) $this->is_rolling,
            'is_stackable' => (bool) $this->is_stackable,
            'volume' => (float) $this->current_lot_volume,
        ];

        if ($this->editingLotIndex !== null) {
            $this->lots[$this->editingLotIndex] = $lotData;
            $this->editingLotIndex = null;
        } else {
            $this->lots[] = $lotData;
        }

        $this->resetLotFields();
        $this->calculateGlobalTotals();

        // Clear any "lots required" error after first add
        $this->resetErrorBag('lots');
    }

    public function editLot($index)
    {
        $lot = $this->lots[$index];

        $this->type = $lot['type'];
        $this->quantity = $lot['quantity'];
        $this->weight = $lot['weight'];
        $this->length = $lot['length'];
        $this->width = $lot['width'];
        $this->height = $lot['height'];
        $this->palette_type = $lot['palette_type'];
        $this->container_type = $lot['container_type'];
        $this->brand = $lot['brand'];
        $this->model = $lot['model'];
        $this->vehicle_weight = $lot['vehicle_weight'];
        $this->is_rolling = $lot['is_rolling'];
        $this->is_stackable = $lot['is_stackable'] ?? true;

        $this->editingLotIndex = $index;
    }

    public function removeLot($index)
    {
        unset($this->lots[$index]);
        $this->lots = array_values($this->lots);

        $this->calculateGlobalTotals();
    }

    public function cancelEdit()
    {
        $this->resetLotFields();
        $this->editingLotIndex = null;
        $this->resetErrorBag();
    }

    private function resetLotFields()
    {
        $this->type = 'colis';
        $this->quantity = 1;
        $this->weight = '';
        $this->length = '';
        $this->width = '';
        $this->height = '';
        $this->palette_type = '80_120';
        $this->container_type = '20_dry';
        $this->brand = '';
        $this->model = '';
        $this->vehicle_weight = '1t';
        $this->is_rolling = true;
        $this->is_stackable = true;
    }

    public function calculateGlobalTotals()
    {
        $this->total_volume = 0.0;
        $this->total_weight = 0.0;

        foreach ($this->lots as $lot) {
            $this->total_volume += (float) ($lot['volume'] ?? 0);
            $this->total_weight += ((float) ($lot['weight'] ?? 0) * (int) ($lot['quantity'] ?? 1));
        }
    }

    // Preview volume of one packaging
    public function getUnitVolumeProperty()
    {
        if ($this->type === 'palette_standard') {
            $dims = $this->palette_dimensions[$this->palette_type] ?? ['l' => 0, 'L' => 0];
            $h = (float) ($this->height ?: 0);

            return ($dims['l'] / 100) * ($dims['L'] / 100) * ($h / 100);
        }

        if ($this->type === 'conteneur_complet') {
            return ($this->container_capacities[$this->container_type] ?? 0);
        }

        if ($this->type === 'vehicule') {
            return 0;
        }

        $l = (float) ($this->length ?: 0);
        $w = (float) ($this->width ?: 0);
        $h = (float) ($this->height ?: 0);

        return ($l / 100) * ($w / 100) * ($h / 100);
    }

    // Preview volume of current lot
    public function getCurrentLotVolumeProperty()
    {
        $qty = (int) ($this->quantity ?: 1);
        return $this->unit_volume * $qty;
    }

    public function updatedIsDangerous($value)
    {
        if (! $value) {
            $this->dangerousGoodsDescription = '';
        }
    }

    public function updatedIsUrgent($value)
    {
        if (! $value) {
            $this->urgentDescription = '';
        }
    }

    public function updatedHasInsuranceOption($value)
    {
        if (! $value) {
            $this->insuranceDescription = '';
        }
    }

    public function updatedHasSpecialHandlingInstructions($value)
    {
        if (! $value) {
            $this->specialHandlingDescription = '';
        }
    }

    public function updatedNeedsTemperatureControlledTransport($value)
    {
        if (! $value) {
            $this->temperatureControlledDescription = '';
        }
    }

    public function updatedHasFragileGoods($value)
    {
        if (! $value) {
            $this->fragileGoodsDescription = '';
        }
    }

    public function updatedHasAdditionalRequirements($value)
    {
        if (! $value) {
            $this->additionalRequirementsDescription = '';
        }
    }

    public function submit()
    {
        $this->validate();

        \Illuminate\Support\Facades\DB::transaction(function () {
            $data = [
                'user_id' => auth()->id(),
                'description' => $this->description,
                'comment' => $this->comment,
                'total_value' => (float) $this->totalValue ?: null,
                'total_volume' => (float) $this->total_volume,
                'total_weight' => (float) $this->total_weight,
                'pickup_address' => $this->pickupAddress,
                'pickup_options' => $this->pickupOptions,
                'delivery_address' => $this->deliveryAddress,
                'delivery_options' => $this->deliveryOptions,
                'latest_pickup_date' => $this->latestPickupDate,
                'latest_pickup_time' => $this->latestPickupTime,
                'latest_delivery_date' => $this->latestDeliveryDate,
                'latest_delivery_time' => $this->latestDeliveryTime,
                'validity_date' => ($this->validityDate && $this->validityTime) ? \Carbon\Carbon::parse($this->validityDate . ' ' . $this->validityTime) : null,
                'delivery_price' => $this->deliveryPrice ?: null,
                'requirements' => [
                    'isDangerous' => $this->isDangerous,
                    'dangerousGoodsDescription' => $this->dangerousGoodsDescription,
                    'isUrgent' => $this->isUrgent,
                    'urgentDescription' => $this->urgentDescription,
                    'pickupNotify' => $this->pickupNotify,
                    'deliveryNotify' => $this->deliveryNotify,
                    'hasInsuranceOption' => $this->hasInsuranceOption,
                    'insuranceDescription' => $this->insuranceDescription,
                    'hasSpecialHandlingInstructions' => $this->hasSpecialHandlingInstructions,
                    'specialHandlingDescription' => $this->specialHandlingDescription,
                    'needsTemperatureControlledTransport' => $this->needsTemperatureControlledTransport,
                    'temperatureControlledDescription' => $this->temperatureControlledDescription,
                    'hasFragileGoods' => $this->hasFragileGoods,
                    'fragileGoodsDescription' => $this->fragileGoodsDescription,
                    'hasAdditionalRequirements' => $this->hasAdditionalRequirements,
                    'additionalRequirementsDescription' => $this->additionalRequirementsDescription,
                ],
                'status' => 'pending',
            ];

            if ($this->shipmentRecord && $this->shipmentRecord->exists) {
                $this->shipmentRecord->update($data);
                $this->shipmentRecord->lots()->delete();
                $shipment = $this->shipmentRecord;
            } else {
                $shipment = \App\Models\Shipment::create($data);
            }

            foreach ($this->lots as $lot) {
                $shipment->lots()->create([
                    'type' => $lot['type'],
                    'quantity' => $lot['quantity'],
                    'weight' => $lot['weight'],
                    'length' => $lot['length'],
                    'width' => $lot['width'],
                    'height' => $lot['height'],
                    'palette_type' => $lot['palette_type'],
                    'container_type' => $lot['container_type'],
                    'brand' => $lot['brand'],
                    'model' => $lot['model'],
                    'vehicle_weight' => $lot['vehicle_weight'],
                    'is_rolling' => $lot['is_rolling'],
                    'is_stackable' => $lot['is_stackable'] ?? true,
                    'volume' => $lot['volume'],
                ]);
            }
        });

        $this->isSubmitted = true;
    }

    public function updated($propertyName)
    {
        if ($propertyName === 'type') {
            $this->resetFields();
            $this->resetErrorBag();
        }
    }

    protected function resetFields()
    {
        if ($this->type !== 'vehicule') {
            $this->brand = '';
            $this->model = '';
            $this->is_rolling = true;
        }

        if ($this->type === 'palette_standard') {
            $this->length = '';
            $this->width = '';
        } elseif ($this->type === 'conteneur_complet' || $this->type === 'vehicule') {
            $this->length = '';
            $this->width = '';
            $this->height = '';
        }
    }

    public function render()
    {
        return view('livewire.shipping-lot-form');
    }


 
}
