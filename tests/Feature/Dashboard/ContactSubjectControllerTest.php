<?php

use App\Models\ContactSubject;
use App\Models\Language;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    Language::factory()->create(['code' => 'en', 'is_active' => true]);

    // The app registers dashboard routes without a locale prefix whenever the
    // very first request of the process has no locale segment (see
    // Mcamara\LaravelLocalization\LaravelLocalization::setLocale()), while
    // LocaleSessionRedirect still requires one whenever hideDefaultLocaleInURL
    // is false. Hiding the default locale for these tests keeps that
    // middleware from redirecting every unprefixed request away from the
    // route it just matched.
    config(['laravellocalization.hideDefaultLocaleInURL' => true]);
});

it('guests are redirected away from the contact subjects index', function () {
    $response = $this->get(route('dashboard.contact-subjects.index'));

    $response->assertRedirect();
});

it('lists contact subjects for any authenticated user', function () {
    $user = User::factory()->create();
    ContactSubject::factory()->count(3)->create();

    $response = $this->actingAs($user)->get(route('dashboard.contact-subjects.index'));

    $response->assertOk();
    $response->assertViewIs('pages.dashboard.contact-subjects.index');
});

it('shows the trashed contact subjects when the trash filter is set', function () {
    $user = User::factory()->create();
    $active = ContactSubject::factory()->create(['name' => ['en' => 'Active Subject']]);
    $trashed = ContactSubject::factory()->create(['name' => ['en' => 'Trashed Subject']]);
    $trashed->delete();

    $response = $this->actingAs($user)->get(route('dashboard.contact-subjects.index', ['trash' => 1]));

    $response->assertOk();
    $response->assertViewHas('contactSubjects', function ($contactSubjects) use ($trashed, $active) {
        return $contactSubjects->contains('id', $trashed->id)
            && ! $contactSubjects->contains('id', $active->id);
    });
});

it('shows the create form', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get(route('dashboard.contact-subjects.create'));

    $response->assertOk();
    $response->assertViewIs('pages.dashboard.contact-subjects.create');
});

it('creates a contact subject with a valid translation', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post(route('dashboard.contact-subjects.store'), [
        'name' => ['en' => 'Billing Question'],
        'is_active' => 1,
    ]);

    $response->assertRedirect(route('dashboard.contact-subjects.index'));
    $response->assertSessionHas('success');

    $subject = ContactSubject::first();
    expect($subject)->not->toBeNull();
    expect($subject->getTranslation('name', 'en'))->toBe('Billing Question');
    expect($subject->getTranslation('slug', 'en'))->toBe('billing-question');
    expect((bool) $subject->is_active)->toBeTrue();
});

it('fails to create a contact subject when no active-language translation is provided', function () {
    $user = User::factory()->create();

    // Submitting a translation for a language that isn't active gets filtered out entirely.
    $response = $this->actingAs($user)->post(route('dashboard.contact-subjects.store'), [
        'name' => ['fr' => 'Question de facturation'],
    ]);

    $response->assertSessionHasErrors('name');
    expect(ContactSubject::count())->toBe(0);
});

it('shows a single contact subject', function () {
    $user = User::factory()->create();
    $subject = ContactSubject::factory()->create();

    $response = $this->actingAs($user)->get(route('dashboard.contact-subjects.show', $subject->id));

    $response->assertOk();
    $response->assertViewIs('pages.dashboard.contact-subjects.show');
    $response->assertViewHas('contactSubject', function ($viewSubject) use ($subject) {
        return $viewSubject->id === $subject->id;
    });
});

it('shows the edit form', function () {
    $user = User::factory()->create();
    $subject = ContactSubject::factory()->create();

    $response = $this->actingAs($user)->get(route('dashboard.contact-subjects.edit', $subject->id));

    $response->assertOk();
    $response->assertViewIs('pages.dashboard.contact-subjects.edit');
});

it('updates a contact subject with a valid translation', function () {
    $user = User::factory()->create();
    $subject = ContactSubject::factory()->create(['name' => ['en' => 'Old Name']]);

    $response = $this->actingAs($user)->put(route('dashboard.contact-subjects.update', $subject->id), [
        'name' => ['en' => 'New Name'],
        'is_active' => 0,
    ]);

    $response->assertRedirect(route('dashboard.contact-subjects.index'));
    $subject->refresh();
    expect($subject->getTranslation('name', 'en'))->toBe('New Name');
    expect((bool) $subject->is_active)->toBeFalse();
});

it('fails to update a contact subject when no active-language translation is provided', function () {
    $user = User::factory()->create();
    $subject = ContactSubject::factory()->create(['name' => ['en' => 'Old Name']]);

    $response = $this->actingAs($user)->put(route('dashboard.contact-subjects.update', $subject->id), [
        'name' => ['fr' => 'Nom'],
    ]);

    $response->assertSessionHasErrors('name');
    expect($subject->fresh()->getTranslation('name', 'en'))->toBe('Old Name');
});

it('soft deletes a contact subject', function () {
    $user = User::factory()->create();
    $subject = ContactSubject::factory()->create();

    $response = $this->actingAs($user)->delete(route('dashboard.contact-subjects.destroy', $subject->id));

    $response->assertRedirect(route('dashboard.contact-subjects.index'));
    expect(ContactSubject::find($subject->id))->toBeNull();
    expect(ContactSubject::withTrashed()->find($subject->id))->not->toBeNull();
});

it('restores a soft deleted contact subject', function () {
    $user = User::factory()->create();
    $subject = ContactSubject::factory()->create();
    $subject->delete();

    $response = $this->actingAs($user)->get(route('dashboard.contact-subjects.restore', $subject->id));

    $response->assertRedirect(route('dashboard.contact-subjects.index'));
    expect(ContactSubject::find($subject->id))->not->toBeNull();
});

it('permanently deletes a contact subject', function () {
    $user = User::factory()->create();
    $subject = ContactSubject::factory()->create();
    $subject->delete();

    $response = $this->actingAs($user)->delete(route('dashboard.contact-subjects.forceDelete', $subject->id));

    $response->assertRedirect(route('dashboard.contact-subjects.index'));
    expect(ContactSubject::withTrashed()->find($subject->id))->toBeNull();
});

it('returns json details for a contact subject', function () {
    $user = User::factory()->create();
    $subject = ContactSubject::factory()->create(['name' => ['en' => 'Support'], 'is_active' => true]);

    $response = $this->actingAs($user)->getJson(route('dashboard.contact-subjects.json', $subject));

    $response->assertOk();
    $response->assertJson([
        'id' => $subject->id,
        'is_active' => true,
        'name' => ['en' => 'Support'],
    ]);
});
