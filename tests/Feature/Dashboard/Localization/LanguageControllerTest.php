<?php

use App\Models\Language;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->actingAs($this->user);
});

it('displays the languages list on the index page', function () {
    // Real ISO codes are used here (rather than the factory's random fake
    // codes) because this view renders the shared layout's language
    // switcher, which resolves each listed language's code against
    // config('laravellocalization.supportedLocales') — and only en/fr/ar
    // are guaranteed to be present there (see AppServiceProvider's
    // bare-minimum fallback used when no active languages exist yet).
    Language::factory()->create(['code' => 'en']);
    Language::factory()->create(['code' => 'fr']);
    Language::factory()->create(['code' => 'ar']);

    $response = $this->get(route('dashboard.localization.languages.index'));

    $response->assertOk();
    $response->assertViewIs('pages.dashboard.localization.languages.index');
    $response->assertViewHas('languages', fn ($languages) => $languages->total() === 3);
});

it('renders the create language form', function () {
    $response = $this->get(route('dashboard.localization.languages.create'));

    $response->assertOk();
    $response->assertViewIs('pages.dashboard.localization.languages.create');
});

it('creates a language, persists it and redirects to the index with a success message', function () {
    $response = $this->post(route('dashboard.localization.languages.store'), [
        'code' => 'xx',
        'name' => 'Xhosa',
        'is_active' => '1',
    ]);

    $this->assertDatabaseHas('languages', [
        'code' => 'xx',
        'name' => 'Xhosa',
        'is_active' => 1,
    ]);

    // NOTE: this assertion currently fails — see summary/bug report.
    $response->assertRedirect(route('dashboard.localization.languages.index'));
    $response->assertSessionHas('success');
});

it('casts is_active to false when the checkbox is omitted on store', function () {
    $this->post(route('dashboard.localization.languages.store'), [
        'code' => 'yy',
        'name' => 'Yiddish',
    ]);

    $this->assertDatabaseHas('languages', [
        'code' => 'yy',
        'is_active' => 0,
    ]);
});

it('fails validation when code is missing on store', function () {
    $response = $this->post(route('dashboard.localization.languages.store'), [
        'name' => 'Test Language',
    ]);

    $response->assertSessionHasErrors('code');
    $this->assertDatabaseMissing('languages', ['name' => 'Test Language']);
});

it('fails validation when code exceeds the max length on store', function () {
    $response = $this->post(route('dashboard.localization.languages.store'), [
        'code' => str_repeat('a', 11),
        'name' => 'Test Language',
    ]);

    $response->assertSessionHasErrors('code');
});

it('fails validation when code is not unique on store', function () {
    Language::factory()->create(['code' => 'en']);

    $response = $this->post(route('dashboard.localization.languages.store'), [
        'code' => 'en',
        'name' => 'English again',
    ]);

    $response->assertSessionHasErrors('code');
});

it('fails validation when name is missing on store', function () {
    $response = $this->post(route('dashboard.localization.languages.store'), [
        'code' => 'zz',
    ]);

    $response->assertSessionHasErrors('name');
});

it('renders the edit form with the existing language', function () {
    $language = Language::factory()->create(['code' => 'fr', 'name' => 'French']);

    $response = $this->get(route('dashboard.localization.languages.edit', $language));

    $response->assertOk();
    $response->assertViewIs('pages.dashboard.localization.languages.edit');
    $response->assertViewHas('language', fn ($viewLanguage) => $viewLanguage->is($language));
});

it('updates a language, persists the changes and redirects to the index with a success message', function () {
    $language = Language::factory()->create([
        'code' => 'en',
        'name' => 'English',
        'is_active' => true,
    ]);

    $response = $this->put(route('dashboard.localization.languages.update', $language), [
        'code' => 'en',
        'name' => 'English (US)',
        'is_active' => '0',
    ]);

    $this->assertDatabaseHas('languages', [
        'id' => $language->id,
        'name' => 'English (US)',
        'is_active' => 0,
    ]);

    // NOTE: this assertion currently fails — see summary/bug report.
    $response->assertRedirect(route('dashboard.localization.languages.index'));
    $response->assertSessionHas('success');
});

it('allows updating a language while keeping its own code', function () {
    $language = Language::factory()->create(['code' => 'en', 'name' => 'English']);

    $this->put(route('dashboard.localization.languages.update', $language), [
        'code' => 'en',
        'name' => 'English updated',
        'is_active' => '1',
    ]);

    $this->assertDatabaseHas('languages', [
        'id' => $language->id,
        'code' => 'en',
        'name' => 'English updated',
    ]);
});

it('fails update validation when code duplicates another language', function () {
    Language::factory()->create(['code' => 'en']);
    $french = Language::factory()->create(['code' => 'fr', 'name' => 'French']);

    $response = $this->put(route('dashboard.localization.languages.update', $french), [
        'code' => 'en',
        'name' => 'French',
    ]);

    $response->assertSessionHasErrors('code');
    $this->assertDatabaseHas('languages', ['id' => $french->id, 'code' => 'fr']);
});

it('fails update validation when name is missing', function () {
    $language = Language::factory()->create();

    $response = $this->put(route('dashboard.localization.languages.update', $language), [
        'code' => $language->code,
    ]);

    $response->assertSessionHasErrors('name');
});

it('soft deletes a language and redirects to the index with a success message', function () {
    $language = Language::factory()->create();

    $response = $this->delete(route('dashboard.localization.languages.destroy', $language));

    $this->assertSoftDeleted('languages', ['id' => $language->id]);

    // NOTE: this assertion currently fails — see summary/bug report.
    $response->assertRedirect(route('dashboard.localization.languages.index'));
    $response->assertSessionHas('success');
});
