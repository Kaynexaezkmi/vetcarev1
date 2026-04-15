<?php

namespace Database\Factories;

use App\Models\Service;
use Illuminate\Database\Eloquent\Factories\Factory;

class ServiceFactory extends Factory
{
    protected $model = Service::class;

    public function definition(): array
    {
        return [
            'name' => fake()->randomElement([
                'General Checkup',
                'Vaccination',
                'Dental Cleaning',
                'Surgery Consultation',
                'Blood Test',
                'X-Ray',
                'Emergency Care',
                'Grooming',
                'Spay/Neuter',
                'Microchipping'
            ]),
            'description' => fake()->sentence(10),
            'price' => fake()->randomFloat(2, 25, 500),
            'duration' => fake()->randomElement([15, 30, 45, 60, 90]),
            'is_active' => true,
        ];
    }
}
