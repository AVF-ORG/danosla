<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // Create permissions
        $permissions = [
            'view-dashboard',
            'manage-sectors',
            'manage-regions',
            'manage-countries',
            'manage-roles',
            'manage-permissions',
            'manage-users',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Create roles and assign created permissions
        $adminRole = Role::firstOrCreate(['name' => 'Super Admin']);
        $adminRole->syncPermissions(Permission::all());

        $managerRole = Role::firstOrCreate(['name' => 'Manager']);
        $managerRole->syncPermissions([
            'view-dashboard',
            'manage-sectors',
            'manage-regions',
            'manage-countries',
        ]);

        $userRole = Role::firstOrCreate(['name' => 'User']);
        $userRole->syncPermissions(['view-dashboard']);

        // Assign Super Admin role to the first user if exists
        $user = User::first();
        if ($user) {
            $user->assignRole($adminRole);
        }
    }
}
