<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

uses(RefreshDatabase::class);

beforeEach(function () {
    Role::firstOrCreate(['name' => 'shipper']);
    Role::firstOrCreate(['name' => 'carrier']);
    Role::firstOrCreate(['name' => 'admin']);
});

it('logs in a user with valid credentials and redirects to the dashboard', function () {
    $user = User::factory()->create([
        'password' => Hash::make('correct-password'),
    ]);
    $user->assignRole('carrier');

    $response = $this->post(route('login.store'), [
        'email' => $user->email,
        'password' => 'correct-password',
    ]);

    $response->assertRedirect();
    $this->assertAuthenticatedAs($user);
});

it('rejects login with an incorrect password', function () {
    $user = User::factory()->create([
        'password' => Hash::make('correct-password'),
    ]);
    $user->assignRole('carrier');

    $response = $this->from(route('login'))->post(route('login.store'), [
        'email' => $user->email,
        'password' => 'wrong-password',
    ]);

    $response->assertRedirect(route('login'));
    $response->assertSessionHasErrors('email');
    $this->assertGuest();
});

it('requires email and password fields', function () {
    $response = $this->from(route('login'))->post(route('login.store'), []);

    $response->assertSessionHasErrors(['email', 'password']);
    $this->assertGuest();
});
