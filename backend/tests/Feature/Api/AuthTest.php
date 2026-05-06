<?php

namespace Tests\Feature\Api;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function user_can_login_with_valid_credentials(): void
    {
        $user = User::factory()->create([
            'email'     => 'test@test.com',
            'password'  => bcrypt('password'),
            'role'      => 'admin',
            'is_active' => true,
        ]);

        $this->postJson('/api/auth/login', ['email' => 'test@test.com', 'password' => 'password'])
            ->assertOk()
            ->assertJsonStructure(['data' => ['token', 'user']]);
    }

    /** @test */
    public function login_fails_with_invalid_credentials(): void
    {
        $this->postJson('/api/auth/login', ['email' => 'wrong@test.com', 'password' => 'wrong'])
            ->assertUnauthorized();
    }

    /** @test */
    public function authenticated_user_can_get_profile(): void
    {
        $user = User::factory()->create(['role' => 'admin']);

        $this->actingAs($user, 'sanctum')
            ->getJson('/api/auth/me')
            ->assertOk()
            ->assertJsonPath('data.email', $user->email);
    }

    /** @test */
    public function authenticated_user_can_logout(): void
    {
        $user = User::factory()->create(['role' => 'admin']);

        $this->actingAs($user, 'sanctum')
            ->postJson('/api/auth/logout')
            ->assertNoContent();
    }
}
