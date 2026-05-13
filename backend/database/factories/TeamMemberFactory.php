<?php

namespace Database\Factories;

use App\Models\Participant;
use App\Models\Team;
use Illuminate\Database\Eloquent\Factories\Factory;

class TeamMemberFactory extends Factory
{
    public function definition(): array
    {
        return [
            'team_id'        => Team::factory(),
            'participant_id' => Participant::factory(),
            'role'           => 'Developer',
        ];
    }
}
