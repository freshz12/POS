<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use App\Models\User; // Ensure you include the User model

class PermissionsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $permissions = [
            'capsters_view' => 'capsters',
            'capsters_create' => 'capsters',
            'capsters_edit' => 'capsters',
            'capsters_delete' => 'capsters',

            'users_view' => 'users',
            'users_create' => 'users',
            'users_edit' => 'users',
            'users_delete' => 'users',

            'roles_view' => 'roles',
            'roles_create' => 'roles',
            'roles_edit' => 'roles',
            'roles_delete' => 'roles',

            'customers_view' => 'customers',
            'customers_create' => 'customers',
            'customers_edit' => 'customers',
            'customers_delete' => 'customers',

            'attendances_view' => 'attendances',
            'attendances_create' => 'attendances',
            'attendances_edit' => 'attendances',
            'attendances_delete' => 'attendances',

            'check_in' => 'attendances',

            'attendances_approval_view' => 'attendances',
            'attendances_approve_or_reject' => 'attendances',

            'promos_view' => 'promos',
            'promos_create' => 'promos',
            'promos_edit' => 'promos',
            'promos_delete' => 'promos',

            'products_view' => 'products',
            'products_create' => 'products',
            'products_edit' => 'products',
            'products_delete' => 'products',

            'POS' => 'POS',

            'transactions_view' => 'transactions',
            'transactions_create' => 'transactions',
            'transactions_edit' => 'transactions',
            'transactions_delete' => 'transactions',

            'appointments_view' => 'appointments',
            'appointments_create' => 'appointments',
            'appointments_edit' => 'appointments',
            'appointments_delete' => 'appointments',

            'main_dashboards_views' => 'dashboards',
            'transactions_dashboards_views' => 'dashboards',
            'capsters_dashboards_views' => 'dashboards',
            'products_dashboards_views' => 'dashboards',
            'customers_dashboards_views' => 'dashboards',
        ];

        // Create permissions
        foreach ($permissions as $permission => $type) {
            Permission::firstOrCreate(
                ['name' => $permission],
                [
                    'type' => $type,
                    'created_by' => 0,
                    'updated_by' => 0,
                ]
            );
        }

        // Create Admin role and assign all permissions
        $adminRole = Role::firstOrCreate(['name' => 'Admin']);

        // Assign all permissions to the Admin role
        $permissions = Permission::all(); // Get all permissions
        $adminRole->givePermissionTo($permissions);

        // Assign the Admin role to the user with ID 1
        $user = User::find(1); // Find the user by ID
        if ($user) {
            $user->assignRole('Admin'); // Assign the Admin role to the user
        }
    }
}