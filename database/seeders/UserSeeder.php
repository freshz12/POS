<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class UserSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        \App\Models\User::create([
            'name' => 'Admin',
            'username' => 'admin@gmail.com',
            'password' => Hash::make('password'),
            'created_by' => 0,
            'updated_by' => 0,
        ]);

        $adminRole = Role::firstOrCreate(
            ['name' => 'Admin'],
            [
                'created_by' => 0, // Set created_by to 0
                'updated_by' => 0, // Set updated_by to 0
            ]
        );

        // Assign all permissions to the Admin role
        $permissions = Permission::all(); // Get all permissions
        $adminRole->givePermissionTo($permissions);

        // Assign the Admin role to the user with ID 1
        $user = User::where('name', 'Admin')->first(); // Find the user by ID
        if ($user) {
            $user->assignRole('Admin'); // Assign the Admin role to the user
        }
    }
}
