<?php

use App\Models\TranslationKey;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->actingAs($this->user);
});

it('displays the translation keys list on the index page', function () {
    TranslationKey::factory()->count(3)->create();

    $response = $this->get(route('dashboard.localization.keys.index'));

    $response->assertOk();
    $response->assertViewIs('pages.dashboard.localization.keys.index');
    $response->assertViewHas('keys', fn ($keys) => $keys->total() === 3);
});

it('filters the keys index by the q search term', function () {
    TranslationKey::factory()->create(['key' => 'dashboard.title', 'group' => 'ui']);
    TranslationKey::factory()->create(['key' => 'auth.login', 'group' => 'ui']);

    $response = $this->get(route('dashboard.localization.keys.index', ['q' => 'dashboard']));

    $response->assertOk();
    $response->assertViewHas('keys', function ($keys) {
        return $keys->total() === 1 && $keys->first()->key === 'dashboard.title';
    });
});

it('filters the keys index by group', function () {
    TranslationKey::factory()->create(['key' => 'a.key', 'group' => 'messages']);
    TranslationKey::factory()->create(['key' => 'b.key', 'group' => 'validation']);

    $response = $this->get(route('dashboard.localization.keys.index', ['group' => 'validation']));

    $response->assertOk();
    $response->assertViewHas('keys', function ($keys) {
        return $keys->total() === 1 && $keys->first()->group === 'validation';
    });
});

it('renders the create key form', function () {
    $response = $this->get(route('dashboard.localization.keys.create'));

    $response->assertOk();
    $response->assertViewIs('pages.dashboard.localization.keys.create');
});

it('creates a translation key, persists it and redirects to the index with a success message', function () {
    $response = $this->post(route('dashboard.localization.keys.store'), [
        'key' => 'dashboard.welcome',
        'group' => 'ui',
    ]);

    $this->assertDatabaseHas('translation_keys', [
        'key' => 'dashboard.welcome',
        'group' => 'ui',
    ]);

    // NOTE: this assertion currently fails — see summary/bug report.
    $response->assertRedirect(route('dashboard.localization.keys.index'));
    $response->assertSessionHas('success');
});

it('fails validation when key is missing on store', function () {
    $response = $this->post(route('dashboard.localization.keys.store'), [
        'group' => 'ui',
    ]);

    $response->assertSessionHasErrors('key');
});

it('fails validation when key exceeds the max length on store', function () {
    $response = $this->post(route('dashboard.localization.keys.store'), [
        'key' => str_repeat('a', 256),
        'group' => 'ui',
    ]);

    $response->assertSessionHasErrors('key');
});

it('fails validation when key is not unique on store', function () {
    TranslationKey::factory()->create(['key' => 'dashboard.title']);

    $response = $this->post(route('dashboard.localization.keys.store'), [
        'key' => 'dashboard.title',
        'group' => 'ui',
    ]);

    $response->assertSessionHasErrors('key');
});

it('fails validation when group is missing on store', function () {
    $response = $this->post(route('dashboard.localization.keys.store'), [
        'key' => 'dashboard.title',
    ]);

    $response->assertSessionHasErrors('group');
});

it('fails validation when group exceeds the max length on store', function () {
    $response = $this->post(route('dashboard.localization.keys.store'), [
        'key' => 'dashboard.title',
        'group' => str_repeat('a', 101),
    ]);

    $response->assertSessionHasErrors('group');
});

it('renders the edit form with the existing translation key', function () {
    $translationKey = TranslationKey::factory()->create();

    $response = $this->get(route('dashboard.localization.keys.edit', $translationKey));

    $response->assertOk();
    $response->assertViewIs('pages.dashboard.localization.keys.edit');
    $response->assertViewHas('translationKey', fn ($viewKey) => $viewKey->is($translationKey));
});

it('updates a translation key, persists the changes and redirects to the index with a success message', function () {
    $translationKey = TranslationKey::factory()->create(['key' => 'old.key', 'group' => 'ui']);

    $response = $this->put(route('dashboard.localization.keys.update', $translationKey), [
        'key' => 'new.key',
        'group' => 'messages',
    ]);

    $this->assertDatabaseHas('translation_keys', [
        'id' => $translationKey->id,
        'key' => 'new.key',
        'group' => 'messages',
    ]);

    // NOTE: this assertion currently fails — see summary/bug report.
    $response->assertRedirect(route('dashboard.localization.keys.index'));
    $response->assertSessionHas('success');
});

it('allows updating a translation key while keeping its own key value', function () {
    $translationKey = TranslationKey::factory()->create(['key' => 'same.key', 'group' => 'ui']);

    $this->put(route('dashboard.localization.keys.update', $translationKey), [
        'key' => 'same.key',
        'group' => 'validation',
    ]);

    $this->assertDatabaseHas('translation_keys', [
        'id' => $translationKey->id,
        'key' => 'same.key',
        'group' => 'validation',
    ]);
});

it('fails update validation when key duplicates another translation key', function () {
    TranslationKey::factory()->create(['key' => 'existing.key']);
    $other = TranslationKey::factory()->create(['key' => 'other.key']);

    $response = $this->put(route('dashboard.localization.keys.update', $other), [
        'key' => 'existing.key',
        'group' => 'ui',
    ]);

    $response->assertSessionHasErrors('key');
    $this->assertDatabaseHas('translation_keys', ['id' => $other->id, 'key' => 'other.key']);
});

it('fails update validation when group is missing', function () {
    $translationKey = TranslationKey::factory()->create();

    $response = $this->put(route('dashboard.localization.keys.update', $translationKey), [
        'key' => $translationKey->key,
    ]);

    $response->assertSessionHasErrors('group');
});

it('deletes a translation key and redirects to the index with a success message', function () {
    $translationKey = TranslationKey::factory()->create();

    $response = $this->delete(route('dashboard.localization.keys.destroy', $translationKey));

    $this->assertDatabaseMissing('translation_keys', ['id' => $translationKey->id]);

    // NOTE: this assertion currently fails — see summary/bug report.
    $response->assertRedirect(route('dashboard.localization.keys.index'));
    $response->assertSessionHas('success');
});
