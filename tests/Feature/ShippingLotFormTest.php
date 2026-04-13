<?php

use Livewire\Livewire;
use App\Livewire\ShippingLotForm;
use App\Models\Shipment;
use App\Models\Lot;

it('submits the form successfully and saves to database', function () {
    Livewire::test(ShippingLotForm::class)
        ->set('lots', [
            [
                'type' => 'colis',
                'quantity' => 2,
                'weight' => 50.5,
                'length' => 10,
                'width' => 10,
                'height' => 10,
                'palette_type' => '',
                'container_type' => '',
                'brand' => '',
                'model' => '',
                'vehicle_weight' => '',
                'is_rolling' => false,
                'volume' => 0.001
            ]
        ])
        ->set('total_weight', 101)
        ->set('total_volume', 0.002)
        ->set('description', 'PEST Test Shipping Request')
        ->set('pickupAddress', '123 Fake Street, Paris')
        ->set('pickupType', 'port')
        ->set('deliveryAddress', '456 Delivery Rd, Lyon')
        ->set('deliveryType', 'airport')
        ->set('latestPickupDate', '2026-10-10')
        ->set('latestDeliveryDate', '2026-10-15')
        ->set('isUrgent', true)
        ->set('urgentDescription', 'Get it there fast')
        ->call('submit')
        ->assertSet('isSubmitted', true);

    $this->assertDatabaseHas('shipments', [
        'description' => 'PEST Test Shipping Request',
        'total_weight' => 101,
        'status' => 'pending',
    ]);

    $this->assertDatabaseHas('lots', [
        'type' => 'colis',
        'quantity' => 2,
        'weight' => 50.5,
    ]);
});
