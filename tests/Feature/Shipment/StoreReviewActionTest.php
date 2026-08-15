<?php

use App\Models\Review;
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

function createCompletedShipmentWithAcceptedBid(): array
{
    $shipper = User::factory()->create();
    $shipper->assignRole('shipper');

    $carrier = User::factory()->create();
    $carrier->assignRole('carrier');

    $shipment = Shipment::factory()->create([
        'user_id' => $shipper->id,
        'status' => 'completed',
    ]);

    $bid = ShipmentBid::factory()->create([
        'shipment_id' => $shipment->id,
        'user_id' => $carrier->id,
        'status' => 'accepted',
    ]);

    return compact('shipper', 'carrier', 'shipment', 'bid');
}

// NOTE: app/Http/Controllers/Shipment/Actions/StoreReviewAction.php never
// checks $request->expectsJson() on any branch (success or error) — unlike
// every sibling action in this folder, it always returns back()->with(...).
// Asserting the actual redirect-based behavior below rather than JSON.

it('lets the shipment owner leave a review for the accepted carrier once completed', function () {
    ['shipper' => $shipper, 'carrier' => $carrier, 'shipment' => $shipment] = createCompletedShipmentWithAcceptedBid();

    $response = $this->actingAs($shipper)->post(route('transport-firm-bid.store-review', $shipment), [
        'rating' => 5,
        'comment' => 'Great service!',
    ]);

    $response->assertRedirect();
    $response->assertSessionHas('success', 'Merci pour votre avis !');

    $review = Review::sole();
    expect($review->shipment_id)->toBe($shipment->id);
    expect($review->reviewer_id)->toBe($shipper->id);
    expect($review->reviewee_id)->toBe($carrier->id);
    expect($review->rating)->toBe(5);
    expect($review->comment)->toBe('Great service!');
});

it('rejects a review from anyone other than the shipment owner', function () {
    ['carrier' => $carrier, 'shipment' => $shipment] = createCompletedShipmentWithAcceptedBid();

    $response = $this->actingAs($carrier)->post(route('transport-firm-bid.store-review', $shipment), [
        'rating' => 4,
    ]);

    $response->assertRedirect();
    $response->assertSessionHas('error', 'Seul le client peut laisser un avis.');
    expect(Review::count())->toBe(0);
});

it('rejects a review before the shipment is completed', function () {
    $shipper = User::factory()->create();
    $shipper->assignRole('shipper');

    $shipment = Shipment::factory()->create([
        'user_id' => $shipper->id,
        'status' => 'active',
    ]);

    $response = $this->actingAs($shipper)->post(route('transport-firm-bid.store-review', $shipment), [
        'rating' => 4,
    ]);

    $response->assertRedirect();
    $response->assertSessionHas('error', 'L\'expédition doit être terminée avant de laisser un avis.');
    expect(Review::count())->toBe(0);
});

it('rejects a second review for the same shipment', function () {
    ['shipper' => $shipper, 'carrier' => $carrier, 'shipment' => $shipment] = createCompletedShipmentWithAcceptedBid();

    Review::factory()->create([
        'shipment_id' => $shipment->id,
        'reviewer_id' => $shipper->id,
        'reviewee_id' => $carrier->id,
    ]);

    $response = $this->actingAs($shipper)->post(route('transport-firm-bid.store-review', $shipment), [
        'rating' => 3,
    ]);

    $response->assertRedirect();
    $response->assertSessionHas('error', 'Vous avez déjà laissé un avis pour cette expédition.');
    expect(Review::count())->toBe(1);
});

it('rejects a review when there is no accepted bid to attribute it to', function () {
    $shipper = User::factory()->create();
    $shipper->assignRole('shipper');

    $shipment = Shipment::factory()->create([
        'user_id' => $shipper->id,
        'status' => 'completed',
    ]);

    $response = $this->actingAs($shipper)->post(route('transport-firm-bid.store-review', $shipment), [
        'rating' => 5,
    ]);

    $response->assertRedirect();
    $response->assertSessionHas('error', 'Aucun transporteur accepté trouvé.');
    expect(Review::count())->toBe(0);
});

it('requires a rating between 1 and 5', function () {
    ['shipper' => $shipper, 'shipment' => $shipment] = createCompletedShipmentWithAcceptedBid();

    $response = $this->actingAs($shipper)->post(route('transport-firm-bid.store-review', $shipment), [
        'rating' => 6,
    ]);

    $response->assertSessionHasErrors(['rating']);
    expect(Review::count())->toBe(0);
});

it('requires a rating to be present', function () {
    ['shipper' => $shipper, 'shipment' => $shipment] = createCompletedShipmentWithAcceptedBid();

    $response = $this->actingAs($shipper)->post(route('transport-firm-bid.store-review', $shipment), []);

    $response->assertSessionHasErrors(['rating']);
    expect(Review::count())->toBe(0);
});
