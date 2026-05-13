<?php

namespace Tests\Feature\Api;

use App\Models\Award;
use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AwardTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);
    }

    /** @test */
    public function unauthenticated_users_cannot_access_awards(): void
    {
        $this->getJson('/api/awards')->assertUnauthorized();
    }

    /** @test */
    public function can_list_awards(): void
    {
        Award::factory()->count(3)->create();

        $this->actingAs($this->admin, 'sanctum')
            ->getJson('/api/awards')
            ->assertOk()
            ->assertJsonStructure(['data']);
    }

    /** @test */
    public function can_create_an_award(): void
    {
        $payload = [
            'name'     => 'Best Innovation',
            'category' => 'Innovation',
            'prize'    => '$1,000',
        ];

        $this->actingAs($this->admin, 'sanctum')
            ->postJson('/api/awards', $payload)
            ->assertCreated()
            ->assertJsonPath('data.name', 'Best Innovation');

        $this->assertDatabaseHas('awards', ['name' => 'Best Innovation']);
    }

    /** @test */
    public function cannot_create_award_without_name(): void
    {
        $this->actingAs($this->admin, 'sanctum')
            ->postJson('/api/awards', ['category' => 'Innovation', 'prize' => '$500'])
            ->assertUnprocessable();
    }

    /** @test */
    public function can_show_an_award(): void
    {
        $award = Award::factory()->create();

        $this->actingAs($this->admin, 'sanctum')
            ->getJson("/api/awards/{$award->id}")
            ->assertOk()
            ->assertJsonPath('data.id', $award->id);
    }

    /** @test */
    public function can_update_an_award(): void
    {
        $award = Award::factory()->create();

        $this->actingAs($this->admin, 'sanctum')
            ->putJson("/api/awards/{$award->id}", [
                'name'     => 'Updated Award',
                'category' => $award->category,
                'prize'    => $award->prize,
            ])
            ->assertOk()
            ->assertJsonPath('data.name', 'Updated Award');
    }

    /** @test */
    public function can_delete_an_award(): void
    {
        $award = Award::factory()->create();

        $this->actingAs($this->admin, 'sanctum')
            ->deleteJson("/api/awards/{$award->id}")
            ->assertNoContent();

        $this->assertDatabaseMissing('awards', ['id' => $award->id]);
    }

    /** @test */
    public function can_assign_award_to_project(): void
    {
        $award   = Award::factory()->create();
        $project = Project::factory()->create();

        $this->actingAs($this->admin, 'sanctum')
            ->postJson("/api/awards/{$award->id}/assign", [
                'project_id'   => $project->id,
                'awarded_date' => '2026-05-01',
            ])
            ->assertOk();

        $this->assertDatabaseHas('awards', [
            'id'         => $award->id,
            'project_id' => $project->id,
        ]);
    }
}
