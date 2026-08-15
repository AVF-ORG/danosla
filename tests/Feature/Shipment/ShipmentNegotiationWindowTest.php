<?php

use App\Models\Shipment;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

// getCanNegotiateAttribute()/getCanDemandAttribute() key off getRawOriginal('status'),
// which only reflects a value synced from the database — so these shipments must be
// persisted rather than built as transient `new Shipment([...])` instances.

it('is within the negotiation window when the deadline is well within 180 minutes', function () {
    $shipment = Shipment::factory()->create([
        'status' => 'pending',
        'validity_date' => now()->addMinutes(90),
    ]);

    expect($shipment->isWithinNegotiationWindow())->toBeTrue();
    expect($shipment->can_negotiate)->toBeTrue();
});

it('is not within the negotiation window when the deadline is further than 180 minutes away', function () {
    $shipment = Shipment::factory()->create([
        'status' => 'pending',
        'validity_date' => now()->addMinutes(240),
    ]);

    expect($shipment->isWithinNegotiationWindow())->toBeFalse();
    expect($shipment->can_negotiate)->toBeFalse();
    // Still open for a direct request since the deadline hasn't passed.
    expect($shipment->can_demand)->toBeTrue();
});

it('is not within the negotiation window once the deadline has passed', function () {
    $shipment = Shipment::factory()->create([
        'status' => 'pending',
        'validity_date' => now()->subMinutes(10),
    ]);

    expect($shipment->is_expired)->toBeTrue();
    expect($shipment->isWithinNegotiationWindow())->toBeFalse();
    expect($shipment->can_negotiate)->toBeFalse();
    expect($shipment->can_demand)->toBeFalse();
});

it('is never within the negotiation window without a validity date', function () {
    $shipment = Shipment::factory()->create([
        'status' => 'pending',
        'validity_date' => null,
    ]);

    expect($shipment->isWithinNegotiationWindow())->toBeFalse();
    expect($shipment->can_negotiate)->toBeFalse();
    // No deadline at all still allows a direct request.
    expect($shipment->can_demand)->toBeTrue();
});

it('never allows negotiation outside the pending status, even within the window', function () {
    $shipment = Shipment::factory()->create([
        'status' => 'active',
        'validity_date' => now()->addMinutes(90),
    ]);

    expect($shipment->isWithinNegotiationWindow())->toBeTrue();
    expect($shipment->can_negotiate)->toBeFalse();
});
