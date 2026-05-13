<?php

namespace Database\Factories;

use App\Models\Team;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProjectFactory extends Factory
{
    public function definition(): array
    {
        return [
            'team_id'          => Team::factory(),
            'name'             => $this->faker->unique()->sentence(3),
            'description'      => $this->faker->paragraph(),
            'category'         => $this->faker->randomElement(['Web', 'Mobile', 'AI/ML', 'IoT', 'Blockchain']),
            'technology_stack' => $this->faker->randomElement(['Laravel', 'React', 'Vue', 'Django', 'Node.js']),
            'github_url'       => 'https://github.com/' . $this->faker->userName() . '/' . $this->faker->slug(),
            'demo_url'         => $this->faker->url(),
            'status'           => $this->faker->randomElement([
                'In Progress',
                'Submitted',
                'Under Review',
                'Evaluated',
                'Awarded',
            ]),
        ];
    }
}
