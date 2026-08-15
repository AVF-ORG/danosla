<?php

use App\Models\Shipment;
use App\Models\ShipmentBid;
use App\Models\User;
use App\Notifications\Shipment\NewBidNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    // See tests/Feature/Shipment/TransportFirmBidControllerTest.php for why
    // this is disabled: mcamara/laravel-localization's route-group prefix is
    // resolved before any HTTP request exists in the test process, so these
    // routes register without a locale segment. Its GET-only
    // LocaleSessionRedirect middleware then redirects every unprefixed GET to
    // a bare "/en", unrelated to the Notification feature under test.
    $this->withoutMiddleware([
        \Mcamara\LaravelLocalization\Middleware\LaravelLocalizationRedirectFilter::class,
        \Mcamara\LaravelLocalization\Middleware\LocaleSessionRedirect::class,
    ]);
});

function notifyUserWithNewBid(User $user): \Illuminate\Notifications\DatabaseNotification
{
    $shipment = Shipment::factory()->create(['user_id' => $user->id]);
    $bid = ShipmentBid::factory()->create(['shipment_id' => $shipment->id]);

    $user->notify(new NewBidNotification($shipment, $bid));

    return $user->notifications()->latest()->first();
}

// -----------------------------------------------------------------------
// index()
// -----------------------------------------------------------------------

it('lists paginated notifications for the authenticated user only', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();

    notifyUserWithNewBid($user);
    notifyUserWithNewBid($user);
    notifyUserWithNewBid($otherUser);

    $response = $this->actingAs($user)->get(route('notifications.index'));

    $response->assertOk();
    $response->assertViewIs('pages.notifications.index');

    $notifications = $response->viewData('notifications');
    expect($notifications->total())->toBe(2);

    foreach ($notifications as $notification) {
        expect($notification->notifiable_id)->toBe($user->id);
    }
});

it('requires authentication to view the notifications index', function () {
    $response = $this->get(route('notifications.index'));

    $response->assertRedirect(route('login'));
});

// -----------------------------------------------------------------------
// markAsRead()
// -----------------------------------------------------------------------

it('marks a single notification as read and redirects to its stored url for a non-json request', function () {
    $user = User::factory()->create();
    $notification = notifyUserWithNewBid($user);

    expect($notification->read_at)->toBeNull();

    $response = $this->actingAs($user)->get(route('notifications.read', ['id' => $notification->id]));

    $response->assertRedirect($notification->data['url']);

    expect($notification->fresh()->read_at)->not->toBeNull();
});

it('marks a single notification as read and returns json for a json request', function () {
    $user = User::factory()->create();
    $notification = notifyUserWithNewBid($user);

    $response = $this->actingAs($user)->getJson(route('notifications.read', ['id' => $notification->id]));

    $response->assertOk();
    $response->assertJson(['success' => true]);

    expect($notification->fresh()->read_at)->not->toBeNull();
});

it('does not allow a user to mark another users notification as read', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    $notification = notifyUserWithNewBid($otherUser);

    $this->actingAs($user)->get(route('notifications.read', ['id' => $notification->id]))
        ->assertNotFound();

    expect($notification->fresh()->read_at)->toBeNull();
});

// -----------------------------------------------------------------------
// markAllAsRead()
// -----------------------------------------------------------------------

it('marks all unread notifications as read for the authenticated user without touching another users notifications', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();

    notifyUserWithNewBid($user);
    notifyUserWithNewBid($user);
    $otherNotification = notifyUserWithNewBid($otherUser);

    $response = $this->actingAs($user)->post(route('notifications.mark-all-read'));

    $response->assertRedirect();

    expect($user->unreadNotifications()->count())->toBe(0);
    expect($user->notifications()->count())->toBe(2);

    expect($otherNotification->fresh()->read_at)->toBeNull();
    expect($otherUser->unreadNotifications()->count())->toBe(1);
});

it('marks all as read and returns json when the request expects json', function () {
    $user = User::factory()->create();
    notifyUserWithNewBid($user);

    $response = $this->actingAs($user)->postJson(route('notifications.mark-all-read'));

    $response->assertOk();
    $response->assertJson(['success' => true]);

    expect($user->unreadNotifications()->count())->toBe(0);
});
