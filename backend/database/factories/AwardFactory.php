<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class AwardFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name'         => $this->faker->unique()->words(3, true),
            'category'     => $this->faker->randomElement([
                'Innovation',
                'Technical Excellence',
                'Best Design',
                'Social Impact',
                'Best Pitch',
            ]),
            'prize'        => '$' . $this->faker->numberBetween(500, 10000),
            'project_id'   => null,
            'awarded_date' => null,
        ];
    }
}
