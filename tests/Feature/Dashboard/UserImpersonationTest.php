<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;

uses(RefreshDatabase::class);

beforeEach(function () {
    // The mcamara/laravel-localization middleware stack redirects any
    // request whose path lacks a locale segment (e.g. "dashboard/users")
    // to a bare "/en" root, unrelated to the Dashboard authorization logic
    // under test. Disable it here so we can exercise the controllers directly.
    $this->withoutMiddleware([
        \Mcamara\LaravelLocalization\Middleware\LaravelLocalizationRedirectFilter::class,
        \Mcamara\LaravelLocalization\Middleware\LocaleSessionRedirect::class,
    ]);

    Role::firstOrCreate(['name' => 'admin']);
    Role::firstOrCreate(['name' => 'carrier']);
    Role::firstOrCreate(['name' => 'shipper']);
});

it('lets an admin impersonate a carrier, logging them in and stashing the admin id in session', function () {
    $admin = User::factory()->create();
    $admin->assignRole('admin');

    $carrier = User::factory()->create();
    $carrier->assignRole('carrier');

    $this->actingAs($admin);

    $response = $this->post(route('dashboard.users.impersonate', $carrier->id));

    $response->assertRedirect(route('dashboard.index'));
    $response->assertSessionHas('success');
    $response->assertSessionHas('impersonated_by', $admin->id);

    $this->assertAuthenticatedAs($carrier);
});

it('forbids a non-admin from impersonating another user', function () {
    $carrier = User::factory()->create();
    $carrier->assignRole('carrier');

    $shipper = User::factory()->create();
    $shipper->assignRole('shipper');

    $this->actingAs($carrier);

    $response = $this->post(route('dashboard.users.impersonate', $shipper->id));

    $response->assertForbidden();
    $this->assertAuthenticatedAs($carrier);
});

it('blocks an admin from impersonating themselves', function () {
    $admin = User::factory()->create();
    $admin->assignRole('admin');

    $this->actingAs($admin);

    $response = $this->post(route('dashboard.users.impersonate', $admin->id));

    $response->assertSessionHas('error');
    $response->assertSessionMissing('impersonated_by');
    $this->assertAuthenticatedAs($admin);
});

it('restores the admin account and clears the session key on stopImpersonate', function () {
    $admin = User::factory()->create();
    $admin->assignRole('admin');

    $carrier = User::factory()->create();
    $carrier->assignRole('carrier');

    $this->actingAs($admin);
    $this->post(route('dashboard.users.impersonate', $carrier->id));
    $this->assertAuthenticatedAs($carrier);

    $response = $this->post(route('dashboard.users.stop-impersonation'));

    $response->assertRedirect(route('dashboard.index'));
    $response->assertSessionHas('success');
    $response->assertSessionMissing('impersonated_by');

    $this->assertAuthenticatedAs($admin);
});

it('redirects to the dashboard without changes when stopImpersonate is called with no active impersonation', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $response = $this->post(route('dashboard.users.stop-impersonation'));

    $response->assertRedirect(route('dashboard.index'));
    $this->assertAuthenticatedAs($user);
});
