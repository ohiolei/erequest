<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_screen_can_be_rendered(): void
    {
        $response = $this->get('/register');

        $response->assertStatus(200);
    }

    public function test_new_users_can_register(): void
    {
        $this->seed(RolePermissionSeeder::class);

        $response = $this->post('/register', [
            'fname' => 'Test',
            'mname' => 'Middle',
            'lname' => 'User',
            'matric_no' => 'tasfued/2026/001',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $this->assertAuthenticated();
        $this->assertDatabaseHas('users', [
            'fname' => 'Test',
            'mname' => 'Middle',
            'lname' => 'User',
            'email' => 'test@example.com',
            'matric_no' => 'TASFUED/2026/001',
        ]);
        $this->assertTrue(User::where('email', 'test@example.com')->firstOrFail()->hasRole('student'));
        $response->assertRedirect(route('dashboard', absolute: false));
    }

    public function test_matric_numbers_are_unique_without_case_sensitivity(): void
    {
        User::factory()->create(['matric_no' => 'TASFUED/2026/001']);

        $response = $this->post('/register', [
            'fname' => 'Another',
            'lname' => 'User',
            'matric_no' => 'tasfued/2026/001',
            'email' => 'another@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertSessionHasErrors('matric_no');
        $this->assertGuest();
    }
}
