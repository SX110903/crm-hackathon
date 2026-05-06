<?php

namespace Database\Factories;

use App\Models\Judge;
use App\Models\Project;
use Illuminate\Database\Eloquent\Factories\Factory;

class EvaluationFactory extends Factory
{
    public function definition(): array
    {
        return [
            'project_id'         => Project::factory(),
            'judge_id'           => Judge::factory(),
            'innovation_score'   => $this->faker->randomFloat(1, 5.0, 10.0),
            'technical_score'    => $this->faker->randomFloat(1, 5.0, 10.0),
            'presentation_score' => $this->faker->randomFloat(1, 5.0, 10.0),
            'usability_score'    => $this->faker->randomFloat(1, 5.0, 10.0),
            'comments'           => $this->faker->optional()->sentence(),
        ];
    }
}
