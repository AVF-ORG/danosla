<?php

use App\Models\Contact;
use App\Models\ContactSubject;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    // The app registers dashboard routes without a locale prefix whenever the
    // very first request of the process has no locale segment (see
    // Mcamara\LaravelLocalization\LaravelLocalization::setLocale()), while
    // LocaleSessionRedirect still requires one whenever hideDefaultLocaleInURL
    // is false. Hiding the default locale for these tests keeps that
    // middleware from redirecting every unprefixed request away from the
    // route it just matched.
    config(['laravellocalization.hideDefaultLocaleInURL' => true]);
});

it('guests are redirected away from the contacts index', function () {
    $response = $this->get(route('dashboard.contacts.index'));

    $response->assertRedirect();
});

it('lists contact messages for any authenticated user', function () {
    $user = User::factory()->create();
    Contact::factory()->count(3)->create();

    $response = $this->actingAs($user)->get(route('dashboard.contacts.index'));

    $response->assertOk();
    $response->assertViewIs('pages.dashboard.contacts.index');
});

it('filters the contact list by subject', function () {
    $user = User::factory()->create();
    $subjectA = ContactSubject::factory()->create();
    $subjectB = ContactSubject::factory()->create();

    $matching = Contact::factory()->create(['contact_subject_id' => $subjectA->id]);
    $other = Contact::factory()->create(['contact_subject_id' => $subjectB->id]);

    $response = $this->actingAs($user)->get(route('dashboard.contacts.index', ['subject_id' => $subjectA->id]));

    $response->assertOk();
    $response->assertViewHas('contacts', function ($contacts) use ($matching, $other) {
        return $contacts->contains('id', $matching->id) && ! $contacts->contains('id', $other->id);
    });
});

it('filters the contact list by replied status', function () {
    $user = User::factory()->create();
    $replied = Contact::factory()->create(['replied_at' => now()]);
    $pending = Contact::factory()->create(['replied_at' => null]);

    $repliedResponse = $this->actingAs($user)->get(route('dashboard.contacts.index', ['status' => 'replied']));
    $repliedResponse->assertViewHas('contacts', function ($contacts) use ($replied, $pending) {
        return $contacts->contains('id', $replied->id) && ! $contacts->contains('id', $pending->id);
    });

    $pendingResponse = $this->actingAs($user)->get(route('dashboard.contacts.index', ['status' => 'pending']));
    $pendingResponse->assertViewHas('contacts', function ($contacts) use ($replied, $pending) {
        return $contacts->contains('id', $pending->id) && ! $contacts->contains('id', $replied->id);
    });
});

it('filters the contact list by search term', function () {
    $user = User::factory()->create();
    $matching = Contact::factory()->create(['name' => 'Jane Searchable Doe']);
    $other = Contact::factory()->create(['name' => 'Someone Else']);

    $response = $this->actingAs($user)->get(route('dashboard.contacts.index', ['search' => 'Searchable']));

    $response->assertOk();
    $response->assertViewHas('contacts', function ($contacts) use ($matching, $other) {
        return $contacts->contains('id', $matching->id) && ! $contacts->contains('id', $other->id);
    });
});

it('shows a single contact message', function () {
    $user = User::factory()->create();
    $contact = Contact::factory()->create();

    $response = $this->actingAs($user)->get(route('dashboard.contacts.show', $contact->id));

    $response->assertOk();
    $response->assertViewIs('pages.dashboard.contacts.show');
    $response->assertViewHas('contact', function ($viewContact) use ($contact) {
        return $viewContact->id === $contact->id;
    });
});

it('records a reply for a contact message', function () {
    $user = User::factory()->create();
    $contact = Contact::factory()->create();

    $response = $this->actingAs($user)->post(route('dashboard.contacts.reply', $contact->id), [
        'reply_content' => 'Thanks for reaching out, we will get back to you.',
    ]);

    $response->assertRedirect(route('dashboard.contacts.show', $contact->id));
    $response->assertSessionHas('success');

    $contact->refresh();
    expect($contact->reply_content)->toBe('Thanks for reaching out, we will get back to you.');
    expect($contact->replied_at)->not->toBeNull();
    expect($contact->replied_by)->toBe($user->id);
});

it('fails to record a reply without reply content', function () {
    $user = User::factory()->create();
    $contact = Contact::factory()->create();

    $response = $this->actingAs($user)->post(route('dashboard.contacts.reply', $contact->id), [
        'reply_content' => '',
    ]);

    $response->assertSessionHasErrors('reply_content');
    expect($contact->fresh()->replied_at)->toBeNull();
});

it('deletes a contact message', function () {
    $user = User::factory()->create();
    $contact = Contact::factory()->create();

    $response = $this->actingAs($user)->delete(route('dashboard.contacts.destroy', $contact->id));

    $response->assertRedirect(route('dashboard.contacts.index'));
    $response->assertSessionHas('success');
    expect(Contact::find($contact->id))->toBeNull();
});
