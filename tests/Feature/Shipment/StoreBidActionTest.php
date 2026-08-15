<?php

use App\Events\Shipment\BidUpdated;
use App\Events\Shipment\NewBidMessage;
use App\Models\BidMessage;
use App\Models\Shipment;
use App\Models\ShipmentBid;
use App\Models\User;
use App\Notifications\Shipment\NewBidNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Notification;
use Spatie\Permission\Models\Role;

uses(RefreshDatabase::class);

beforeEach(function () {
    Role::firstOrCreate(['name' => 'shipper']);
    Role::firstOrCreate(['name' => 'carrier']);
    Role::firstOrCreate(['name' => 'admin']);

    Event::fake([BidUpdated::class, NewBidMessage::class]);
    Notification::fake();
});

function createBiddableShipment(array $attributes = []): array
{
    $shipper = User::factory()->create();
    $shipper->assignRole('shipper');

    $carrier = User::factory()->create();
    $carrier->assignRole('carrier');

    $shipment = Shipment::factory()->create(array_merge([
        'user_id' => $shipper->id,
        'status' => 'pending',
        'validity_date' => now()->addMinutes(90),
    ], $attributes));

    return [$shipper, $carrier, $shipment];
}

it('lets a carrier submit a negotiable bid with full terms', function () {
    [$shipper, $carrier, $shipment] = createBiddableShipment();

    $response = $this->actingAs($carrier)->postJson(route('transport-firm-bid.store-bid', $shipment), [
        'is_negotiable' => true,
        'price' => 250.50,
        'latest_pickup_date' => now()->addDay()->format('Y-m-d'),
        'latest_pickup_time' => '09:00',
        'latest_delivery_date' => now()->addDays(2)->format('Y-m-d'),
        'latest_delivery_time' => '17:00',
        'message' => 'Hello, here is my offer.',
    ]);

    $response->assertOk()->assertJson(['success' => true]);

    $bid = ShipmentBid::sole();
    expect($bid->shipment_id)->toBe($shipment->id);
    expect($bid->user_id)->toBe($carrier->id);
    expect((float) $bid->price)->toBe(250.50);
    expect($bid->is_negotiable)->toBeTrue();
    expect($bid->status)->toBe('pending');

    $message = BidMessage::sole();
    expect($message->bid_id)->toBe($bid->id);
    expect($message->message)->toBe('Hello, here is my offer.');

    Event::assertDispatched(BidUpdated::class, fn ($event) => $event->bid->is($bid));
    Event::assertDispatched(NewBidMessage::class, fn ($event) => $event->message->is($message));
    Notification::assertSentTo($shipper, NewBidNotification::class);
});

it('lets a carrier submit a direct (non-negotiable) request without terms', function () {
    [$shipper, $carrier, $shipment] = createBiddableShipment();

    $response = $this->actingAs($carrier)->postJson(route('transport-firm-bid.store-bid', $shipment), [
        'is_negotiable' => false,
    ]);

    $response->assertOk()->assertJson(['success' => true]);

    $bid = ShipmentBid::sole();
    expect($bid->is_negotiable)->toBeFalse();
    expect($bid->price)->toBeNull();
    expect($bid->latest_pickup_date)->toBeNull();
    expect($bid->latest_pickup_time)->toBeNull();
    expect($bid->latest_delivery_date)->toBeNull();
    expect($bid->latest_delivery_time)->toBeNull();

    expect(BidMessage::count())->toBe(0);
    Event::assertNotDispatched(NewBidMessage::class);
    Event::assertDispatched(BidUpdated::class);
    Notification::assertSentTo($shipper, NewBidNotification::class);
});

it('is idempotent per carrier: posting a second bid updates the existing row instead of creating a new one', function () {
    [$shipper, $carrier, $shipment] = createBiddableShipment();

    $this->actingAs($carrier)->postJson(route('transport-firm-bid.store-bid', $shipment), [
        'is_negotiable' => true,
        'price' => 100,
        'latest_pickup_date' => now()->addDay()->format('Y-m-d'),
        'latest_pickup_time' => '09:00',
        'latest_delivery_date' => now()->addDays(2)->format('Y-m-d'),
        'latest_delivery_time' => '17:00',
    ])->assertOk();

    $this->actingAs($carrier)->postJson(route('transport-firm-bid.store-bid', $shipment), [
        'is_negotiable' => true,
        'price' => 200,
        'latest_pickup_date' => now()->addDay()->format('Y-m-d'),
        'latest_pickup_time' => '09:00',
        'latest_delivery_date' => now()->addDays(2)->format('Y-m-d'),
        'latest_delivery_time' => '17:00',
    ])->assertOk();

    expect(ShipmentBid::count())->toBe(1);
    expect((float) ShipmentBid::sole()->price)->toBe(200.0);
});

