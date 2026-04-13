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
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $adminRole->syncPermissions(Permission::all());

        $superAdminRole = Role::firstOrCreate(['name' => 'Super Admin']);
        $superAdminRole->syncPermissions(Permission::all());

        $managerRole = Role::firstOrCreate(['name' => 'Manager']);
        $managerRole->syncPermissions([
            'view-dashboard',
            'manage-sectors',
            'manage-regions',
            'manage-countries',
        ]);

        $transporterRole = Role::firstOrCreate(['name' => 'transporter']);
        $transporterRole->syncPermissions(['view-dashboard']);

        $customerRole = Role::firstOrCreate(['name' => 'customer-transporter']);
        $customerRole->syncPermissions(['view-dashboard']);

        $userRole = Role::firstOrCreate(['name' => 'User']);
        $userRole->syncPermissions(['view-dashboard']);

        // Assign Super Admin and admin role to the first user if exists
        $user = User::where('email', 'medjadjiabdelkadir22@gmail.com')->first() ?: User::first();
        if ($user) {
            $user->assignRole($superAdminRole);
            $user->assignRole($adminRole);
        }
    }
}
