<?php

use App\Models\Shipment;
use App\Models\ShipmentBid;
use App\Models\User;
use App\Notifications\Shipment\ShipmentStatusChangedNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Spatie\Permission\Models\Role;

uses(RefreshDatabase::class);

beforeEach(function () {
    Role::firstOrCreate(['name' => 'shipper']);
    Role::firstOrCreate(['name' => 'carrier']);
    Role::firstOrCreate(['name' => 'admin']);

    Notification::fake();
});

function createActiveShipmentWithAcceptedBid(): array
{
    $shipper = User::factory()->create();
    $shipper->assignRole('shipper');

    $carrier = User::factory()->create();
    $carrier->assignRole('carrier');

    $shipment = Shipment::factory()->create([
        'user_id' => $shipper->id,
        'status' => 'active',
    ]);

    $bid = ShipmentBid::factory()->create([
        'shipment_id' => $shipment->id,
        'user_id' => $carrier->id,
        'status' => 'accepted',
    ]);

    return compact('shipper', 'carrier', 'shipment', 'bid');
}

it('marks an active shipment as completed and notifies the accepted carrier', function () {
    ['shipper' => $shipper, 'carrier' => $carrier, 'shipment' => $shipment] = createActiveShipmentWithAcceptedBid();

    $response = $this->actingAs($shipper)->postJson(route('transport-firm-bid.complete-shipment', $shipment));

    $response->assertOk()->assertJson([
        'success' => true,
        'shipment_status' => 'completed',
    ]);

    expect($shipment->fresh()->getRawOriginal('status'))->toBe('completed');

    Notification::assertSentTo($carrier, ShipmentStatusChangedNotification::class, fn ($notification) => $notification->type === 'completed');
});

it('allows an admin to complete the shipment', function () {
    ['shipment' => $shipment] = createActiveShipmentWithAcceptedBid();

    $admin = User::factory()->create();
    $admin->assignRole('admin');

    $response = $this->actingAs($admin)->postJson(route('transport-firm-bid.complete-shipment', $shipment));

    $response->assertOk();
    expect($shipment->fresh()->getRawOriginal('status'))->toBe('completed');
});

it('does nothing and sends no notification when no bid was accepted', function () {
    $shipper = User::factory()->create();
    $shipper->assignRole('shipper');

    $shipment = Shipment::factory()->create([
        'user_id' => $shipper->id,
        'status' => 'active',
    ]);

    $response = $this->actingAs($shipper)->postJson(route('transport-firm-bid.complete-shipment', $shipment));

    $response->assertOk();
    expect($shipment->fresh()->getRawOriginal('status'))->toBe('completed');
    Notification::assertNothingSent();
});

it('refuses to complete a shipment that is not active', function () {
    $shipper = User::factory()->create();
    $shipper->assignRole('shipper');

    $shipment = Shipment::factory()->create([
        'user_id' => $shipper->id,
        'status' => 'pending',
        'validity_date' => now()->addMinutes(90),
    ]);

    $response = $this->actingAs($shipper)->postJson(route('transport-firm-bid.complete-shipment', $shipment));

    // NOTE: app/Http/Controllers/Shipment/Actions/CompleteShipmentAction.php:18-20
    // never checks $request->expectsJson() on this branch (unlike the success
    // path just below it), so even an XHR/JSON request gets a redirect with a
    // flashed error instead of a JSON error payload. Asserting actual behavior.
    $response->assertRedirect();
    $response->assertSessionHas('error', 'Seules les expéditions actives peuvent être marquées comme terminées.');

    expect($shipment->fresh()->getRawOriginal('status'))->toBe('pending');
});

it('denies completion by a carrier who is not the shipment owner', function () {
    ['shipment' => $shipment] = createActiveShipmentWithAcceptedBid();

    $carrier = User::factory()->create();
    $carrier->assignRole('carrier');

    $response = $this->actingAs($carrier)->postJson(route('transport-firm-bid.complete-shipment', $shipment));

    $response->assertForbidden();
    expect($shipment->fresh()->getRawOriginal('status'))->toBe('active');
});
