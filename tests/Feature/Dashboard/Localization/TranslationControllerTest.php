<?php

use App\Models\Language;
use App\Models\Translation;
use App\Models\TranslationKey;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->actingAs($this->user);
});

it('displays the translations list on the index page', function () {
    Translation::factory()->count(3)->create();

    $response = $this->get(route('dashboard.localization.translations.index'));

    $response->assertOk();
    $response->assertViewIs('pages.dashboard.localization.translations.index');
    $response->assertViewHas('translations', fn ($translations) => $translations->total() === 3);
});

it('filters the translations index by the q search term matching the key', function () {
    $matching = TranslationKey::factory()->create(['key' => 'dashboard.title']);
    $other = TranslationKey::factory()->create(['key' => 'auth.login']);
    Translation::factory()->create(['translation_key_id' => $matching->id]);
    Translation::factory()->create(['translation_key_id' => $other->id]);

    $response = $this->get(route('dashboard.localization.translations.index', ['q' => 'dashboard']));

    $response->assertOk();
    $response->assertViewHas('translations', fn ($translations) => $translations->total() === 1);
});

it('renders the edit form for a translation key with only active languages', function () {
    $translationKey = TranslationKey::factory()->create();
    $activeOne = Language::factory()->create(['code' => 'en', 'is_active' => true]);
    $activeTwo = Language::factory()->create(['code' => 'fr', 'is_active' => true]);
    Language::factory()->create(['code' => 'de', 'is_active' => false]);

    $response = $this->get(route('dashboard.localization.translations.edit', $translationKey));

    $response->assertOk();
    $response->assertViewIs('pages.dashboard.localization.translations.edit');
    $response->assertViewHas('languages', function ($languages) use ($activeOne, $activeTwo) {
        return $languages->count() === 2
            && $languages->pluck('id')->sort()->values()->all() === collect([$activeOne->id, $activeTwo->id])->sort()->values()->all();
    });
});

it('creates new translation rows for languages missing a value and redirects with a success message', function () {
    $translationKey = TranslationKey::factory()->create();
    $english = Language::factory()->create(['is_active' => true]);
    $french = Language::factory()->create(['is_active' => true]);

    $response = $this->put(route('dashboard.localization.translations.update', $translationKey), [
        'values' => [
            $english->id => 'Hello',
            $french->id => 'Bonjour',
        ],
    ]);

    $this->assertDatabaseHas('translations', [
        'translation_key_id' => $translationKey->id,
        'language_id' => $english->id,
        'value' => 'Hello',
    ]);
    $this->assertDatabaseHas('translations', [
        'translation_key_id' => $translationKey->id,
        'language_id' => $french->id,
        'value' => 'Bonjour',
    ]);

    // NOTE: this assertion currently fails — see summary/bug report.
    $response->assertRedirect(route('dashboard.localization.translations.edit', $translationKey));
    $response->assertSessionHas('success');
});

it('updates an existing translation value instead of duplicating the row', function () {
    $translationKey = TranslationKey::factory()->create();
    $language = Language::factory()->create(['is_active' => true]);
    Translation::factory()->create([
        'translation_key_id' => $translationKey->id,
        'language_id' => $language->id,
        'value' => 'Old value',
    ]);

    $this->put(route('dashboard.localization.translations.update', $translationKey), [
        'values' => [
            $language->id => 'New value',
        ],
    ]);

    $this->assertDatabaseHas('translations', [
        'translation_key_id' => $translationKey->id,
        'language_id' => $language->id,
        'value' => 'New value',
    ]);
    $this->assertDatabaseCount('translations', 1);
});

it('allows a null translation value for a language', function () {
    $translationKey = TranslationKey::factory()->create();
    $language = Language::factory()->create(['is_active' => true]);

    $this->put(route('dashboard.localization.translations.update', $translationKey), [
        'values' => [
            $language->id => null,
        ],
    ]);

    $this->assertDatabaseHas('translations', [
        'translation_key_id' => $translationKey->id,
        'language_id' => $language->id,
        'value' => null,
    ]);
});

it('silently skips language ids that do not correspond to an existing language', function () {
    $translationKey = TranslationKey::factory()->create();
    $ghostLanguageId = 999999;

    $this->put(route('dashboard.localization.translations.update', $translationKey), [
        'values' => [
            $ghostLanguageId => 'Ghost value',
        ],
    ]);

    $this->assertDatabaseMissing('translations', [
        'translation_key_id' => $translationKey->id,
    ]);
});

it('fails validation when values is missing on update', function () {
    $translationKey = TranslationKey::factory()->create();

    $response = $this->put(route('dashboard.localization.translations.update', $translationKey), []);

    $response->assertSessionHasErrors('values');
});

it('fails validation when values is not an array on update', function () {
    $translationKey = TranslationKey::factory()->create();

    $response = $this->put(route('dashboard.localization.translations.update', $translationKey), [
        'values' => 'not-an-array',
    ]);

    $response->assertSessionHasErrors('values');
});
