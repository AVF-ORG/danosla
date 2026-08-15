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
    // a bare "/en", unrelated to the static pages under test.
    $this->withoutMiddleware([
        \Mcamara\LaravelLocalization\Middleware\LaravelLocalizationRedirectFilter::class,
        \Mcamara\LaravelLocalization\Middleware\LocaleSessionRedirect::class,
    ]);
});

it('renders the landing page for a guest', function () {
    $this->get(route('landing'))->assertOk();
});

it('renders the calendar page for an authenticated user', function () {
    $user = User::factory()->create();

    $this->actingAs($user)->get(route('calendar'))->assertOk();
});

it('renders each static error page', function (string $routeName) {
    $this->get(route($routeName))->assertOk();
})->with([
    'error-403',
    'error-404',
    'error-419',
    'error-429',
    'error-500',
    'error-503',
]);
