<?php

use App\Livewire\Auth\Register;
use App\Models\Country;
use App\Models\Region;
use App\Models\Sector;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;

uses(RefreshDatabase::class);

beforeEach(function () {
    Role::firstOrCreate(['name' => 'shipper']);
    Role::firstOrCreate(['name' => 'carrier']);
    Role::firstOrCreate(['name' => 'admin']);
});

function createRegionWithCountry(): array
{
    $region = Region::factory()->create();
    $country = Country::factory()->create();
    $region->countries()->attach($country->id);

    return [$region, $country];
}

it('registers a carrier through the multi-step wizard and logs them in', function () {
    [$region, $country] = createRegionWithCountry();

    Livewire::test(Register::class)
        ->call('selectRole', 'carrier')
        ->assertSet('role', 'carrier')
        ->assertSet('step', 2)
        ->set('name', 'John Carrier')
        ->set('email', 'john.carrier@example.com')
        ->set('password', 'Passw0rd@1')
        ->set('password_confirmation', 'Passw0rd@1')
        ->set('region_id', $region->id)
        ->set('country_id', $country->id)
        ->set('phone', '+1234567890')
        ->set('address', '123 Main Street')
        ->set('company_name', 'Carrier Co')
        ->set('company_number', 'CC-001')
        ->set('acceptedTerms', true)
        ->call('register')
        ->assertRedirect('/dashboard');

    $user = User::where('email', 'john.carrier@example.com')->first();

    expect($user)->not->toBeNull();
    expect($user->status)->toBe(User::STATUS_PENDING);
    expect($user->sector_id)->toBeNull();
    expect($user->hasRole('carrier'))->toBeTrue();

    $this->assertAuthenticatedAs($user);
});

it('registers a shipper with a sector through the multi-step wizard', function () {
    [$region, $country] = createRegionWithCountry();
    $sector = Sector::factory()->create();

    Livewire::test(Register::class)
        ->call('selectRole', 'shipper')
        ->set('name', 'Jane Shipper')
        ->set('email', 'jane.shipper@example.com')
        ->set('password', 'Passw0rd@1')
        ->set('password_confirmation', 'Passw0rd@1')
        ->set('region_id', $region->id)
        ->set('country_id', $country->id)
        ->set('phone', '+1234567890')
        ->set('address', '123 Main Street')
        ->set('sector_id', $sector->id)
        ->set('company_name', 'Shipper Co')
        ->set('company_number', 'SC-001')
        ->set('acceptedTerms', true)
        ->call('register')
        ->assertRedirect('/dashboard');

    $user = User::where('email', 'jane.shipper@example.com')->first();

    expect($user)->not->toBeNull();
    expect($user->status)->toBe(User::STATUS_PENDING);
    expect($user->sector_id)->toBe($sector->id);
    expect($user->hasRole('shipper'))->toBeTrue();
});

it('fails validation when a shipper does not provide a sector', function () {
    [$region, $country] = createRegionWithCountry();

    Livewire::test(Register::class)
        ->call('selectRole', 'shipper')
        ->set('name', 'Jane Shipper')
        ->set('email', 'jane-no-sector@example.com')
        ->set('password', 'Passw0rd@1')
        ->set('password_confirmation', 'Passw0rd@1')
        ->set('region_id', $region->id)
        ->set('country_id', $country->id)
        ->set('phone', '+1234567890')
        ->set('address', '123 Main Street')
        ->set('company_name', 'Shipper Co')
        ->set('company_number', 'SC-001')
        ->set('acceptedTerms', true)
        ->call('register')
        ->assertHasErrors('sector_id');

    $this->assertDatabaseMissing('users', ['email' => 'jane-no-sector@example.com']);
});

it('fails validation when the password does not meet complexity rules', function () {
    [$region, $country] = createRegionWithCountry();

    Livewire::test(Register::class)
        ->call('selectRole', 'carrier')
        ->set('name', 'John Carrier')
        ->set('email', 'weakpass@example.com')
        ->set('password', 'weakpassword')
        ->set('password_confirmation', 'weakpassword')
        ->set('region_id', $region->id)
        ->set('country_id', $country->id)
        ->set('phone', '+1234567890')
        ->set('address', '123 Main Street')
        ->set('company_name', 'Carrier Co')
        ->set('company_number', 'CC-001')
        ->set('acceptedTerms', true)
        ->call('register')
        ->assertHasErrors('password');

    $this->assertDatabaseMissing('users', ['email' => 'weakpass@example.com']);
});

it('fails validation when the terms are not accepted', function () {
    [$region, $country] = createRegionWithCountry();

    Livewire::test(Register::class)
        ->call('selectRole', 'carrier')
        ->set('name', 'John Carrier')
        ->set('email', 'noterms@example.com')
        ->set('password', 'Passw0rd@1')
        ->set('password_confirmation', 'Passw0rd@1')
        ->set('region_id', $region->id)
        ->set('country_id', $country->id)
        ->set('phone', '+1234567890')
        ->set('address', '123 Main Street')
        ->set('company_name', 'Carrier Co')
        ->set('company_number', 'CC-001')
        ->set('acceptedTerms', false)
        ->call('register')
        ->assertHasErrors('acceptedTerms');

    $this->assertDatabaseMissing('users', ['email' => 'noterms@example.com']);
});

it('only exposes countries belonging to the selected region', function () {
    $region = Region::factory()->create();
    $otherRegion = Region::factory()->create();
    $countryInRegion = Country::factory()->create();
    $countryInOtherRegion = Country::factory()->create();
    $region->countries()->attach($countryInRegion->id);
    $otherRegion->countries()->attach($countryInOtherRegion->id);

    $component = Livewire::test(Register::class)
        ->call('selectRole', 'carrier')
        ->set('region_id', $region->id);

    $countryIds = collect($component->get('countries'))->pluck('id')->all();

    expect($countryIds)->toContain($countryInRegion->id);
    expect($countryIds)->not->toContain($countryInOtherRegion->id);
});
