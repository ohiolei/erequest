<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class UserManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolePermissionSeeder::class);
    }

    public function test_admin_can_view_and_filter_users(): void
    {
        $admin = User::factory()->create(['name' => 'Portal Admin']);
        $admin->assignRole('admin');
        $student = User::factory()->create(['name' => 'Jamie Student']);
        $student->assignRole('student');

        $this->actingAs($admin)
            ->get(route('admin.users.index', ['search' => 'Jamie', 'role' => 'student']))
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->component('Admin/Users/Index')
                ->has('users.data', 1)
                ->where('users.data.0.name', 'Jamie Student')
                ->where('users.data.0.roles.0', 'student')
                ->where('filters.search', 'Jamie'));
    }

    public function test_student_cannot_manage_users(): void
    {
        $student = User::factory()->create();
        $student->assignRole('student');

        $this->actingAs($student)
            ->get(route('admin.users.index'))
            ->assertForbidden();
    }

    public function test_admin_can_create_a_user_with_a_role(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $this->actingAs($admin)
            ->post(route('admin.users.store'), [
                'name' => 'New Student',
                'email' => 'new.student@example.com',
                'matric_no' => 'tasfued/2026/100',
                'password' => 'temporary-password',
                'password_confirmation' => 'temporary-password',
                'roles' => ['student'],
            ])
            ->assertRedirect(route('admin.users.index'));

        $user = User::where('email', 'new.student@example.com')->firstOrFail();
        $this->assertSame('TASFUED/2026/100', $user->matric_no);
        $this->assertTrue($user->hasRole('student'));
    }

    public function test_admin_can_update_user_details_and_roles(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');
        $student = User::factory()->create(['email' => 'student@example.com']);
        $student->assignRole('student');

        $this->actingAs($admin)
            ->patch(route('admin.users.update', $student), [
                'name' => 'Updated Student',
                'email' => 'updated.student@example.com',
                'matric_no' => 'tasfued/2026/101',
                'roles' => ['admin'],
            ])
            ->assertRedirect();

        $student->refresh();
        $this->assertSame('Updated Student', $student->name);
        $this->assertSame('updated.student@example.com', $student->email);
        $this->assertSame('TASFUED/2026/101', $student->matric_no);
        $this->assertTrue($student->hasRole('admin'));
    }

    public function test_admin_cannot_delete_their_own_account(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $this->actingAs($admin)
            ->delete(route('admin.users.destroy', $admin))
            ->assertSessionHasErrors('user');
    }

    public function test_admin_can_delete_another_manager_while_retaining_access(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');
        $otherAdmin = User::factory()->create();
        $otherAdmin->assignRole(Role::findByName('admin'));

        $this->actingAs($admin)
            ->delete(route('admin.users.destroy', $otherAdmin))
            ->assertRedirect();

        $this->assertDatabaseMissing('users', ['id' => $otherAdmin->id]);
    }
}