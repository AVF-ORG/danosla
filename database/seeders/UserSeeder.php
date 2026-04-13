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
        $users = [
            [
                'name' => 'Admin (Medjadji)',
                'email' => 'medjadjiabdelkadir22@gmail.com',
                'password' => \Illuminate\Support\Facades\Hash::make('12345678'),
                'role' => 'Super Admin',
            ],
            [
                'name' => 'Carrier Test 1',
                'email' => 'carrier_test_1@example.com',
                'password' => \Illuminate\Support\Facades\Hash::make('12345678'),
                'role' => 'carrier',
            ],
            [
                'name' => 'Shipper Test 1',
                'email' => 'shipper_test_1@example.com',
                'password' => \Illuminate\Support\Facades\Hash::make('12345678'),
                'role' => 'shipper',
            ],
        ];

        foreach ($users as $userData) {
            $roleName = $userData['role'];
            unset($userData['role']); // Remove from array so we can pass to firstOrCreate
            
            $user = \App\Models\User::firstOrCreate(
                ['email' => $userData['email']],
                $userData
            );

            $role = \Spatie\Permission\Models\Role::firstOrCreate(['name' => $roleName]);
            $user->assignRole($role);
        }
    }
}
