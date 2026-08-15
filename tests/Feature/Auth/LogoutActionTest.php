<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;

uses(RefreshDatabase::class);

beforeEach(function () {
    Role::firstOrCreate(['name' => 'shipper']);
    Role::firstOrCreate(['name' => 'carrier']);
    Role::firstOrCreate(['name' => 'admin']);
});

it('logs out an authenticated user and redirects to login', function () {
    $user = User::factory()->create();
    $user->assignRole('carrier');

    $response = $this->actingAs($user)->post(route('logout'));

    $response->assertRedirect(route('login'));
    $this->assertGuest();
});

it('does not allow a guest to hit the logout route', function () {
    $response = $this->post(route('logout'));

    // Protected by the 'auth' middleware group.
    $response->assertRedirect(route('login'));
    $this->assertGuest();
});
