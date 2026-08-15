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

    $this->actor = User::factory()->create();
    $this->actingAs($this->actor);
});

it('lists pending users on the pending endpoint', function () {
    $pending = User::factory()->create(['status' => User::STATUS_PENDING]);
    $active = User::factory()->create(['status' => User::STATUS_ACTIVE]);

    $response = $this->get(route('dashboard.users.pending'));

    $response->assertOk();
    $response->assertViewIs('pages.dashboard.users.pending');

    $ids = $response->viewData('users')->pluck('id');
    expect($ids)->toContain($pending->id);
    expect($ids)->not->toContain($active->id);
});

it('searches users by name or email on the index', function () {
    $match = User::factory()->create(['name' => 'Zeynep Carrier', 'email' => 'zeynep@example.com']);
    $other = User::factory()->create(['name' => 'Someone Else', 'email' => 'someone@example.com']);

    $response = $this->get(route('dashboard.users.index', ['search' => 'Zeynep']));

    $response->assertOk();
    $ids = $response->viewData('users')->pluck('id');
    expect($ids)->toContain($match->id);
    expect($ids)->not->toContain($other->id);
});

it('filters users by status on the index', function () {
    $active = User::factory()->create(['status' => User::STATUS_ACTIVE]);
    $inactive = User::factory()->create(['status' => User::STATUS_INACTIVE]);

    $response = $this->get(route('dashboard.users.index', ['status' => User::STATUS_ACTIVE]));

    $ids = $response->viewData('users')->pluck('id');
    expect($ids)->toContain($active->id);
    expect($ids)->not->toContain($inactive->id);
});

it('filters users by role via the dedicated carriers route', function () {
    $carrier = User::factory()->create();
    $carrier->assignRole('carrier');

    $shipper = User::factory()->create();
    $shipper->assignRole('shipper');

    $response = $this->get(route('dashboard.users.carriers'));

    $response->assertOk();
    $response->assertViewHas('roleToFilter', 'carrier');

    $ids = $response->viewData('users')->pluck('id');
    expect($ids)->toContain($carrier->id);
    expect($ids)->not->toContain($shipper->id);
});

it('shows a single user', function () {
    $user = User::factory()->create();

    $response = $this->get(route('dashboard.users.show', $user->id));

    $response->assertOk();
    $response->assertViewIs('pages.dashboard.users.show');
    $response->assertViewHas('user', fn ($viewUser) => $viewUser->id === $user->id);
});

it('renders the edit user form', function () {
    $user = User::factory()->create();

    $response = $this->get(route('dashboard.users.edit', $user->id));

    $response->assertOk();
    $response->assertViewIs('pages.dashboard.users.edit');
    $response->assertViewHas('user', fn ($viewUser) => $viewUser->id === $user->id);
});

it('updates a user and syncs roles on the happy path', function () {
    $user = User::factory()->create(['name' => 'Old Name', 'status' => User::STATUS_PENDING]);
    $carrierRole = Role::where('name', 'carrier')->first();

    $response = $this->put(route('dashboard.users.update', $user->id), [
        'name' => 'New Name',
        'email' => $user->email,
        'status' => User::STATUS_ACTIVE,
        'roles' => [$carrierRole->id],
    ]);

    $response->assertRedirect(route('dashboard.users.index'));
    $response->assertSessionHas('success');

    $user->refresh();
    expect($user->name)->toBe('New Name');
    expect($user->status)->toBe(User::STATUS_ACTIVE);
    expect($user->roles->pluck('name')->all())->toBe(['carrier']);
});

it('clears roles on update when none are submitted', function () {
    $user = User::factory()->create(['status' => User::STATUS_ACTIVE]);
    $user->assignRole('carrier');

    $response = $this->put(route('dashboard.users.update', $user->id), [
        'name' => $user->name,
        'email' => $user->email,
        'status' => $user->status,
    ]);

    $response->assertRedirect(route('dashboard.users.index'));
    expect($user->fresh()->roles)->toBeEmpty();
});

it('validates required fields and status enum on update', function () {
    $user = User::factory()->create();

    $response = $this->put(route('dashboard.users.update', $user->id), [
        'name' => '',
        'email' => 'not-an-email',
        'status' => 'bogus-status',
    ]);

    $response->assertSessionHasErrors(['name', 'email', 'status']);
});

it('rejects a duplicate email on update, excluding the user itself', function () {
    $existing = User::factory()->create(['email' => 'taken@example.com']);
    $user = User::factory()->create(['status' => User::STATUS_ACTIVE]);

    $response = $this->put(route('dashboard.users.update', $user->id), [
        'name' => $user->name,
        'email' => 'taken@example.com',
        'status' => $user->status,
    ]);

    $response->assertSessionHasErrors('email');
    expect($user->fresh()->email)->not->toBe('taken@example.com');
});

it('updates only the status via updateStatus', function () {
    $user = User::factory()->create(['status' => User::STATUS_PENDING]);

    $response = $this->patch(route('dashboard.users.update-status', $user->id), [
        'status' => User::STATUS_ACTIVE,
    ]);

    $response->assertRedirect();
    $response->assertSessionHas('success');
    expect($user->fresh()->status)->toBe(User::STATUS_ACTIVE);
});

it('rejects an invalid status value on updateStatus', function () {
    $user = User::factory()->create(['status' => User::STATUS_PENDING]);

    $response = $this->patch(route('dashboard.users.update-status', $user->id), [
        'status' => 'not-a-real-status',
    ]);

    $response->assertSessionHasErrors('status');
    expect($user->fresh()->status)->toBe(User::STATUS_PENDING);
});

it('deletes another user', function () {
    $user = User::factory()->create();

    $response = $this->delete(route('dashboard.users.destroy', $user->id));

    $response->assertRedirect(route('dashboard.users.index'));
    expect(User::find($user->id))->toBeNull();
});

it('blocks a user from deleting their own account', function () {
    $response = $this->delete(route('dashboard.users.destroy', $this->actor->id));

    $response->assertSessionHas('error');
    expect(User::find($this->actor->id))->not->toBeNull();
});

it('allows any authenticated user to view/edit/update/delete users, regardless of role (no authorization check beyond auth)', function () {
    // UserController's index/pending/show/edit/update/updateStatus/destroy have no hasRole()/can() gate --
    // only the global "auth" middleware applies. impersonate() is the sole exception (explicit hasRole('admin') check).
    $target = User::factory()->create(['status' => User::STATUS_ACTIVE]);

    $showResponse = $this->get(route('dashboard.users.show', $target->id));
    $showResponse->assertOk();

    $updateResponse = $this->put(route('dashboard.users.update', $target->id), [
        'name' => $target->name,
        'email' => $target->email,
        'status' => $target->status,
    ]);
    $updateResponse->assertRedirect(route('dashboard.users.index'));

    $destroyResponse = $this->delete(route('dashboard.users.destroy', $target->id));
    $destroyResponse->assertRedirect(route('dashboard.users.index'));
    expect(User::find($target->id))->toBeNull();
});
