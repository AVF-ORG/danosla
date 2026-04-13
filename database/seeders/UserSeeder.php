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
                'name' => 'Transporter Test 1',
                'email' => 'transporter_test_1@example.com',
                'password' => \Illuminate\Support\Facades\Hash::make('12345678'),
                'role' => 'transporter',
            ],
            [
                'name' => 'Customer Transporter Test 1',
                'email' => 'customer_transporter_test_1@example.com',
                'password' => \Illuminate\Support\Facades\Hash::make('12345678'),
                'role' => 'customer-transporter',
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
