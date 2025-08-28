<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create permissions
        $permissions = [
            'manage-organizations',
            'manage-contacts',
            'view-contacts',
            'manage-own-notes',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Create Admin role with all permissions
        $adminRole = Role::firstOrCreate(['name' => 'Admin']);
        $adminRole->syncPermissions([
            'manage-organizations',
            'manage-contacts',
        ]);

        // Create Member role with limited permissions
        $memberRole = Role::firstOrCreate(['name' => 'Member']);
        $memberRole->syncPermissions([
            'view-contacts',
            'manage-own-notes',
        ]);
    }
}
