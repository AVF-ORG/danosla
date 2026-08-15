<?php

use App\Models\BidMessage;
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

function createBidWithMessagesFromBothParties(): array
{
    $shipper = User::factory()->create();
    $shipper->assignRole('shipper');

    $carrier = User::factory()->create();
    $carrier->assignRole('carrier');

    $shipment = Shipment::factory()->create([
        'user_id' => $shipper->id,
        'status' => 'pending',
    ]);

    $bid = ShipmentBid::factory()->create([
        'shipment_id' => $shipment->id,
        'user_id' => $carrier->id,
    ]);

    $fromCarrierUnread = BidMessage::factory()->create([
        'bid_id' => $bid->id,
        'user_id' => $carrier->id,
        'is_read' => false,
    ]);

    $fromShipperUnread = BidMessage::factory()->create([
        'bid_id' => $bid->id,
        'user_id' => $shipper->id,
        'is_read' => false,
    ]);

    $fromShipperAlreadyRead = BidMessage::factory()->create([
        'bid_id' => $bid->id,
        'user_id' => $shipper->id,
        'is_read' => true,
    ]);

    return compact('shipper', 'carrier', 'shipment', 'bid', 'fromCarrierUnread', 'fromShipperUnread', 'fromShipperAlreadyRead');
}

it('marks the other party\'s unread messages as read when the carrier reads them', function () {
    [
        'carrier' => $carrier,
        'bid' => $bid,
        'fromCarrierUnread' => $fromCarrierUnread,
        'fromShipperUnread' => $fromShipperUnread,
        'fromShipperAlreadyRead' => $fromShipperAlreadyRead,
    ] = createBidWithMessagesFromBothParties();

    $response = $this->actingAs($carrier)->postJson(route('transport-firm-bid.mark-as-read', $bid));

    $response->assertOk()->assertJson(['success' => true]);

    // The shipper's message gets marked read, the carrier's own message is untouched.
    expect((bool) $fromShipperUnread->fresh()->is_read)->toBeTrue();
    expect((bool) $fromCarrierUnread->fresh()->is_read)->toBeFalse();
    expect((bool) $fromShipperAlreadyRead->fresh()->is_read)->toBeTrue();
});

it('marks the other party\'s unread messages as read when the shipper reads them', function () {
    [
        'shipper' => $shipper,
        'bid' => $bid,
        'fromCarrierUnread' => $fromCarrierUnread,
        'fromShipperUnread' => $fromShipperUnread,
    ] = createBidWithMessagesFromBothParties();

    $response = $this->actingAs($shipper)->postJson(route('transport-firm-bid.mark-as-read', $bid));

    $response->assertOk()->assertJson(['success' => true]);

    expect((bool) $fromCarrierUnread->fresh()->is_read)->toBeTrue();
    expect((bool) $fromShipperUnread->fresh()->is_read)->toBeFalse();
});

it('allows an admin to mark messages as read', function () {
    ['bid' => $bid, 'fromCarrierUnread' => $fromCarrierUnread] = createBidWithMessagesFromBothParties();

    $admin = User::factory()->create();
    $admin->assignRole('admin');

    $response = $this->actingAs($admin)->postJson(route('transport-firm-bid.mark-as-read', $bid));

    $response->assertOk();
    // The admin isn't a party to the bid, so every unread message (from either side) qualifies as "not this user's".
    expect((bool) $fromCarrierUnread->fresh()->is_read)->toBeTrue();
});

it('denies an unrelated carrier from marking messages as read', function () {
    ['shipment' => $shipment, 'bid' => $bid] = createBidWithMessagesFromBothParties();

    // Make the shipment inaccessible to strangers: not pending, and the stranger has no bid on it.
    $shipment->update(['status' => 'active']);

    $stranger = User::factory()->create();
    $stranger->assignRole('carrier');

    $response = $this->actingAs($stranger)->postJson(route('transport-firm-bid.mark-as-read', $bid));

    $response->assertForbidden();
});
