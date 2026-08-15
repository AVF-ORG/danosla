<?php

use App\Models\Country;
use App\Models\Sector;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;

uses(RefreshDatabase::class);

beforeEach(function () {
    Role::firstOrCreate(['name' => 'shipper']);
    Role::firstOrCreate(['name' => 'carrier']);
    Role::firstOrCreate(['name' => 'admin']);
});

function validRegisterPayload(array $overrides = []): array
{
    $country = Country::factory()->create();

    return array_merge([
        'role' => 'carrier',
        'name' => 'Jane Doe',
        'email' => 'jane.doe@example.com',
        'password' => 'Passw0rd@1',
        'password_confirmation' => 'Passw0rd@1',
        'country_id' => $country->id,
        'phone' => '+1234567890',
        'address' => '123 Main Street',
        'website' => 'https://example.com',
        'company_name' => 'Acme Inc',
        'company_number' => 'ACME-001',
    ], $overrides);
}

it('registers a carrier, logs them in and redirects to the dashboard', function () {
    $payload = validRegisterPayload(['role' => 'carrier']);

    $response = $this->post(route('register.store'), $payload);

    $response->assertRedirect('/dashboard');

    $user = User::where('email', $payload['email'])->first();

    expect($user)->not->toBeNull();
    expect($user->status)->toBe(User::STATUS_PENDING);
    expect($user->sector_id)->toBeNull();
    expect($user->hasRole('carrier'))->toBeTrue();

    $this->assertAuthenticatedAs($user);
});

it('registers a shipper with a sector and assigns the shipper role', function () {
    $sector = Sector::factory()->create();

    $payload = validRegisterPayload([
        'role' => 'shipper',
        'email' => 'shipper@example.com',
        'sector_id' => $sector->id,
    ]);

    $response = $this->post(route('register.store'), $payload);

    $response->assertRedirect('/dashboard');

    $user = User::where('email', 'shipper@example.com')->first();

    expect($user)->not->toBeNull();
    expect($user->status)->toBe(User::STATUS_PENDING);
    expect($user->sector_id)->toBe($sector->id);
    expect($user->hasRole('shipper'))->toBeTrue();
});

it('requires a sector when registering as a shipper', function () {
    $payload = validRegisterPayload([
        'role' => 'shipper',
        'email' => 'shipper-no-sector@example.com',
    ]);
    unset($payload['sector_id']);

    $response = $this->from(route('register'))->post(route('register.store'), $payload);

    $response->assertSessionHasErrors('sector_id');
    $this->assertDatabaseMissing('users', ['email' => 'shipper-no-sector@example.com']);
});

it('rejects registration with a password that does not meet complexity rules', function () {
    $payload = validRegisterPayload([
        'email' => 'weakpass@example.com',
        'password' => 'weakpassword',
        'password_confirmation' => 'weakpassword',
    ]);

    $response = $this->from(route('register'))->post(route('register.store'), $payload);

    $response->assertSessionHasErrors('password');
    $this->assertDatabaseMissing('users', ['email' => 'weakpass@example.com']);
});

it('rejects registration with a duplicate email', function () {
    $existing = User::factory()->create(['email' => 'duplicate@example.com']);

    $payload = validRegisterPayload(['email' => 'duplicate@example.com']);

    $response = $this->from(route('register'))->post(route('register.store'), $payload);

    $response->assertSessionHasErrors('email');
    expect(User::where('email', 'duplicate@example.com')->count())->toBe(1);
});

it('rejects registration with an invalid role', function () {
    $payload = validRegisterPayload(['role' => 'admin']);

    $response = $this->from(route('register'))->post(route('register.store'), $payload);

    $response->assertSessionHasErrors('role');
    $this->assertDatabaseMissing('users', ['email' => $payload['email']]);
});
