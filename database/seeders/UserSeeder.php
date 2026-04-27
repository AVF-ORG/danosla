<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Create the specific Admin user
        $admin = \App\Models\User::firstOrCreate(
            ['email' => 'medjadjiabdelkadir22@gmail.com'],
            [
                'name' => 'Admin (Medjadji)',
                'password' => \Illuminate\Support\Facades\Hash::make('12345678'),
            ]
        );
        
        // Ensure roles exist before assigning
        $superAdminRole = \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'Super Admin']);
        $adminRole = \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'admin']);
        
        $admin->assignRole($superAdminRole);
        $admin->assignRole($adminRole);

        // 2. Create 10 Carriers
        \App\Models\User::factory(10)->create()->each(function ($user) {
            $role = \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'carrier']);
            $user->assignRole($role);
        });

        // 3. Create 10 Shippers
        \App\Models\User::factory(10)->create()->each(function ($user) {
            $role = \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'shipper']);
            $user->assignRole($role);
        });
    }
}
