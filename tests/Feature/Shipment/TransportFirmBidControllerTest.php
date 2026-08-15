<?php

use App\Models\Lot;
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

    // In the test process the whole framework (including routes/web.php) boots
    // once, before any HTTP request exists. mcamara/laravel-localization's
    // route-group prefix is normally derived from the current request at boot
    // time, so with no request yet available it registers these routes with
    // no locale segment at all. On a real per-request boot (production) that
    // resolves consistently; here it doesn't, and the package's GET-only
    // LocaleSessionRedirect middleware then tries to redirect every
    // unprefixed GET to a locale-prefixed URL — which for a plain (untranslated)
    // route name resolves to just "/en" and drops the whole path. This is a
    // testing-environment artifact of the localization package, unrelated to
    // the Shipment feature under test, so it's disabled here.
    $this->withoutMiddleware(\Mcamara\LaravelLocalization\Middleware\LocaleSessionRedirect::class);
});

function makeUserWithRole(string $role): User
{
    $user = User::factory()->create();
    $user->assignRole($role);

    return $user;
}

// -----------------------------------------------------------------------
// index()
// -----------------------------------------------------------------------

it('shows carriers pending non-expired shipments and any shipment they have bid on', function () {
    $carrier = makeUserWithRole('carrier');
    $shipper = makeUserWithRole('shipper');

    $pendingVisible = Shipment::factory()->create([
        'user_id' => $shipper->id,
        'status' => 'pending',
        'validity_date' => now()->addDay(),
    ]);

    $pendingNoValidity = Shipment::factory()->create([
        'user_id' => $shipper->id,
        'status' => 'pending',
        'validity_date' => null,
    ]);

    // Pending but expired validity_date -> the status accessor turns this into
    // 'expired', so it should NOT be visible to the carrier via the pending clause.
    $pendingExpired = Shipment::factory()->create([
        'user_id' => $shipper->id,
        'status' => 'pending',
        'validity_date' => now()->subDay(),
    ]);

    // Active shipment the carrier has bid on -> visible via the bids relationship.
    $biddedActive = Shipment::factory()->create([
        'user_id' => $shipper->id,
        'status' => 'active',
        'validity_date' => now()->subDay(),
    ]);
    ShipmentBid::factory()->create([
        'shipment_id' => $biddedActive->id,
        'user_id' => $carrier->id,
    ]);

    // Active shipment the carrier has NOT bid on -> should not be visible.
    $unrelatedActive = Shipment::factory()->create([
        'user_id' => $shipper->id,
        'status' => 'active',
        'validity_date' => now()->addDay(),
    ]);

    $response = $this->actingAs($carrier)->get(route('transport-firm-bid.index'));

    $response->assertOk();
    $shipments = $response->viewData('shipments');
    $ids = $shipments->pluck('id')->all();

    expect($ids)->toContain($pendingVisible->id);
    expect($ids)->toContain($pendingNoValidity->id);
    expect($ids)->toContain($biddedActive->id);
    expect($ids)->not->toContain($pendingExpired->id);
    expect($ids)->not->toContain($unrelatedActive->id);
});

it('shows shippers only their own shipments regardless of status', function () {
    $shipper = makeUserWithRole('shipper');
    $otherShipper = makeUserWithRole('shipper');

    $own = Shipment::factory()->create(['user_id' => $shipper->id, 'status' => 'completed']);
    $other = Shipment::factory()->create(['user_id' => $otherShipper->id, 'status' => 'pending']);

    $response = $this->actingAs($shipper)->get(route('transport-firm-bid.index'));

    $response->assertOk();
    $ids = $response->viewData('shipments')->pluck('id')->all();

    expect($ids)->toContain($own->id);
    expect($ids)->not->toContain($other->id);
});

// -----------------------------------------------------------------------
// create()
// -----------------------------------------------------------------------

it('renders the create form for an authenticated shipper', function () {
    $shipper = makeUserWithRole('shipper');

    $response = $this->actingAs($shipper)->get(route('transport-firm-bid.create'));

    $response->assertOk();
    $response->assertViewIs('pages.transport-firm-bid.create');
});

// -----------------------------------------------------------------------
// show()
// -----------------------------------------------------------------------

it('allows an admin to view any shipment', function () {
    $admin = makeUserWithRole('admin');
    $shipper = makeUserWithRole('shipper');
    $shipment = Shipment::factory()->create(['user_id' => $shipper->id, 'status' => 'active']);

    $response = $this->actingAs($admin)->get(route('transport-firm-bid.show', $shipment));

    $response->assertOk();
    $response->assertViewIs('pages.transport-firm-bid.show');
});

it('allows a shipper to view only their own shipment', function () {
    $shipper = makeUserWithRole('shipper');
    $otherShipper = makeUserWithRole('shipper');

    $own = Shipment::factory()->create(['user_id' => $shipper->id]);
    $foreign = Shipment::factory()->create(['user_id' => $otherShipper->id]);

    $this->actingAs($shipper)->get(route('transport-firm-bid.show', $own))->assertOk();
    $this->actingAs($shipper)->get(route('transport-firm-bid.show', $foreign))->assertForbidden();
});

it('allows a carrier to view a pending shipment but denies a non-pending shipment they have not bid on', function () {
    $carrier = makeUserWithRole('carrier');
    $shipper = makeUserWithRole('shipper');

    $pending = Shipment::factory()->create(['user_id' => $shipper->id, 'status' => 'pending']);
    $active = Shipment::factory()->create(['user_id' => $shipper->id, 'status' => 'active']);

    $this->actingAs($carrier)->get(route('transport-firm-bid.show', $pending))->assertOk();
    $this->actingAs($carrier)->get(route('transport-firm-bid.show', $active))->assertForbidden();
});

