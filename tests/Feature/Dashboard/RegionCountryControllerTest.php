<?php

use App\Models\Country;
use App\Models\Language;
use App\Models\Region;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    // The mcamara/laravel-localization middleware stack redirects any
    // request whose path lacks a locale segment (e.g. "dashboard/region-countries")
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

it('renders the region-country listing', function () {
    $region = Region::factory()->create();
    $countries = Country::factory()->count(2)->create();
    $region->countries()->attach($countries->pluck('id'));

    $response = $this->get(route('dashboard.region-countries.index'));

    $response->assertOk();
    $response->assertViewIs('pages.dashboard.region-countries.index');
});

it('assigns countries to a region on the happy path', function () {
    $region = Region::factory()->create();
    $countries = Country::factory()->count(2)->create();

    $response = $this->post(route('dashboard.region-countries.store'), [
        'region_id' => $region->id,
        'country_ids' => $countries->pluck('id')->toArray(),
    ]);

    $response->assertRedirect(route('dashboard.region-countries.index'));
    $response->assertSessionHas('success');

    expect($region->countries()->pluck('countries.id')->sort()->values()->toArray())
        ->toBe($countries->pluck('id')->sort()->values()->toArray());
});

it('fails to assign countries when the region does not exist', function () {
    $countries = Country::factory()->count(2)->create();

    $response = $this->post(route('dashboard.region-countries.store'), [
        'region_id' => 9999,
        'country_ids' => $countries->pluck('id')->toArray(),
    ]);

    $response->assertSessionHasErrors('region_id');
});

it('removes a country from a region', function () {
    $region = Region::factory()->create();
    $country = Country::factory()->create();
    $region->countries()->attach($country->id);

    $response = $this->delete(route('dashboard.region-countries.destroy', [
        'regionId' => $region->id,
        'countryId' => $country->id,
    ]));

    $response->assertRedirect(route('dashboard.region-countries.index'));
    expect($region->countries()->where('countries.id', $country->id)->exists())->toBeFalse();
});
