<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Spatie\Permission\Models\Role;

uses(RefreshDatabase::class);

beforeEach(function () {
    Role::firstOrCreate(['name' => 'shipper']);
    Role::firstOrCreate(['name' => 'carrier']);
    Role::firstOrCreate(['name' => 'admin']);
});

it('resets the password with a valid token and redirects to login', function () {
    $user = User::factory()->create([
        'password' => Hash::make('old-password'),
    ]);

    $token = Password::createToken($user);

    $response = $this->post(route('password.update'), [
        'token' => $token,
        'email' => $user->email,
        'password' => 'NewPassw0rd@1',
        'password_confirmation' => 'NewPassw0rd@1',
    ]);

    $response->assertRedirect(route('login'));
    $response->assertSessionHas('status');

    $user->refresh();
    expect(Hash::check('NewPassw0rd@1', $user->password))->toBeTrue();
});

it('rejects reset with an invalid token', function () {
    $user = User::factory()->create([
        'password' => Hash::make('old-password'),
    ]);

    $response = $this->from(route('password.reset', ['token' => 'bogus-token']))
        ->post(route('password.update'), [
            'token' => 'bogus-token',
            'email' => $user->email,
            'password' => 'NewPassw0rd@1',
            'password_confirmation' => 'NewPassw0rd@1',
        ]);

    $response->assertSessionHasErrors('email');

    $user->refresh();
    expect(Hash::check('old-password', $user->password))->toBeTrue();
});

it('requires the password confirmation to match', function () {
    $user = User::factory()->create();
    $token = Password::createToken($user);

    $response = $this->from(route('password.reset', ['token' => $token]))
        ->post(route('password.update'), [
            'token' => $token,
            'email' => $user->email,
            'password' => 'NewPassw0rd@1',
            'password_confirmation' => 'MismatchPassw0rd@1',
        ]);

    $response->assertSessionHasErrors('password');
});
