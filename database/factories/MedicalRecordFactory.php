<?php

namespace Database\Factories;

use App\Models\MedicalRecord;
use App\Models\Pet;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class MedicalRecordFactory extends Factory
{
    protected $model = MedicalRecord::class;

    public function definition(): array
    {
        return [
            'pet_id' => Pet::factory(),
            'appointment_id' => null,
            'created_by' => User::factory(),
            'title' => fake()->randomElement([
                'Annual Checkup',
                'Vaccination Record',
                'Dental Examination',
                'Surgery Report',
                'Blood Test Results',
                'Follow-up Visit',
                'Injury Treatment',
                'Wellness Exam'
            ]),
            'diagnosis' => fake()->optional()->sentence(),
            'treatment' => fake()->optional()->sentence(),
            'notes' => fake()->optional()->paragraph(),
            'file_path' => null,
            'file_type' => null,
            'record_date' => fake()->dateTimeBetween('-1 year', 'now'),
        ];
    }
}
