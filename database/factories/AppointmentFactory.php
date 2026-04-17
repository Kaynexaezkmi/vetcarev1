<?php

namespace Database\Factories;

use App\Models\Appointment;
use App\Models\Pet;
use App\Models\Service;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Carbon\Carbon;

class AppointmentFactory extends Factory
{
    protected $model = Appointment::class;

    public function definition(): array
    {
        $date = fake()->dateTimeBetween('-1 month', '+2 months');
        $status = fake()->randomElement(['pending', 'pending', 'pending', 'approved', 'completed', 'cancelled']);

        return [
            'user_id' => Pet::factory()->create()->user_id,
            'pet_id' => Pet::factory(),
            'service_id' => Service::factory(),
            'appointment_date' => $date,
            'appointment_time' => fake()->time('H:i'),
            'status' => $status,
            'reason' => fake()->sentence(5),
            'notes' => fake()->optional()->sentence(),
            'rescheduled' => false,
            'approved_at' => $status === 'approved' ? Carbon::now() : null,
            'completed_at' => $status === 'completed' ? Carbon::now() : null,
        ];
    }

    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'pending',
        ]);
    }

    public function approved(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'approved',
            'approved_at' => Carbon::now(),
        ]);
    }

    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'completed',
            'approved_at' => Carbon::now()->subDay(),
            'completed_at' => Carbon::now(),
        ]);
    }
}
