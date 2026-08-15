<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

uses(RefreshDatabase::class);

beforeEach(function () {
    // The mcamara/laravel-localization middleware stack redirects any
    // request whose path lacks a locale segment (e.g. "dashboard/roles")
    // to a bare "/en" root, unrelated to the Dashboard authorization logic
    // under test. Disable it here so we can exercise the controllers directly.
    $this->withoutMiddleware([
        \Mcamara\LaravelLocalization\Middleware\LaravelLocalizationRedirectFilter::class,
        \Mcamara\LaravelLocalization\Middleware\LocaleSessionRedirect::class,
    ]);

    $this->user = User::factory()->create();
    $this->actingAs($this->user);
});

it('renders the role listing', function () {
    Role::create(['name' => 'editor']);
    Role::create(['name' => 'moderator']);

    $response = $this->get(route('dashboard.roles.index'));

    $response->assertOk();
    $response->assertViewIs('pages.dashboard.roles.index');
    expect($response->viewData('roles')->pluck('name'))->toContain('editor', 'moderator');
});

it('filters the role listing by search term', function () {
    Role::create(['name' => 'editor']);
    Role::create(['name' => 'moderator']);

    $response = $this->get(route('dashboard.roles.index', ['search' => 'edit']));

    $names = $response->viewData('roles')->pluck('name');
    expect($names)->toContain('editor');
    expect($names)->not->toContain('moderator');
});

it('renders the create role form', function () {
    Permission::create(['name' => 'edit-articles']);

    $response = $this->get(route('dashboard.roles.create'));

    $response->assertOk();
    $response->assertViewIs('pages.dashboard.roles.form');
});

it('creates a role without permissions', function () {
    $response = $this->post(route('dashboard.roles.store'), [
        'name' => 'editor',
    ]);

    $response->assertRedirect(route('dashboard.roles.index'));
    $response->assertSessionHas('success');

    $role = Role::where('name', 'editor')->first();
    expect($role)->not->toBeNull();
    expect($role->permissions)->toBeEmpty();
});

it('creates a role and syncs the given permissions', function () {
    $editArticles = Permission::create(['name' => 'edit-articles']);
    $deleteArticles = Permission::create(['name' => 'delete-articles']);

    $response = $this->post(route('dashboard.roles.store'), [
        'name' => 'editor',
        'permissions' => [$editArticles->id, $deleteArticles->id],
    ]);

    $response->assertRedirect(route('dashboard.roles.index'));

    $role = Role::where('name', 'editor')->first();
    expect($role->permissions->pluck('name')->sort()->values()->all())
        ->toBe(['delete-articles', 'edit-articles']);
});

it('rejects a duplicate role name on store', function () {
    Role::create(['name' => 'editor']);

    $response = $this->post(route('dashboard.roles.store'), [
        'name' => 'editor',
    ]);

    $response->assertSessionHasErrors('name');
    expect(Role::where('name', 'editor')->count())->toBe(1);
});

it('rejects a non-existent permission id when creating a role', function () {
    $response = $this->post(route('dashboard.roles.store'), [
        'name' => 'editor',
        'permissions' => [999999],
    ]);

    $response->assertSessionHasErrors('permissions.0');
    expect(Role::where('name', 'editor')->exists())->toBeFalse();
});

it('renders the edit role form', function () {
    $role = Role::create(['name' => 'editor']);

    $response = $this->get(route('dashboard.roles.edit', $role->id));

    $response->assertOk();
    $response->assertViewIs('pages.dashboard.roles.form');
    $response->assertViewHas('role', fn ($viewRole) => $viewRole->id === $role->id);
});

it('updates a role name and resyncs its permissions', function () {
    $role = Role::create(['name' => 'editor']);
    $keep = Permission::create(['name' => 'edit-articles']);
    $drop = Permission::create(['name' => 'delete-articles']);
    $role->syncPermissions([$drop->id]);

    $response = $this->put(route('dashboard.roles.update', $role->id), [
        'name' => 'senior-editor',
        'permissions' => [$keep->id],
    ]);

    $response->assertRedirect(route('dashboard.roles.index'));

    $role->refresh();
    expect($role->name)->toBe('senior-editor');
    expect($role->permissions->pluck('name')->all())->toBe(['edit-articles']);
});

it('clears permissions on update when none are submitted', function () {
    $role = Role::create(['name' => 'editor']);
    $permission = Permission::create(['name' => 'edit-articles']);
    $role->syncPermissions([$permission->id]);

    $response = $this->put(route('dashboard.roles.update', $role->id), [
        'name' => 'editor',
    ]);

    $response->assertRedirect(route('dashboard.roles.index'));
    expect($role->fresh()->permissions)->toBeEmpty();
});

it('rejects a duplicate role name on update, excluding the role itself', function () {
    Role::create(['name' => 'editor']);
    $role = Role::create(['name' => 'moderator']);

    $response = $this->put(route('dashboard.roles.update', $role->id), [
        'name' => 'editor',
    ]);

    $response->assertSessionHasErrors('name');
    expect($role->fresh()->name)->toBe('moderator');
});

it('allows updating a role with the same name it already has', function () {
    $role = Role::create(['name' => 'editor']);

    $response = $this->put(route('dashboard.roles.update', $role->id), [
        'name' => 'editor',
    ]);

    $response->assertSessionHasNoErrors();
    $response->assertRedirect(route('dashboard.roles.index'));
});

it('deletes a role', function () {
    $role = Role::create(['name' => 'editor']);

    $response = $this->delete(route('dashboard.roles.destroy', $role->id));

    $response->assertRedirect(route('dashboard.roles.index'));
    expect(Role::find($role->id))->toBeNull();
});

it('allows any authenticated user to manage roles, regardless of role/permission (no authorization check beyond auth)', function () {
    // RoleController has no hasRole()/can() gate at all -- only the global "auth" middleware applies.
    // This is a plain, unprivileged user (no roles/permissions assigned) and it can still create/update/delete roles.
    $role = Role::create(['name' => 'editor']);

    $storeResponse = $this->post(route('dashboard.roles.store'), ['name' => 'new-role']);
    $storeResponse->assertRedirect(route('dashboard.roles.index'));

    $destroyResponse = $this->delete(route('dashboard.roles.destroy', $role->id));
    $destroyResponse->assertRedirect(route('dashboard.roles.index'));

    expect(Role::where('name', 'new-role')->exists())->toBeTrue();
    expect(Role::find($role->id))->toBeNull();
});
