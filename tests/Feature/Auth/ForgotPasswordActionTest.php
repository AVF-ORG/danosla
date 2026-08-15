<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Illuminate\Auth\Notifications\ResetPassword;
use Spatie\Permission\Models\Role;

uses(RefreshDatabase::class);

beforeEach(function () {
    Role::firstOrCreate(['name' => 'shipper']);
    Role::firstOrCreate(['name' => 'carrier']);
    Role::firstOrCreate(['name' => 'admin']);
});

it('sends a password reset link for an existing user', function () {
    Notification::fake();

    $user = User::factory()->create();

    $response = $this->from(route('password.request'))->post(route('password.email'), [
        'email' => $user->email,
    ]);

    $response->assertRedirect(route('password.request'));
    $response->assertSessionHas('status');
    $response->assertSessionDoesntHaveErrors();

    Notification::assertSentTo($user, ResetPassword::class);
});

it('returns an error when requesting a reset link for an unknown email', function () {
    Notification::fake();

    $response = $this->from(route('password.request'))->post(route('password.email'), [
        'email' => 'unknown@example.com',
    ]);

    $response->assertSessionHasErrors('email');
    Notification::assertNothingSent();
});

it('requires a valid email to request a password reset link', function () {
    $response = $this->from(route('password.request'))->post(route('password.email'), [
        'email' => 'not-an-email',
    ]);

    $response->assertSessionHasErrors('email');
});
