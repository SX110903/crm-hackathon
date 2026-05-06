<?php

namespace Tests\Feature\Api;

use App\Models\Evaluation;
use App\Models\Judge;
use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EvaluationTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);
    }

    /** @test */
    public function unauthenticated_users_cannot_access_evaluations(): void
    {
        $this->getJson('/api/evaluations')->assertUnauthorized();
    }

    /** @test */
    public function can_list_evaluations(): void
    {
        Evaluation::factory()->count(3)->create();

        $this->actingAs($this->admin, 'sanctum')
            ->getJson('/api/evaluations')
            ->assertOk()
            ->assertJsonStructure(['data']);
    }

    /** @test */
    public function can_create_an_evaluation(): void
    {
        $project = Project::factory()->create();
        $judge   = Judge::factory()->create();

        $payload = [
            'project_id'         => $project->id,
            'judge_id'           => $judge->id,
            'innovation_score'   => 8.5,
            'technical_score'    => 9.0,
            'presentation_score' => 7.5,
            'usability_score'    => 8.0,
        ];

        $this->actingAs($this->admin, 'sanctum')
            ->postJson('/api/evaluations', $payload)
            ->assertCreated()
            ->assertJsonPath('data.project_id', $project->id)
            ->assertJsonPath('data.judge_id', $judge->id);

        $this->assertDatabaseHas('evaluations', [
            'project_id' => $project->id,
            'judge_id'   => $judge->id,
        ]);
    }

    /** @test */
    public function cannot_create_evaluation_with_scores_out_of_range(): void
    {
        $project = Project::factory()->create();
        $judge   = Judge::factory()->create();

        $this->actingAs($this->admin, 'sanctum')
            ->postJson('/api/evaluations', [
                'project_id'         => $project->id,
                'judge_id'           => $judge->id,
                'innovation_score'   => 15,  // out of range
                'technical_score'    => 9.0,
                'presentation_score' => 7.5,
                'usability_score'    => 8.0,
            ])
            ->assertUnprocessable();
    }

    /** @test */
    public function can_show_an_evaluation(): void
    {
        $evaluation = Evaluation::factory()->create();

        $this->actingAs($this->admin, 'sanctum')
            ->getJson("/api/evaluations/{$evaluation->id}")
            ->assertOk()
            ->assertJsonPath('data.id', $evaluation->id);
    }
}
