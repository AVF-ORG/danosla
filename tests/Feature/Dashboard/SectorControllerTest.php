<?php

use App\Models\Language;
use App\Models\Sector;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    // The mcamara/laravel-localization middleware stack redirects any
    // request whose path lacks a locale segment (e.g. "dashboard/sectors")
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

it('renders the sector listing', function () {
    Sector::factory()->count(3)->create();

    $response = $this->get(route('dashboard.sectors.index'));

    $response->assertOk();
    $response->assertViewIs('pages.dashboard.sectors.index');
});

it('stores a sector on the happy path', function () {
    $response = $this->post(route('dashboard.sectors.store'), [
        'name' => ['en' => 'Logistics'],
        'is_active' => true,
    ]);

    $response->assertRedirect(route('dashboard.sectors.index'));
    $response->assertSessionHas('success');

    $sector = Sector::firstWhere('slug->en', 'logistics');
    expect($sector)->not->toBeNull();
    expect($sector->getTranslations('name'))->toBe(['en' => 'Logistics']);
    expect((bool) $sector->is_active)->toBeTrue();
});

it('fails to store a sector without any translation', function () {
    $response = $this->post(route('dashboard.sectors.store'), [
        'name' => [],
    ]);

    $response->assertSessionHasErrors('name');
    expect(Sector::count())->toBe(0);
});

it('updates a sector', function () {
    $sector = Sector::factory()->create(['name' => ['en' => 'Old Name']]);

    $response = $this->put(route('dashboard.sectors.update', $sector->id), [
        'name' => ['en' => 'New Name'],
        'is_active' => false,
    ]);

    $response->assertRedirect(route('dashboard.sectors.index'));

    $sector->refresh();
    expect($sector->getTranslations('name'))->toBe(['en' => 'New Name']);
    expect((bool) $sector->is_active)->toBeFalse();
});

it('soft deletes a sector', function () {
    $sector = Sector::factory()->create();

    $response = $this->delete(route('dashboard.sectors.destroy', $sector->id));

    $response->assertRedirect(route('dashboard.sectors.index'));
    expect($sector->fresh()->trashed())->toBeTrue();
});

it('restores a soft deleted sector', function () {
    $sector = Sector::factory()->create();
    $sector->delete();

    $response = $this->get(route('dashboard.sectors.restore', $sector->id));

    $response->assertRedirect(route('dashboard.sectors.index'));
    expect($sector->fresh()->trashed())->toBeFalse();
});

it('permanently deletes a sector', function () {
    $sector = Sector::factory()->create();
    $sector->delete();

    $response = $this->delete(route('dashboard.sectors.forceDelete', $sector->id));

    $response->assertRedirect(route('dashboard.sectors.index'));
    expect(Sector::withTrashed()->find($sector->id))->toBeNull();
});

it('returns sector data as json', function () {
    $sector = Sector::factory()->create(['name' => ['en' => 'Trade'], 'is_active' => true]);

    $response = $this->get(route('dashboard.sectors.json', $sector->id));

    $response->assertOk();
    $response->assertJson([
        'id' => $sector->id,
        'is_active' => true,
        'name' => ['en' => 'Trade'],
    ]);
});
