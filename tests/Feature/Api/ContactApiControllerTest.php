<?php

use App\Models\Contact;
use App\Models\ContactSubject;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('lists only active contact subjects', function () {
    $active = ContactSubject::factory()->create(['name' => ['en' => 'Sales'], 'is_active' => true]);
    $inactive = ContactSubject::factory()->create(['name' => ['en' => 'Archived'], 'is_active' => false]);

    $response = $this->getJson('/api/v1/contact-subjects');

    $response->assertOk();
    $response->assertJson([
        'success' => true,
        'message' => 'Contact subjects retrieved successfully',
    ]);

    $ids = collect($response->json('data'))->pluck('id');
    expect($ids)->toContain($active->id);
    expect($ids)->not->toContain($inactive->id);

    $response->assertJsonFragment([
        'id' => $active->id,
        'name' => $active->name,
        'slug' => $active->slug,
    ]);
});

it('creates a contact message with valid data', function () {
    $subject = ContactSubject::factory()->create(['is_active' => true]);

    $payload = [
        'name' => 'John Doe',
        'email' => 'john.doe@example.com',
        'phone' => '0600000000',
        'contact_subject_id' => $subject->id,
        'content' => 'I have a question about my shipment.',
    ];

    $response = $this->postJson('/api/v1/contacts', $payload);

    $response->assertCreated();
    $response->assertJson([
        'success' => true,
        'message' => 'Your message has been sent successfully',
        'data' => [
            'name' => 'John Doe',
            'email' => 'john.doe@example.com',
            'phone' => '0600000000',
            'content' => 'I have a question about my shipment.',
        ],
    ]);

    $response->assertJsonPath('data.subject.id', $subject->id);

    expect(Contact::where('email', 'john.doe@example.com')->exists())->toBeTrue();
});

it('allows creating a contact message without a phone number', function () {
    $subject = ContactSubject::factory()->create(['is_active' => true]);

    $response = $this->postJson('/api/v1/contacts', [
        'name' => 'Jane Doe',
        'email' => 'jane.doe@example.com',
        'contact_subject_id' => $subject->id,
        'content' => 'General inquiry.',
    ]);

    $response->assertCreated();
    expect(Contact::where('email', 'jane.doe@example.com')->first()->phone)->toBeNull();
});

it('fails to create a contact message with missing required fields', function () {
    $response = $this->postJson('/api/v1/contacts', []);

    $response->assertStatus(422);
    $response->assertJson(['success' => false, 'message' => 'Validation error']);
    $response->assertJsonValidationErrors(['name', 'email', 'contact_subject_id', 'content']);
});

it('fails to create a contact message with an invalid email', function () {
    $subject = ContactSubject::factory()->create(['is_active' => true]);

    $response = $this->postJson('/api/v1/contacts', [
        'name' => 'John Doe',
        'email' => 'not-an-email',
        'contact_subject_id' => $subject->id,
        'content' => 'Some content.',
    ]);

    $response->assertStatus(422);
    $response->assertJsonValidationErrors(['email']);
});

it('fails to create a contact message with a non-existent contact subject', function () {
    $response = $this->postJson('/api/v1/contacts', [
        'name' => 'John Doe',
        'email' => 'john.doe@example.com',
        'contact_subject_id' => 999999,
        'content' => 'Some content.',
    ]);

    $response->assertStatus(422);
    $response->assertJsonValidationErrors(['contact_subject_id']);
});
