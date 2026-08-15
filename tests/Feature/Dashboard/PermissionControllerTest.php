<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;

uses(RefreshDatabase::class);

// Note: dashboard.permissions.create / .show / .edit routes exist (full Route::resource)
// but PermissionController defines no create()/show()/edit() methods, so those three
// routes are unreachable (fatal "call to undefined method" if hit). They are intentionally
// not exercised here -- see final report.

beforeEach(function () {
    // The mcamara/laravel-localization middleware stack redirects any
    // request whose path lacks a locale segment (e.g. "dashboard/permissions")
    // to a bare "/en" root, unrelated to the Dashboard authorization logic
    // under test. Disable it here so we can exercise the controllers directly.
    $this->withoutMiddleware([
        \Mcamara\LaravelLocalization\Middleware\LaravelLocalizationRedirectFilter::class,
        \Mcamara\LaravelLocalization\Middleware\LocaleSessionRedirect::class,
    ]);

    $this->user = User::factory()->create();
    $this->actingAs($this->user);
});

it('renders the permission listing', function () {
    Permission::create(['name' => 'edit-articles']);
    Permission::create(['name' => 'delete-articles']);

    $response = $this->get(route('dashboard.permissions.index'));

    $response->assertOk();
    $response->assertViewIs('pages.dashboard.permissions.index');
    expect($response->viewData('permissions')->pluck('name'))->toContain('edit-articles', 'delete-articles');
});

it('filters the permission listing by search term', function () {
    Permission::create(['name' => 'edit-articles']);
    Permission::create(['name' => 'delete-articles']);

    $response = $this->get(route('dashboard.permissions.index', ['search' => 'edit']));

    $names = $response->viewData('permissions')->pluck('name');
    expect($names)->toContain('edit-articles');
    expect($names)->not->toContain('delete-articles');
});

it('creates a permission', function () {
    $response = $this->post(route('dashboard.permissions.store'), [
        'name' => 'edit-articles',
    ]);

    $response->assertRedirect(route('dashboard.permissions.index'));
    $response->assertSessionHas('success');
    expect(Permission::where('name', 'edit-articles')->exists())->toBeTrue();
});

it('rejects a duplicate permission name on store', function () {
    Permission::create(['name' => 'edit-articles']);

    $response = $this->post(route('dashboard.permissions.store'), [
        'name' => 'edit-articles',
    ]);

    $response->assertSessionHasErrors('name');
    expect(Permission::where('name', 'edit-articles')->count())->toBe(1);
});

it('requires a name to create a permission', function () {
    $response = $this->post(route('dashboard.permissions.store'), []);

    $response->assertSessionHasErrors('name');
});

it('updates a permission name', function () {
    $permission = Permission::create(['name' => 'edit-articles']);

    $response = $this->put(route('dashboard.permissions.update', $permission->id), [
        'name' => 'edit-posts',
    ]);

    $response->assertRedirect(route('dashboard.permissions.index'));
    expect($permission->fresh()->name)->toBe('edit-posts');
});

it('rejects a duplicate permission name on update, excluding itself', function () {
    Permission::create(['name' => 'edit-articles']);
    $permission = Permission::create(['name' => 'delete-articles']);

    $response = $this->put(route('dashboard.permissions.update', $permission->id), [
        'name' => 'edit-articles',
    ]);

    $response->assertSessionHasErrors('name');
    expect($permission->fresh()->name)->toBe('delete-articles');
});

it('allows updating a permission with the same name it already has', function () {
    $permission = Permission::create(['name' => 'edit-articles']);

    $response = $this->put(route('dashboard.permissions.update', $permission->id), [
        'name' => 'edit-articles',
    ]);

    $response->assertSessionHasNoErrors();
});

it('deletes a permission', function () {
    $permission = Permission::create(['name' => 'edit-articles']);

    $response = $this->delete(route('dashboard.permissions.destroy', $permission->id));

    $response->assertRedirect(route('dashboard.permissions.index'));
    expect(Permission::find($permission->id))->toBeNull();
});

it('allows any authenticated user to manage permissions, regardless of role/permission (no authorization check beyond auth)', function () {
    // PermissionController has no hasRole()/can() gate at all -- only the global "auth" middleware applies.
    $permission = Permission::create(['name' => 'edit-articles']);

    $storeResponse = $this->post(route('dashboard.permissions.store'), ['name' => 'new-permission']);
    $storeResponse->assertRedirect(route('dashboard.permissions.index'));

    $destroyResponse = $this->delete(route('dashboard.permissions.destroy', $permission->id));
    $destroyResponse->assertRedirect(route('dashboard.permissions.index'));

    expect(Permission::where('name', 'new-permission')->exists())->toBeTrue();
    expect(Permission::find($permission->id))->toBeNull();
});
