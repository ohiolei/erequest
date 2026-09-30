<?php

namespace Database\Seeders;

use App\Models\MenuItem;
use Illuminate\Database\Seeder;

class MenuSeeder extends Seeder
{
    public function run(): void
    {
        $items = [
            [
                'key' => 'dashboard',
                'label' => 'Dashboard',
                'route' => 'dashboard',
                'parent_key' => null,
                'roles' => ['admin', 'student'],
                'permission' => 'view dashboard',
                'sort_order' => 10,
            ],
            [
                'key' => 'administration',
                'label' => 'Administration',
                'route' => null,
                'parent_key' => null,
                'roles' => ['admin'],
                'permission' => null,
                'sort_order' => 20,
            ],
            [
                'key' => 'users',
                'label' => 'User Manager',
                'route' => 'admin.users.index',
                'parent_key' => 'administration',
                'roles' => ['admin'],
                'permission' => 'manage users',
                'sort_order' => 10,
            ],
            [
                'key' => 'roles',
                'label' => 'Roles & Permissions',
                'route' => 'admin.roles.index',
                'parent_key' => 'administration',
                'roles' => ['admin'],
                'permission' => 'manage roles',
                'sort_order' => 20,
            ],
            [
                'key' => 'profile',
                'label' => 'Profile',
                'route' => 'profile.edit',
                'parent_key' => null,
                'roles' => ['admin', 'student'],
                'permission' => null,
                'sort_order' => 30,
            ],
        ];

        foreach ($items as $item) {
            $parentKey = $item['parent_key'];
            unset($item['parent_key']);

            $item['parent_id'] = $parentKey
                ? MenuItem::query()->where('key', $parentKey)->value('id')
                : null;

            MenuItem::updateOrCreate(['key' => $item['key']], $item);
        }
    }
}
