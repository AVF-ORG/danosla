<?php

use App\Models\Country;
use App\Models\Language;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    // The mcamara/laravel-localization middleware stack redirects any
    // request whose path lacks a locale segment (e.g. "dashboard/countries")
    // to a bare "/en" root, unrelated to the Dashboard authorization logic
    // under test. Disable it here so we can exercise the controllers directly.
    $this->withoutMiddleware([
        \Mcamara\LaravelLocalization\Middleware\LaravelLocalizationRedirectFilter::class,
        \Mcamara\LaravelLocalization\Middleware\LocaleSessionRedirect::class,
    ]);

    Language::factory()->create(['code' => 'en', 'is_active' => true]);

    $this->user = User::factory()->create();
    $this->actingAs($this->user);
});

it('renders the country listing', function () {
    Country::factory()->count(3)->create();

    $response = $this->get(route('dashboard.countries.index'));

    $response->assertOk();
    $response->assertViewIs('pages.dashboard.countries.index');
});

it('stores a country on the happy path', function () {
    $response = $this->post(route('dashboard.countries.store'), [
        'name' => ['en' => 'Algeria'],
        'iso2' => 'dz',
        'international_code' => '+213',
    ]);

    $response->assertRedirect(route('dashboard.countries.index'));
    $response->assertSessionHas('success');

    $country = Country::firstWhere('iso2', 'DZ');
    expect($country)->not->toBeNull();
    expect($country->getTranslations('name'))->toBe(['en' => 'Algeria']);
    expect($country->international_code)->toBe('+213');
});

it('fails to store a country with an invalid iso2 length', function () {
    $response = $this->post(route('dashboard.countries.store'), [
        'name' => ['en' => 'Algeria'],
        'iso2' => 'dza',
        'international_code' => '+213',
    ]);

    $response->assertSessionHasErrors('iso2');
    expect(Country::count())->toBe(0);
});

it('updates a country', function () {
    $country = Country::factory()->create(['iso2' => 'FR']);

    $response = $this->put(route('dashboard.countries.update', $country->id), [
        'name' => ['en' => 'France Updated'],
        'iso2' => 'fr',
        'international_code' => '+33',
    ]);

    $response->assertRedirect(route('dashboard.countries.index'));

    $country->refresh();
    expect($country->getTranslations('name'))->toBe(['en' => 'France Updated']);
    expect($country->iso2)->toBe('FR');
    expect($country->international_code)->toBe('+33');
});

it('soft deletes a country', function () {
    $country = Country::factory()->create();

    $response = $this->delete(route('dashboard.countries.destroy', $country->id));

    $response->assertRedirect(route('dashboard.countries.index'));
    expect($country->fresh()->trashed())->toBeTrue();
});

it('restores a soft deleted country', function () {
    $country = Country::factory()->create();
    $country->delete();

    $response = $this->get(route('dashboard.countries.restore', $country->id));

    $response->assertRedirect(route('dashboard.countries.index'));
    expect($country->fresh()->trashed())->toBeFalse();
});
