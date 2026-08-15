<?php

use App\Events\Shipment\BidUpdated;
use App\Models\Shipment;
use App\Models\ShipmentBid;
use App\Models\User;
use App\Notifications\Shipment\ShipmentStatusChangedNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Notification;
use Spatie\Permission\Models\Role;

uses(RefreshDatabase::class);

beforeEach(function () {
    Role::firstOrCreate(['name' => 'shipper']);
    Role::firstOrCreate(['name' => 'carrier']);
    Role::firstOrCreate(['name' => 'admin']);

    Event::fake([BidUpdated::class]);
    Notification::fake();
});

function createShipmentWithTwoBids(): array
{
    $shipper = User::factory()->create();
    $shipper->assignRole('shipper');

    $carrierOne = User::factory()->create();
    $carrierOne->assignRole('carrier');

    $carrierTwo = User::factory()->create();
    $carrierTwo->assignRole('carrier');

    $shipment = Shipment::factory()->create([
        'user_id' => $shipper->id,
        'status' => 'pending',
    ]);

    $bidOne = ShipmentBid::factory()->create([
        'shipment_id' => $shipment->id,
        'user_id' => $carrierOne->id,
        'status' => 'pending',
    ]);

    $bidTwo = ShipmentBid::factory()->create([
        'shipment_id' => $shipment->id,
        'user_id' => $carrierTwo->id,
        'status' => 'pending',
    ]);

    return compact('shipper', 'carrierOne', 'carrierTwo', 'shipment', 'bidOne', 'bidTwo');
}

it('accepts a bid, rejects the others and activates the shipment', function () {
    ['shipper' => $shipper, 'carrierOne' => $carrierOne, 'carrierTwo' => $carrierTwo, 'shipment' => $shipment, 'bidOne' => $bidOne, 'bidTwo' => $bidTwo] = createShipmentWithTwoBids();

    $response = $this->actingAs($shipper)->postJson(route('transport-firm-bid.accept-bid', $bidOne));

    $response->assertOk()->assertJson([
        'success' => true,
        'shipment_status' => 'active',
    ]);

    expect($bidOne->fresh()->status)->toBe('accepted');
    expect($bidTwo->fresh()->status)->toBe('rejected');
    expect($shipment->fresh()->getRawOriginal('status'))->toBe('active');

    Event::assertDispatched(BidUpdated::class, fn ($event) => $event->bid->is($bidOne));
    Notification::assertSentTo($carrierOne, ShipmentStatusChangedNotification::class, fn ($notification) => $notification->type === 'accepted');
    Notification::assertSentTo($carrierTwo, ShipmentStatusChangedNotification::class, fn ($notification) => $notification->type === 'rejected');
});

it('allows an admin to accept a bid on behalf of the shipment owner', function () {
    ['shipment' => $shipment, 'bidOne' => $bidOne] = createShipmentWithTwoBids();

    $admin = User::factory()->create();
    $admin->assignRole('admin');

    $response = $this->actingAs($admin)->postJson(route('transport-firm-bid.accept-bid', $bidOne));

    $response->assertOk();
    expect($bidOne->fresh()->status)->toBe('accepted');
});

it('denies acceptance by the bidding carrier itself', function () {
    ['carrierOne' => $carrierOne, 'bidOne' => $bidOne] = createShipmentWithTwoBids();

    $response = $this->actingAs($carrierOne)->postJson(route('transport-firm-bid.accept-bid', $bidOne));

    $response->assertForbidden();
    expect($bidOne->fresh()->status)->toBe('pending');
});

it('denies acceptance by an unrelated shipper', function () {
    ['bidOne' => $bidOne] = createShipmentWithTwoBids();

    $stranger = User::factory()->create();
    $stranger->assignRole('shipper');

    $response = $this->actingAs($stranger)->postJson(route('transport-firm-bid.accept-bid', $bidOne));

    $response->assertForbidden();
    expect($bidOne->fresh()->status)->toBe('pending');
});
