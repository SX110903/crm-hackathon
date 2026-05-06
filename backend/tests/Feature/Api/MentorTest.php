<?php

namespace Tests\Feature\Api;

use App\Models\Mentor;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MentorTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);
    }

    /** @test */
    public function unauthenticated_users_cannot_access_mentors(): void
    {
        $this->getJson('/api/mentors')->assertUnauthorized();
    }

    /** @test */
    public function can_list_mentors(): void
    {
        Mentor::factory()->count(3)->create();

        $this->actingAs($this->admin, 'sanctum')
            ->getJson('/api/mentors')
            ->assertOk()
            ->assertJsonStructure(['data']);
    }

    /** @test */
    public function can_create_a_mentor(): void
    {
        $payload = [
            'first_name' => 'Carlos',
            'last_name'  => 'Mentor',
            'email'      => 'carlos@mentors.com',
        ];

        $this->actingAs($this->admin, 'sanctum')
            ->postJson('/api/mentors', $payload)
            ->assertCreated()
            ->assertJsonPath('data.email', 'carlos@mentors.com');

        $this->assertDatabaseHas('mentors', ['email' => 'carlos@mentors.com']);
    }

    /** @test */
    public function cannot_create_mentor_with_duplicate_email(): void
    {
        Mentor::factory()->create(['email' => 'dup@mentors.com']);

        $this->actingAs($this->admin, 'sanctum')
            ->postJson('/api/mentors', [
                'first_name' => 'Dupe',
                'last_name'  => 'Mentor',
                'email'      => 'dup@mentors.com',
            ])
            ->assertUnprocessable();
    }

    /** @test */
    public function can_show_a_mentor(): void
    {
        $mentor = Mentor::factory()->create();

        $this->actingAs($this->admin, 'sanctum')
            ->getJson("/api/mentors/{$mentor->id}")
            ->assertOk()
            ->assertJsonPath('data.id', $mentor->id);
    }

    /** @test */
    public function can_update_a_mentor(): void
    {
        $mentor = Mentor::factory()->create();

        $this->actingAs($this->admin, 'sanctum')
            ->putJson("/api/mentors/{$mentor->id}", [
                'first_name' => 'Updated',
                'last_name'  => 'Mentor',
                'email'      => $mentor->email,
            ])
            ->assertOk()
            ->assertJsonPath('data.first_name', 'Updated');
    }

    /** @test */
    public function can_delete_a_mentor(): void
    {
        $mentor = Mentor::factory()->create();

        $this->actingAs($this->admin, 'sanctum')
            ->deleteJson("/api/mentors/{$mentor->id}")
            ->assertNoContent();

        $this->assertDatabaseMissing('mentors', ['id' => $mentor->id]);
    }
}
