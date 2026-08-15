<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    // See tests/Feature/Shipment/TransportFirmBidControllerTest.php for why
    // this is disabled: mcamara/laravel-localization's route-group prefix is
    // resolved before any HTTP request exists in the test process, so these
    // routes register without a locale segment. Its GET-only
    // LocaleSessionRedirect middleware then redirects every unprefixed GET to
    // a bare "/en", unrelated to the Dashboard feature under test.
    $this->withoutMiddleware([
        \Mcamara\LaravelLocalization\Middleware\LaravelLocalizationRedirectFilter::class,
        \Mcamara\LaravelLocalization\Middleware\LocaleSessionRedirect::class,
    ]);
});

it('renders the dashboard for an authenticated user', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get(route('dashboard.index'));

    $response->assertOk();
    $response->assertViewIs('pages.dashboard.dashboard.index');
});

it('requires authentication to view the dashboard', function () {
    $response = $this->get(route('dashboard.index'));

    $response->assertRedirect(route('login'));
});
