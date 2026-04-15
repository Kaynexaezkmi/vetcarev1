<?php

namespace Database\Factories;

use App\Models\Pet;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class PetFactory extends Factory
{
    protected $model = Pet::class;

    public function definition(): array
    {
        $types = ['Dog', 'Cat', 'Bird', 'Rabbit', 'Hamster', 'Fish', 'Reptile', 'Other'];
        $breeds = [
            'Dog' => ['Labrador Retriever', 'German Shepherd', 'Golden Retriever', 'Bulldog', 'Beagle', 'Poodle'],
            'Cat' => ['Persian', 'Maine Coon', 'Siamese', 'British Shorthair', 'Ragdoll', 'Bengal'],
            'Bird' => ['Parrot', 'Canary', 'Budgerigar', 'Cockatiel', 'Finch'],
            'Rabbit' => ['Holland Lop', 'Mini Rex', 'Flemish Giant'],
            'Hamster' => ['Syrian', 'Dwarf', 'Robo'],
        ];

        $type = fake()->randomElement($types);
        $breedOptions = $breeds[$type] ?? ['Mixed'];

        return [
            'user_id' => User::factory(),
            'name' => fake()->firstName() . ' ' . fake()->randomElement(['', ' ' . fake()->lastName()]),
            'type' => $type,
            'breed' => fake()->randomElement($breedOptions),
            'gender' => fake()->randomElement(['Male', 'Female']),
            'date_of_birth' => fake()->dateTimeBetween('-15 years', '-1 month'),
            'notes' => fake()->optional()->sentence(),
        ];
    }
}
