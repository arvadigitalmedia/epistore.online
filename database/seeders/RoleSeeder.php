<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create Roles
        $roles = [
            'super_admin',
            'epi_staff',
            'distributor_owner',
            'distributor_staff',
        ];

        foreach ($roles as $roleName) {
            Role::firstOrCreate(['name' => $roleName, 'guard_name' => 'web']);
        }

        // Setup basic permissions (example placeholder)
        // Permission::create(['name' => 'manage users']);
        
        // Assign permissions to roles (example)
        // $role = Role::findByName('super_admin');
        // $role->givePermissionTo(Permission::all());
    }
}
