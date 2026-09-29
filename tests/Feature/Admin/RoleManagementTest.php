<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class RoleManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolePermissionSeeder::class);
    }

    public function test_admin_can_view_roles_and_permissions(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $this->actingAs($admin)
            ->get(route('admin.roles.index'))
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->component('Admin/Roles/Index')
                ->has('roles', 2)
                ->has('permissions', 8)
                ->has('permissionRecords', 8));
    }

    public function test_student_cannot_manage_roles(): void
    {
        $student = User::factory()->create();
        $student->assignRole('student');

        $this->actingAs($student)
            ->get(route('admin.roles.index'))
            ->assertForbidden();
    }

    public function test_admin_can_create_a_role_with_permissions(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $this->actingAs($admin)
            ->post(route('admin.roles.store'), [
                'name' => 'request-reviewer',
                'permissions' => ['view all requests'],
            ])
            ->assertRedirect();

        $role = Role::findByName('request-reviewer');
        $this->assertTrue($role->hasPermissionTo('view all requests'));
    }

    public function test_admin_can_update_a_role_permissions(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');
        $studentRole = Role::findByName('student');

        $this->actingAs($admin)
            ->patch(route('admin.roles.update', $studentRole), [
                'name' => 'student',
                'permissions' => ['view dashboard'],
            ])
            ->assertRedirect();

        $this->assertTrue($studentRole->fresh()->hasPermissionTo('view dashboard'));
        $this->assertFalse($studentRole->fresh()->hasPermissionTo('create requests'));
    }

    public function test_admin_role_cannot_be_deleted(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $this->actingAs($admin)
            ->delete(route('admin.roles.destroy', Role::findByName('admin')))
            ->assertSessionHasErrors('role');
    }

    public function test_role_assigned_to_a_user_cannot_be_deleted(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');
        $student = User::factory()->create();
        $student->assignRole('student');

        $this->actingAs($admin)
            ->delete(route('admin.roles.destroy', Role::findByName('student')))
            ->assertSessionHasErrors('role');
    }

    public function test_admin_can_create_a_permission(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $this->actingAs($admin)
            ->post(route('admin.permissions.store'), ['name' => 'approve requests'])
            ->assertRedirect();

        $this->assertDatabaseHas('permissions', [
            'name' => 'approve requests',
            'guard_name' => 'web',
        ]);
    }

    public function test_admin_can_rename_a_permission_without_losing_role_assignments(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');
        $permission = Permission::findByName('create requests');

        $this->actingAs($admin)
            ->patch(route('admin.permissions.update', $permission), ['name' => 'submit requests'])
            ->assertRedirect();

        $this->assertTrue(Role::findByName('student')->fresh()->hasPermissionTo('submit requests'));
    }

    public function test_permission_in_use_cannot_be_deleted(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');
        $permission = Permission::findByName('create requests');

        $this->actingAs($admin)
            ->delete(route('admin.permissions.destroy', $permission))
            ->assertSessionHasErrors('permission');
    }

    public function test_unused_permission_can_be_deleted(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');
        $permission = Permission::create(['name' => 'temporary feature', 'guard_name' => 'web']);

        $this->actingAs($admin)
            ->delete(route('admin.permissions.destroy', $permission))
            ->assertRedirect();

        $this->assertDatabaseMissing('permissions', ['id' => $permission->id]);
    }
}
