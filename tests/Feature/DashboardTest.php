<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\MenuSeeder;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolePermissionSeeder::class);
        $this->seed(MenuSeeder::class);
    }

    public function test_dashboard_is_available_to_regular_users(): void
    {
        $user = User::factory()->create();
        $user->assignRole('student');

        $this->actingAs($user)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->component('Dashboard')
                ->where('isAdmin', false)
                ->has('sidebar_menu', 2)
                ->where('sidebar_menu.0.key', 'dashboard')
                ->where('sidebar_menu.1.key', 'profile'));
    }

    public function test_dashboard_identifies_administrators(): void
    {
        $user = User::factory()->create();
        $user->assignRole('admin');

        $this->actingAs($user)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->component('Dashboard')
                ->where('isAdmin', true)
                ->has('sidebar_menu', 3)
                ->where('sidebar_menu.1.key', 'administration')
                ->has('sidebar_menu.1.children', 2));
    }

    public function test_direct_permissions_grant_matching_sidebar_items_without_a_role(): void
    {
        $user = User::factory()->create();
        $user->givePermissionTo(['view dashboard', 'manage users']);

        $this->actingAs($user)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->has('sidebar_menu', 2)
                ->where('sidebar_menu.0.key', 'dashboard')
                ->where('sidebar_menu.1.key', 'administration')
                ->has('sidebar_menu.1.children', 1)
                ->where('sidebar_menu.1.children.0.key', 'users')
                ->where('auth.canManageUsers', true));
    }
}
