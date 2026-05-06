<?php

namespace Tests\Feature\Api;

use App\Models\Participant;
use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TeamTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);
    }

    /** @test */
    public function unauthenticated_users_cannot_access_teams(): void
    {
        $this->getJson('/api/teams')->assertUnauthorized();
    }

    /** @test */
    public function can_list_teams(): void
    {
        Team::factory()->count(3)->create();

        $this->actingAs($this->admin, 'sanctum')
            ->getJson('/api/teams')
            ->assertOk()
            ->assertJsonStructure(['data']);
    }

    /** @test */
    public function can_create_a_team(): void
    {
        $payload = [
            'name'        => 'Alpha Team',
            'max_members' => 5,
        ];

        $this->actingAs($this->admin, 'sanctum')
            ->postJson('/api/teams', $payload)
            ->assertCreated()
            ->assertJsonPath('data.name', 'Alpha Team');

        $this->assertDatabaseHas('teams', ['name' => 'Alpha Team']);
    }

    /** @test */
    public function cannot_create_team_without_name(): void
    {
        $this->actingAs($this->admin, 'sanctum')
            ->postJson('/api/teams', ['max_members' => 5])
            ->assertUnprocessable();
    }

    /** @test */
    public function can_show_a_team(): void
    {
        $team = Team::factory()->create();

        $this->actingAs($this->admin, 'sanctum')
            ->getJson("/api/teams/{$team->id}")
            ->assertOk()
            ->assertJsonPath('data.id', $team->id);
    }

    /** @test */
    public function can_update_a_team(): void
    {
        $team = Team::factory()->create();

        $this->actingAs($this->admin, 'sanctum')
            ->putJson("/api/teams/{$team->id}", [
                'name'        => 'Updated Team',
                'max_members' => 8,
            ])
            ->assertOk()
            ->assertJsonPath('data.name', 'Updated Team');
    }

    /** @test */
    public function can_delete_a_team(): void
    {
        $team = Team::factory()->create();

        $this->actingAs($this->admin, 'sanctum')
            ->deleteJson("/api/teams/{$team->id}")
            ->assertNoContent();

        $this->assertDatabaseMissing('teams', ['id' => $team->id]);
    }

    /** @test */
    public function can_add_member_to_team(): void
    {
        $team        = Team::factory()->create();
        $participant = Participant::factory()->create();

        $this->actingAs($this->admin, 'sanctum')
            ->postJson("/api/teams/{$team->id}/members", [
                'participant_id' => $participant->id,
                'role'           => 'developer',
            ])
            ->assertCreated();

        $this->assertDatabaseHas('team_members', [
            'team_id'        => $team->id,
            'participant_id' => $participant->id,
        ]);
    }

    /** @test */
    public function can_remove_member_from_team(): void
    {
        $team        = Team::factory()->create();
        $participant = Participant::factory()->create();

        // Add the member first
        $this->actingAs($this->admin, 'sanctum')
            ->postJson("/api/teams/{$team->id}/members", [
                'participant_id' => $participant->id,
                'role'           => 'developer',
            ]);

        // Now remove
        $this->actingAs($this->admin, 'sanctum')
            ->deleteJson("/api/teams/{$team->id}/members/{$participant->id}")
            ->assertNoContent();

        $this->assertDatabaseMissing('team_members', [
            'team_id'        => $team->id,
            'participant_id' => $participant->id,
        ]);
    }
}
