<?php

use App\Models\Shipment;
use App\Models\ShipmentBid;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;

uses(RefreshDatabase::class);

beforeEach(function () {
    Role::firstOrCreate(['name' => 'shipper']);
    Role::firstOrCreate(['name' => 'carrier']);
    Role::firstOrCreate(['name' => 'admin']);
});

/**
 * Create a shipper-owned shipment with a carrier's bid on it, so tests only
 * need to vary the shipment/bid attributes that drive negotiate().
 */
function createNegotiationTestBid(array $shipmentAttributes = [], array $bidAttributes = []): ShipmentBid
{
    $shipper = User::factory()->create();
    $shipper->assignRole('shipper');

    $carrier = User::factory()->create();
    $carrier->assignRole('carrier');

    $shipment = Shipment::factory()->create(array_merge([
        'user_id' => $shipper->id,
        'status' => 'pending',
    ], $shipmentAttributes));

    return ShipmentBid::factory()->create(array_merge([
        'shipment_id' => $shipment->id,
        'user_id' => $carrier->id,
        'is_negotiable' => true,
    ], $bidAttributes));
}

it('allows negotiation once the shipment is active, regardless of the deadline', function () {
    $bid = createNegotiationTestBid(['status' => 'active', 'validity_date' => now()->subDay()]);

    expect($bid->user->can('negotiate', $bid))->toBeTrue();
});

it('allows negotiation once the shipment is completed', function () {
    $bid = createNegotiationTestBid(['status' => 'completed', 'validity_date' => now()->subDay()]);

    expect($bid->user->can('negotiate', $bid))->toBeTrue();
});

it('allows negotiation on a pending shipment within the negotiation window', function () {
    $bid = createNegotiationTestBid(['validity_date' => now()->addMinutes(90)], ['is_negotiable' => true]);

    expect($bid->user->can('negotiate', $bid))->toBeTrue();
});

it('denies negotiation on a pending shipment outside the negotiation window', function () {
    $bid = createNegotiationTestBid(['validity_date' => now()->addMinutes(240)], ['is_negotiable' => true]);

    expect($bid->user->can('negotiate', $bid))->toBeFalse();
});

it('denies negotiation for a non-negotiable (direct request) bid on a pending shipment', function () {
    $bid = createNegotiationTestBid(['validity_date' => now()->addMinutes(90)], ['is_negotiable' => false]);

    expect($bid->user->can('negotiate', $bid))->toBeFalse();
});

it('denies negotiation once the shipment deadline has passed, matching Shipment::can_negotiate', function () {
    // Regression test: the policy used to compute this window itself with
    // diffInHours(), which flips sign after the deadline and incorrectly
    // allowed negotiation on expired shipments. It must now agree with
    // Shipment::isWithinNegotiationWindow().
    $bid = createNegotiationTestBid(['validity_date' => now()->subMinutes(10)], ['is_negotiable' => true]);

    expect($bid->shipment->is_expired)->toBeTrue();
    expect($bid->user->can('negotiate', $bid))->toBeFalse();
});

it('denies negotiation for a user unrelated to the bid or shipment', function () {
    $bid = createNegotiationTestBid(['validity_date' => now()->addMinutes(90)], ['is_negotiable' => true]);

    $stranger = User::factory()->create();
    $stranger->assignRole('carrier');

    expect($stranger->can('negotiate', $bid))->toBeFalse();
});
