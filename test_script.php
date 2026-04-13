<?php

use Livewire\Livewire;
use App\Livewire\ShippingLotForm;
use App\Models\Shipment;

// Clean up old
Shipment::where('description', 'Test Description from Tinker')->delete();

echo PHP_EOL . '>>> Simulating User Filling Out Form via Livewire...' . PHP_EOL;

$component = Livewire::test(ShippingLotForm::class)
    ->set('type', 'colis') // Start with a random type
    ->set('lots', [
        [
            'type' => 'colis',
            'quantity' => 5,
            'weight' => 200,
            'length' => 10,
            'width' => 10,
            'height' => 10,
            'palette_type' => null,
            'container_type' => null,
            'brand' => null,
            'model' => null,
            'vehicle_weight' => null,
            'is_rolling' => false,
            'volume' => 0.001
        ]
    ])
    ->set('total_weight', 200)
    ->set('total_volume', 0.001)
    ->set('description', 'Test Description from Tinker')
    ->set('pickupAddress', '123 Test Street, Paris, France')
    ->set('pickupType', 'port')
    ->set('deliveryAddress', '456 Delivery Ave, Lyon, France')
    ->set('deliveryType', 'airport')
    ->set('latestPickupDate', '2026-12-01')
    ->set('latestDeliveryDate', '2026-12-10')
    ->set('isUrgent', true)
    ->set('urgentDescription', 'Needs to be shipped ASAP!')
    ->call('submit');

echo '>>> Form Submitted! Validating...' . PHP_EOL;

$shipment = Shipment::with('lots')->where('description', 'Test Description from Tinker')->first();

if ($shipment) {
    echo PHP_EOL . '✅ SUCCESS! Data saved correctly!' . PHP_EOL;
    echo 'Shipment Description: ' . $shipment->description . PHP_EOL;
    echo 'Total Lots Extracted: ' . $shipment->lots->count() . PHP_EOL;
    echo 'First Lot Details: ' . $shipment->lots->first()->quantity . 'x ' . $shipment->lots->first()->type . ' weighing ' . $shipment->lots->first()->weight . 'kg' . PHP_EOL;
} else {
    echo PHP_EOL . '❌ ERROR! Shipment was not saved to database.' . PHP_EOL;
}