it('allows a carrier to view a non-pending shipment they have bid on', function () {
    $carrier = makeUserWithRole('carrier');
    $shipper = makeUserWithRole('shipper');

    $active = Shipment::factory()->create(['user_id' => $shipper->id, 'status' => 'active']);
    ShipmentBid::factory()->create(['shipment_id' => $active->id, 'user_id' => $carrier->id]);

    $this->actingAs($carrier)->get(route('transport-firm-bid.show', $active))->assertOk();
});

it('loads the lot relationship in the standard view response', function () {
    $shipper = makeUserWithRole('shipper');
    $shipment = Shipment::factory()->create(['user_id' => $shipper->id]);
    Lot::factory()->create(['shipment_id' => $shipment->id]);

    $response = $this->actingAs($shipper)->get(route('transport-firm-bid.show', $shipment));

    $response->assertOk();
    $viewShipment = $response->viewData('shipment');
    expect($viewShipment->relationLoaded('lot'))->toBeTrue();
});

it('returns bid and messages json shape when the request expects json', function () {
    $shipper = makeUserWithRole('shipper');
    $carrier = makeUserWithRole('carrier');
    $shipment = Shipment::factory()->create(['user_id' => $shipper->id, 'status' => 'pending']);
    $bid = ShipmentBid::factory()->create(['shipment_id' => $shipment->id, 'user_id' => $carrier->id]);

    $response = $this->actingAs($shipper)
        ->getJson(route('transport-firm-bid.show', ['shipment' => $shipment, 'bid_id' => $bid->id]));

    $response->assertOk();
    $response->assertJsonStructure(['bid', 'messages']);
    expect($response->json('bid.id'))->toBe($bid->id);
});

// -----------------------------------------------------------------------
// edit()
// -----------------------------------------------------------------------

it('loads the edit form with the shipment and its lot for the owning shipper', function () {
    // TransportFirmBidController::edit() itself performs no authorization check
    // (unlike show()/destroy()), but the edit view mounts the ShippingLotForm
    // Livewire component with the shipment, and that component's mount()
    // enforces ShipmentPolicy::update() (owner or admin) — see
    // app/Livewire/ShippingLotForm.php:20. So editing does end up gated, just
    // one layer down from the controller.
    $shipper = makeUserWithRole('shipper');
    $shipment = Shipment::factory()->create(['user_id' => $shipper->id]);
    Lot::factory()->create(['shipment_id' => $shipment->id]);

    $response = $this->actingAs($shipper)->get(route('transport-firm-bid.edit', $shipment));

    $response->assertOk();
    $response->assertViewIs('pages.transport-firm-bid.edit');
    $viewShipment = $response->viewData('shipment');
    expect($viewShipment->relationLoaded('lot'))->toBeTrue();
    expect($viewShipment->id)->toBe($shipment->id);
});

it('renders a 403 for the edit page when the mounted form denies a non-owning user', function () {
    $shipper = makeUserWithRole('shipper');
    $otherShipper = makeUserWithRole('shipper');
    $shipment = Shipment::factory()->create(['user_id' => $shipper->id]);

    $response = $this->actingAs($otherShipper)->get(route('transport-firm-bid.edit', $shipment));

    $response->assertForbidden();
});

it('allows an admin to load the edit page for a shipment they do not own', function () {
    $admin = makeUserWithRole('admin');
    $shipper = makeUserWithRole('shipper');
    $shipment = Shipment::factory()->create(['user_id' => $shipper->id]);

    $response = $this->actingAs($admin)->get(route('transport-firm-bid.edit', $shipment));

    $response->assertOk();
});

// -----------------------------------------------------------------------
// destroy()
// -----------------------------------------------------------------------

it('allows the owning shipper to delete their shipment and its lot', function () {
    $shipper = makeUserWithRole('shipper');
    $shipment = Shipment::factory()->create(['user_id' => $shipper->id]);
    $lot = Lot::factory()->create(['shipment_id' => $shipment->id]);

    $response = $this->actingAs($shipper)->delete(route('transport-firm-bid.destroy', $shipment));

    $response->assertRedirect(route('transport-firm-bid.index'));
    $this->assertDatabaseMissing('shipments', ['id' => $shipment->id]);
    $this->assertDatabaseMissing('lots', ['id' => $lot->id]);
});

it('allows an admin to delete any shipment', function () {
    $admin = makeUserWithRole('admin');
    $shipper = makeUserWithRole('shipper');
    $shipment = Shipment::factory()->create(['user_id' => $shipper->id]);

    $response = $this->actingAs($admin)->delete(route('transport-firm-bid.destroy', $shipment));

    $response->assertRedirect(route('transport-firm-bid.index'));
    $this->assertDatabaseMissing('shipments', ['id' => $shipment->id]);
});

it('denies deletion by a non-owning shipper and a carrier', function () {
    $shipper = makeUserWithRole('shipper');
    $otherShipper = makeUserWithRole('shipper');
    $carrier = makeUserWithRole('carrier');
    $shipment = Shipment::factory()->create(['user_id' => $shipper->id]);

    $this->actingAs($otherShipper)->delete(route('transport-firm-bid.destroy', $shipment))->assertForbidden();
    $this->actingAs($carrier)->delete(route('transport-firm-bid.destroy', $shipment))->assertForbidden();

    $this->assertDatabaseHas('shipments', ['id' => $shipment->id]);
});
