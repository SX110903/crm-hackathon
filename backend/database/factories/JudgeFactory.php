<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class JudgeFactory extends Factory
{
    public function definition(): array
    {
        return [
            'first_name'          => $this->faker->firstName(),
            'last_name'           => $this->faker->lastName(),
            'email'               => $this->faker->unique()->safeEmail(),
            'company'             => $this->faker->company(),
            'expertise'           => $this->faker->randomElement([
                'Machine Learning',
                'Web Development',
                'Mobile Apps',
                'Blockchain',
                'Cloud Computing',
                'Cybersecurity',
            ]),
            'years_of_experience' => $this->faker->numberBetween(1, 20),
        ];
    }
}
