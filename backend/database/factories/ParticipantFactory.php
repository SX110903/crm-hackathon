<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ParticipantFactory extends Factory
{
    public function definition(): array
    {
        return [
            'first_name'    => $this->faker->firstName(),
            'last_name'     => $this->faker->lastName(),
            'email'         => $this->faker->unique()->safeEmail(),
            'phone'         => $this->faker->phoneNumber(),
            'university'    => $this->faker->company() . ' University',
            'major'         => $this->faker->randomElement(['Computer Science', 'Engineering', 'Design']),
            'year_of_study' => $this->faker->numberBetween(1, 5),
        ];
    }
}
