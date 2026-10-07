<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use App\Services\TwoFactorService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TwoFactorTest extends TestCase
{
    use RefreshDatabase;

    public function test_totp_matches_the_rfc_sha1_test_vector(): void
    {
        $service = app(TwoFactorService::class);

        $this->assertTrue($service->verifyCode(
            'GEZDGNBVGY3TQOJQGEZDGNBVGY3TQOJQ',
            '287082',
            59,
        ));
    }

    public function test_user_can_enable_two_factor_authentication_and_receive_recovery_codes(): void
    {
        $user = User::factory()->create();
        $service = app(TwoFactorService::class);

        $setup = $this->actingAs($user)
            ->postJson(route('two-factor.enable'))
            ->assertOk()
            ->assertJsonStructure(['secret']);

        $this->postJson(route('two-factor.confirm'), [
            'code' => $service->currentCode($setup->json('secret')),
        ])
            ->assertOk()
            ->assertJsonCount(8, 'recovery_codes');

        $this->assertNotNull($user->fresh()->two_factor_confirmed_at);
        $this->getJson(route('two-factor.show'))
            ->assertOk()
            ->assertJsonPath('two_factor_enabled', true);
    }

    public function test_two_factor_setup_rejects_an_invalid_authenticator_code(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->postJson(route('two-factor.enable'))->assertOk();
        $this->postJson(route('two-factor.confirm'), ['code' => '000000'])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('code');

        $this->assertNull($user->fresh()->two_factor_confirmed_at);
    }

    public function test_user_must_confirm_their_password_to_disable_two_factor(): void
    {
        $user = User::factory()->create();
        $user->forceFill([
            'two_factor_secret' => app(TwoFactorService::class)->generateSecret(),
            'two_factor_recovery_codes' => [],
            'two_factor_confirmed_at' => now(),
        ])->save();

        $this->actingAs($user)
            ->deleteJson(route('two-factor.disable'), ['current_password' => 'wrong-password'])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('current_password');

        $this->deleteJson(route('two-factor.disable'), ['current_password' => 'password'])
            ->assertOk()
            ->assertJsonPath('two_factor_enabled', false);

        $this->assertNull($user->fresh()->two_factor_secret);
        $this->assertNull($user->fresh()->two_factor_confirmed_at);
    }

    public function test_login_requires_a_second_factor_and_accepts_a_totp_code(): void
    {
        $user = User::factory()->create();
        $service = app(TwoFactorService::class);
        $secret = $service->generateSecret();
        $user->forceFill([
            'two_factor_secret' => $secret,
            'two_factor_confirmed_at' => now(),
        ])->save();

        $this->post('/login', ['email' => $user->email, 'password' => 'password'])
            ->assertRedirect(route('two-factor.challenge'));
        $this->assertGuest();
        $this->withoutVite()
            ->get(route('two-factor.challenge'))
            ->assertOk()
            ->assertInertia(fn ($page) => $page->component('Auth/TwoFactorChallenge'));

        $this->post(route('two-factor.challenge.verify'), [
            'code' => $service->currentCode($secret),
        ])->assertRedirect(route('dashboard', absolute: false));

        $this->assertAuthenticatedAs($user);
    }

    public function test_recovery_code_can_be_used_once_to_complete_login(): void
    {
        $user = User::factory()->create();
        $recoveryCode = 'A1B2C3D4E5F60708';
        $user->forceFill([
            'two_factor_secret' => app(TwoFactorService::class)->generateSecret(),
            'two_factor_recovery_codes' => [\Illuminate\Support\Facades\Hash::make($recoveryCode)],
            'two_factor_confirmed_at' => now(),
        ])->save();

        $this->post('/login', ['email' => $user->email, 'password' => 'password'])
            ->assertRedirect(route('two-factor.challenge'));
        $this->post(route('two-factor.challenge.verify'), ['code' => $recoveryCode])
            ->assertRedirect(route('dashboard', absolute: false));

        $this->assertAuthenticatedAs($user);
        $this->assertSame([], $user->fresh()->two_factor_recovery_codes);
    }
}
