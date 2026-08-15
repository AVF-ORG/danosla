<?php

use App\Models\Language;
use App\Models\Region;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    // The mcamara/laravel-localization middleware stack redirects any
    // request whose path lacks a locale segment (e.g. "dashboard/regions")
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

it('renders the region listing', function () {
    Region::factory()->count(3)->create();

    $response = $this->get(route('dashboard.regions.index'));

    $response->assertOk();
    $response->assertViewIs('pages.dashboard.regions.index');
});

it('stores a region on the happy path', function () {
    $response = $this->post(route('dashboard.regions.store'), [
        'name' => ['en' => 'North Africa'],
        'code' => 'naf',
        'description' => ['en' => 'Countries in North Africa'],
    ]);

    $response->assertRedirect(route('dashboard.regions.index'));
    $response->assertSessionHas('success');

    $region = Region::firstWhere('code', 'NAF');
    expect($region)->not->toBeNull();
    expect($region->getTranslations('name'))->toBe(['en' => 'North Africa']);
    expect($region->getTranslations('description'))->toBe(['en' => 'Countries in North Africa']);
});

it('fails to store a region with a duplicate code', function () {
    Region::factory()->create(['code' => 'NAF']);

    $response = $this->post(route('dashboard.regions.store'), [
        'name' => ['en' => 'Another Region'],
        'code' => 'NAF',
    ]);

    $response->assertSessionHasErrors('code');
    expect(Region::count())->toBe(1);
});

it('updates a region', function () {
    $region = Region::factory()->create(['name' => ['en' => 'Old'], 'code' => 'OLD']);

    $response = $this->put(route('dashboard.regions.update', $region->id), [
        'name' => ['en' => 'Updated Region'],
        'code' => 'UPD',
        'description' => ['en' => 'Updated description'],
    ]);

    $response->assertRedirect(route('dashboard.regions.index'));

    $region->refresh();
    expect($region->getTranslations('name'))->toBe(['en' => 'Updated Region']);
    expect($region->code)->toBe('UPD');
});

it('soft deletes a region', function () {
    $region = Region::factory()->create();

    $response = $this->delete(route('dashboard.regions.destroy', $region->id));

    $response->assertRedirect(route('dashboard.regions.index'));
    expect($region->fresh()->trashed())->toBeTrue();
});

it('restores a soft deleted region', function () {
    $region = Region::factory()->create();
    $region->delete();

    $response = $this->get(route('dashboard.regions.restore', $region->id));

    $response->assertRedirect(route('dashboard.regions.index'));
    expect($region->fresh()->trashed())->toBeFalse();
});
