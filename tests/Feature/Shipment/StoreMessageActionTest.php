<?php

use App\Events\Shipment\NewBidMessage;
use App\Models\BidMessage;
use App\Models\Shipment;
use App\Models\ShipmentBid;
use App\Models\User;
use App\Notifications\Shipment\NewMessageNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Notification;
use Spatie\Permission\Models\Role;

uses(RefreshDatabase::class);

beforeEach(function () {
    Role::firstOrCreate(['name' => 'shipper']);
    Role::firstOrCreate(['name' => 'carrier']);
    Role::firstOrCreate(['name' => 'admin']);

    Event::fake([NewBidMessage::class]);
    Notification::fake();
});

function createNegotiableBidForMessaging(array $shipmentAttributes = [], array $bidAttributes = []): array
{
    $shipper = User::factory()->create();
    $shipper->assignRole('shipper');

    $carrier = User::factory()->create();
    $carrier->assignRole('carrier');

    $shipment = Shipment::factory()->create(array_merge([
        'user_id' => $shipper->id,
        'status' => 'pending',
        'validity_date' => now()->addMinutes(90),
    ], $shipmentAttributes));

    $bid = ShipmentBid::factory()->create(array_merge([
        'shipment_id' => $shipment->id,
        'user_id' => $carrier->id,
        'is_negotiable' => true,
        'status' => 'pending',
    ], $bidAttributes));

    return compact('shipper', 'carrier', 'shipment', 'bid');
}

it('lets the carrier send a message to the shipper', function () {
    ['shipper' => $shipper, 'carrier' => $carrier, 'bid' => $bid] = createNegotiableBidForMessaging();

    $response = $this->actingAs($carrier)->postJson(route('transport-firm-bid.store-message', $bid), [
        'message' => 'Can we agree on a lower price?',
    ]);

    $response->assertOk()->assertJson(['success' => true]);

    $message = BidMessage::sole();
    expect($message->bid_id)->toBe($bid->id);
    expect($message->user_id)->toBe($carrier->id);
    expect($message->message)->toBe('Can we agree on a lower price?');

    Event::assertDispatched(NewBidMessage::class, fn ($event) => $event->message->is($message));
    Notification::assertSentTo($shipper, NewMessageNotification::class);
    Notification::assertNotSentTo($carrier, NewMessageNotification::class);
});

it('lets the shipper reply to the carrier', function () {
    ['shipper' => $shipper, 'carrier' => $carrier, 'bid' => $bid] = createNegotiableBidForMessaging();

    $response = $this->actingAs($shipper)->postJson(route('transport-firm-bid.store-message', $bid), [
        'message' => 'I can do 90% of that price.',
    ]);

    $response->assertOk();

    $message = BidMessage::sole();
    expect($message->user_id)->toBe($shipper->id);

    Notification::assertSentTo($carrier, NewMessageNotification::class);
    Notification::assertNotSentTo($shipper, NewMessageNotification::class);
});

it('requires a non-empty message', function () {
    ['carrier' => $carrier, 'bid' => $bid] = createNegotiableBidForMessaging();

    $response = $this->actingAs($carrier)->postJson(route('transport-firm-bid.store-message', $bid), [
        'message' => '',
    ]);

    $response->assertStatus(422)->assertJsonValidationErrors(['message']);
    expect(BidMessage::count())->toBe(0);
});

it('denies messaging for a user unrelated to the bid', function () {
    ['bid' => $bid] = createNegotiableBidForMessaging();

    $stranger = User::factory()->create();
    $stranger->assignRole('carrier');

    $response = $this->actingAs($stranger)->postJson(route('transport-firm-bid.store-message', $bid), [
        'message' => 'Let me in on this deal.',
    ]);

    $response->assertForbidden();
    expect(BidMessage::count())->toBe(0);
});

it('denies messaging on a pending shipment outside the negotiation window for a negotiable bid', function () {
    ['carrier' => $carrier, 'bid' => $bid] = createNegotiableBidForMessaging(['validity_date' => now()->addMinutes(240)]);

    $response = $this->actingAs($carrier)->postJson(route('transport-firm-bid.store-message', $bid), [
        'message' => 'Still there?',
    ]);

    $response->assertForbidden();
    expect(BidMessage::count())->toBe(0);
});

it('allows messaging once the shipment is active, even past the original negotiation window', function () {
    ['shipper' => $shipper, 'carrier' => $carrier, 'bid' => $bid] = createNegotiableBidForMessaging(
        ['status' => 'active', 'validity_date' => now()->subDay()],
        ['status' => 'accepted']
    );

    $response = $this->actingAs($carrier)->postJson(route('transport-firm-bid.store-message', $bid), [
        'message' => 'Coordinating pickup time.',
    ]);

    $response->assertOk();
    expect(BidMessage::count())->toBe(1);
});
