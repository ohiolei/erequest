<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $permissions = [
            'view dashboard',
            'create requests',
            'view own requests',
            'update own requests',
            'view all requests',
            'manage requests',
            'manage users',
            'manage roles',
        ];

        foreach ($permissions as $permission) {
            Permission::findOrCreate($permission, 'web');
        }

        $admin = Role::findOrCreate('admin', 'web');
        $student = Role::findOrCreate('student', 'web');

        $admin->syncPermissions($permissions);
        $student->syncPermissions([
            'view dashboard',
            'create requests',
            'view own requests',
            'update own requests',
        ]);

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
}
