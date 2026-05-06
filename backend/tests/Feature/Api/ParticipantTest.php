<?php

namespace Tests\Feature\Api;

use App\Models\Participant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ParticipantTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);
    }

    /** @test */
    public function unauthenticated_users_cannot_access_participants(): void
    {
        $this->getJson('/api/participants')->assertUnauthorized();
    }

    /** @test */
    public function can_list_participants(): void
    {
        Participant::factory()->count(3)->create();

        $this->actingAs($this->admin, 'sanctum')
            ->getJson('/api/participants')
            ->assertOk()
            ->assertJsonStructure(['data']);
    }

    /** @test */
    public function can_create_a_participant(): void
    {
        $payload = [
            'first_name' => 'Jane',
            'last_name'  => 'Doe',
            'email'      => 'jane@example.com',
            'phone'      => '555-0001',
        ];

        $this->actingAs($this->admin, 'sanctum')
            ->postJson('/api/participants', $payload)
            ->assertCreated()
            ->assertJsonPath('data.email', 'jane@example.com');

        $this->assertDatabaseHas('participants', ['email' => 'jane@example.com']);
    }

    /** @test */
    public function cannot_create_participant_with_duplicate_email(): void
    {
        Participant::factory()->create(['email' => 'duplicate@example.com']);

        $this->actingAs($this->admin, 'sanctum')
            ->postJson('/api/participants', [
                'first_name' => 'Test',
                'last_name'  => 'User',
                'email'      => 'duplicate@example.com',
            ])
            ->assertUnprocessable();
    }

    /** @test */
    public function can_show_a_participant(): void
    {
        $p = Participant::factory()->create();

        $this->actingAs($this->admin, 'sanctum')
            ->getJson("/api/participants/{$p->id}")
            ->assertOk()
            ->assertJsonPath('data.id', $p->id);
    }

    /** @test */
    public function can_update_a_participant(): void
    {
        $p = Participant::factory()->create();

        $this->actingAs($this->admin, 'sanctum')
            ->putJson("/api/participants/{$p->id}", [
                'first_name' => 'Updated',
                'last_name'  => 'Name',
                'email'      => $p->email,
            ])
            ->assertOk()
            ->assertJsonPath('data.first_name', 'Updated');
    }

    /** @test */
    public function can_delete_a_participant(): void
    {
        $p = Participant::factory()->create();

        $this->actingAs($this->admin, 'sanctum')
            ->deleteJson("/api/participants/{$p->id}")
            ->assertNoContent();

        $this->assertDatabaseMissing('participants', ['id' => $p->id]);
    }
}
