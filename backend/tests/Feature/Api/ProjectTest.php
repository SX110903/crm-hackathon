<?php

namespace Tests\Feature\Api;

use App\Models\Project;
use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProjectTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);
    }

    /** @test */
    public function unauthenticated_users_cannot_access_projects(): void
    {
        $this->getJson('/api/projects')->assertUnauthorized();
    }

    /** @test */
    public function can_list_projects(): void
    {
        Project::factory()->count(3)->create();

        $this->actingAs($this->admin, 'sanctum')
            ->getJson('/api/projects')
            ->assertOk()
            ->assertJsonStructure(['data']);
    }

    /** @test */
    public function can_create_a_project(): void
    {
        $team = Team::factory()->create();

        $payload = [
            'team_id' => $team->id,
            'name'    => 'My Project',
            'status'  => 'In Progress',
        ];

        $this->actingAs($this->admin, 'sanctum')
            ->postJson('/api/projects', $payload)
            ->assertCreated()
            ->assertJsonPath('data.name', 'My Project');

        $this->assertDatabaseHas('projects', ['name' => 'My Project']);
    }

    /** @test */
    public function cannot_create_project_without_required_fields(): void
    {
        $this->actingAs($this->admin, 'sanctum')
            ->postJson('/api/projects', [])
            ->assertUnprocessable();
    }

    /** @test */
    public function can_show_a_project(): void
    {
        $project = Project::factory()->create();

        $this->actingAs($this->admin, 'sanctum')
            ->getJson("/api/projects/{$project->id}")
            ->assertOk()
            ->assertJsonPath('data.id', $project->id);
    }

    /** @test */
    public function can_update_a_project(): void
    {
        $project = Project::factory()->create();

        $this->actingAs($this->admin, 'sanctum')
            ->putJson("/api/projects/{$project->id}", [
                'team_id' => $project->team_id,
                'name'    => 'Updated Project',
                'status'  => 'Submitted',
            ])
            ->assertOk()
            ->assertJsonPath('data.name', 'Updated Project');
    }

    /** @test */
    public function can_delete_a_project(): void
    {
        $project = Project::factory()->create();

        $this->actingAs($this->admin, 'sanctum')
            ->deleteJson("/api/projects/{$project->id}")
            ->assertNoContent();

        $this->assertDatabaseMissing('projects', ['id' => $project->id]);
    }
}
