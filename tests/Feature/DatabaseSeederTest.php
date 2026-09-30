<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DatabaseSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_database_seeder_creates_admin_and_student_accounts_with_distinct_permissions(): void
    {
        $this->seed();

        $admin = User::role('admin')->firstOrFail();
        $student = User::role('student')->firstOrFail();

        $this->assertTrue($admin->hasRole('admin'));
        $this->assertTrue($admin->can('manage users'));
        $this->assertTrue($student->hasRole('student'));
        $this->assertTrue($student->can('create requests'));
        $this->assertFalse($student->can('manage users'));
        $this->assertDatabaseCount('menu_items', 5);

        $this->seed();

        $this->assertDatabaseCount('users', 2);
        $this->assertDatabaseCount('menu_items', 5);
    }
}
