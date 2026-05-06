<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class MentorFactory extends Factory
{
    public function definition(): array
    {
        return [
            'first_name'      => $this->faker->firstName(),
            'last_name'       => $this->faker->lastName(),
            'email'           => $this->faker->unique()->safeEmail(),
            'company'         => $this->faker->company(),
            'specialization'  => $this->faker->randomElement([
                'Frontend Development',
                'Backend Development',
                'DevOps',
                'Data Science',
                'UX/UI Design',
                'Product Management',
            ]),
            'available_slots' => $this->faker->numberBetween(1, 10),
        ];
    }
}
