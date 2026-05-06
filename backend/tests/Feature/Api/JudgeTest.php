<?php

namespace Tests\Feature\Api;

use App\Models\Judge;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class JudgeTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);
    }

    /** @test */
    public function unauthenticated_users_cannot_access_judges(): void
    {
        $this->getJson('/api/judges')->assertUnauthorized();
    }

    /** @test */
    public function can_list_judges(): void
    {
        Judge::factory()->count(3)->create();

        $this->actingAs($this->admin, 'sanctum')
            ->getJson('/api/judges')
            ->assertOk()
            ->assertJsonStructure(['data']);
    }

    /** @test */
    public function can_create_a_judge(): void
    {
        $payload = [
            'first_name' => 'Alice',
            'last_name'  => 'Smith',
            'email'      => 'alice@judges.com',
        ];

        $this->actingAs($this->admin, 'sanctum')
            ->postJson('/api/judges', $payload)
            ->assertCreated()
            ->assertJsonPath('data.email', 'alice@judges.com');

        $this->assertDatabaseHas('judges', ['email' => 'alice@judges.com']);
    }

    /** @test */
    public function cannot_create_judge_with_duplicate_email(): void
    {
        Judge::factory()->create(['email' => 'dup@judges.com']);

        $this->actingAs($this->admin, 'sanctum')
            ->postJson('/api/judges', [
                'first_name' => 'Bob',
                'last_name'  => 'Jones',
                'email'      => 'dup@judges.com',
            ])
            ->assertUnprocessable();
    }

    /** @test */
    public function can_show_a_judge(): void
    {
        $judge = Judge::factory()->create();

        $this->actingAs($this->admin, 'sanctum')
            ->getJson("/api/judges/{$judge->id}")
            ->assertOk()
            ->assertJsonPath('data.id', $judge->id);
    }

    /** @test */
    public function can_update_a_judge(): void
    {
        $judge = Judge::factory()->create();

        $this->actingAs($this->admin, 'sanctum')
            ->putJson("/api/judges/{$judge->id}", [
                'first_name' => 'Updated',
                'last_name'  => 'Judge',
                'email'      => $judge->email,
            ])
            ->assertOk()
            ->assertJsonPath('data.first_name', 'Updated');
    }

    /** @test */
    public function can_delete_a_judge(): void
    {
        $judge = Judge::factory()->create();

        $this->actingAs($this->admin, 'sanctum')
            ->deleteJson("/api/judges/{$judge->id}")
            ->assertNoContent();

        $this->assertDatabaseMissing('judges', ['id' => $judge->id]);
    }
}