it('rejects a bid on an expired shipment, but never reaches the friendly "expired" message (dead code)', function () {
    // NOTE: this is a real discrepancy, not just an inconsistency like the
    // other "blocked" branches tested below.
    //
    // StoreBidAction::__invoke() (app/Http/Controllers/Shipment/Actions/StoreBidAction.php:23-28)
    // implies that posting to an expired shipment returns a 422 JSON payload
    // with message "Cette expédition a expiré." for XHR/JSON clients. In
    // practice this branch is unreachable: StoreBidRequest::authorize()
    // (app/Http/Requests/Shipment/StoreBidRequest.php:15) runs first and
    // delegates to ShipmentBidPolicy::create() (app/Policies/Shipment/ShipmentBidPolicy.php:30),
    // which requires `$shipment->status === 'pending'`. But Shipment::getStatusAttribute()
    // (app/Models/Shipment.php:110-116) turns a raw 'pending' + is_expired shipment
    // into status 'expired', so the policy always denies first with a bare
    // 403 — the controller's own expiry check and its French error message
    // can never execute for this route.
    [$shipper, $carrier, $shipment] = createBiddableShipment(['validity_date' => now()->subMinutes(10)]);

    expect($shipment->is_expired)->toBeTrue();

    $response = $this->actingAs($carrier)->postJson(route('transport-firm-bid.store-bid', $shipment), [
        'is_negotiable' => false,
    ]);

    $response->assertForbidden();

    expect(ShipmentBid::count())->toBe(0);
    Event::assertNotDispatched(BidUpdated::class);
});

it('blocks a negotiable bid once the shipment is outside the negotiation window, even though it can still be demanded', function () {
    // Pending, not expired (can_demand true) but past the 180-minute negotiation window (can_negotiate false).
    [$shipper, $carrier, $shipment] = createBiddableShipment(['validity_date' => now()->addMinutes(240)]);

    expect($shipment->can_demand)->toBeTrue();
    expect($shipment->can_negotiate)->toBeFalse();

    $response = $this->actingAs($carrier)->postJson(route('transport-firm-bid.store-bid', $shipment), [
        'is_negotiable' => true,
        'price' => 100,
        'latest_pickup_date' => now()->addDay()->format('Y-m-d'),
        'latest_pickup_time' => '09:00',
        'latest_delivery_date' => now()->addDays(2)->format('Y-m-d'),
        'latest_delivery_time' => '17:00',
    ]);

    // NOTE: unlike the is_expired branch above, this branch of StoreBidAction
    // (app/Http/Controllers/Shipment/Actions/StoreBidAction.php:30-32) does not
    // check $request->expectsJson() and always redirects back with a flashed
    // error, even for an XHR/JSON request. Asserting the actual behavior here.
    $response->assertRedirect();
    $response->assertSessionHas('error', 'La négociation n\'est pas autorisée pour cette expédition.');

    expect(ShipmentBid::count())->toBe(0);
});

it('denies bidding for a non-carrier user', function () {
    [$shipper, $carrier, $shipment] = createBiddableShipment();

    $response = $this->actingAs($shipper)->postJson(route('transport-firm-bid.store-bid', $shipment), [
        'is_negotiable' => false,
    ]);

    $response->assertForbidden();
    expect(ShipmentBid::count())->toBe(0);
});

it('denies bidding on a non-pending shipment', function () {
    [$shipper, $carrier, $shipment] = createBiddableShipment(['status' => 'active']);

    $response = $this->actingAs($carrier)->postJson(route('transport-firm-bid.store-bid', $shipment), [
        'is_negotiable' => false,
    ]);

    $response->assertForbidden();
    expect(ShipmentBid::count())->toBe(0);
});

it('requires full terms when submitting a negotiable bid', function () {
    [$shipper, $carrier, $shipment] = createBiddableShipment();

    $response = $this->actingAs($carrier)->postJson(route('transport-firm-bid.store-bid', $shipment), [
        'is_negotiable' => true,
    ]);

    $response->assertStatus(422);
    $response->assertJsonValidationErrors(['price', 'latest_pickup_date', 'latest_pickup_time', 'latest_delivery_date', 'latest_delivery_time']);
});
