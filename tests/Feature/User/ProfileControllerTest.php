<?php

use App\Models\Country;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    // See tests/Feature/Shipment/TransportFirmBidControllerTest.php for why
    // this is disabled: mcamara/laravel-localization's route-group prefix is
    // resolved before any HTTP request exists in the test process, so these
    // routes register without a locale segment. Its GET-only
    // LocaleSessionRedirect middleware then redirects every unprefixed GET to
    // a bare "/en", unrelated to the Profile feature under test.
    $this->withoutMiddleware([
        \Mcamara\LaravelLocalization\Middleware\LaravelLocalizationRedirectFilter::class,
        \Mcamara\LaravelLocalization\Middleware\LocaleSessionRedirect::class,
    ]);
});

// -----------------------------------------------------------------------
// index()
// -----------------------------------------------------------------------

it('renders the profile page for an authenticated user with the default tab', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get(route('profile.index'));

    $response->assertOk();
    $response->assertViewIs('pages.user.profile.index');
    $response->assertViewHas('tab', 'profile');
    $response->assertViewHas('user', fn ($viewUser) => $viewUser->id === $user->id);
});

it('renders the profile page for an authenticated user with an explicit tab', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get(route('profile.index', ['tab' => 'security']));

    $response->assertOk();
    $response->assertViewHas('tab', 'security');
});

it('requires authentication to view the profile page', function () {
    $response = $this->get(route('profile.index'));

    $response->assertRedirect(route('login'));
});

// -----------------------------------------------------------------------
// update()
// -----------------------------------------------------------------------

it('allows a user to update their own profile', function () {
    $user = User::factory()->create(['name' => 'Old Name']);
    $country = Country::factory()->create();

    $response = $this->actingAs($user)->put(route('profile.update', $user), [
        'name' => 'New Name',
        'email' => $user->email,
        'phone' => '0123456789',
        'address' => '123 Main St',
        'website' => 'https://example.com',
        'company_name' => 'Acme Inc',
        'company_number' => 'AC-001',
        'country_id' => $country->id,
    ]);

    $response->assertRedirect(route('profile.index'));
    $response->assertSessionHas('success');

    $this->assertDatabaseHas('users', [
        'id' => $user->id,
        'name' => 'New Name',
        'company_name' => 'Acme Inc',
        'country_id' => $country->id,
    ]);
});

it('forbids a user from updating another users profile even via a crafted request', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create(['name' => 'Untouched']);

    $response = $this->actingAs($user)->put(route('profile.update', $otherUser), [
        'name' => 'Hacked Name',
        'email' => $otherUser->email,
    ]);

    $response->assertForbidden();

    $this->assertDatabaseHas('users', [
        'id' => $otherUser->id,
        'name' => 'Untouched',
    ]);
});

it('does not fail email uniqueness validation when a user resubmits their own unchanged email', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->put(route('profile.update', $user), [
        'name' => $user->name,
        'email' => $user->email,
    ]);

    $response->assertSessionHasNoErrors();
    $response->assertRedirect(route('profile.index'));
});

it('fails email uniqueness validation when a user submits an email already taken by another user', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();

    $response = $this->actingAs($user)->put(route('profile.update', $user), [
        'name' => $user->name,
        'email' => $otherUser->email,
    ]);

    $response->assertSessionHasErrors('email');
});

it('requires authentication to update a profile', function () {
    $user = User::factory()->create();

    $response = $this->put(route('profile.update', $user), [
        'name' => 'New Name',
        'email' => $user->email,
    ]);

    $response->assertRedirect(route('login'));
});
