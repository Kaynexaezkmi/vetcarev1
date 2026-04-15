<?php

namespace Database\Seeders;

use App\Models\Appointment;
use App\Models\Inquiry;
use App\Models\MedicalRecord;
use App\Models\Pet;
use App\Models\Reminder;
use App\Models\Service;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate([
            'email' => 'admin@vetcare.com',
        ], [
            'name' => 'Admin User',
            'password' => Hash::make('vetcare2026'),
            'phone' => '555-0100',
            'address' => '123 Vet Street',
            'role' => 'admin',
        ]);

        $user1 = User::updateOrCreate([
            'email' => 'john@example.com',
        ], [
            'name' => 'John Doe',
            'password' => Hash::make('vetcare2026'),
            'phone' => '555-0101',
            'address' => '456 Pet Avenue',
            'role' => 'user',
        ]);

        $user2 = User::updateOrCreate([
            'email' => 'jane@example.com',
        ], [
            'name' => 'Jane Smith',
            'password' => Hash::make('vetcare2026'),
            'phone' => '555-0102',
            'address' => '789 Animal Road',
            'role' => 'user',
        ]);

        $services = [
            ['name' => 'General Checkup', 'price' => 50.00, 'duration' => '30', 'is_active' => true],
            ['name' => 'Vaccination', 'price' => 75.00, 'duration' => '30', 'is_active' => true],
            ['name' => 'Dental Cleaning', 'price' => 150.00, 'duration' => '60', 'is_active' => true],
            ['name' => 'Surgery', 'price' => 500.00, 'duration' => '120', 'is_active' => true],
            ['name' => 'Grooming', 'price' => 45.00, 'duration' => '45', 'is_active' => true],
            ['name' => 'Emergency Care', 'price' => 200.00, 'duration' => '60', 'is_active' => true],
            ['name' => 'X-Ray', 'price' => 120.00, 'duration' => '30', 'is_active' => true],
            ['name' => 'Blood Test', 'price' => 80.00, 'duration' => '15', 'is_active' => true],
        ];

        $serviceModels = [];

        foreach ($services as $service) {
            $serviceModels[$service['name']] = Service::updateOrCreate(
                ['name' => $service['name']],
                $service
            );
        }

        $pets = [
            ['user_id' => $user1->id, 'name' => 'Buddy', 'type' => 'Dog', 'breed' => 'Golden Retriever'],
            ['user_id' => $user1->id, 'name' => 'Whiskers', 'type' => 'Cat', 'breed' => 'Persian'],
            ['user_id' => $user2->id, 'name' => 'Max', 'type' => 'Dog', 'breed' => 'German Shepherd'],
            ['user_id' => $user2->id, 'name' => 'Luna', 'type' => 'Cat', 'breed' => 'Siamese'],
            ['user_id' => $user1->id, 'name' => 'Charlie', 'type' => 'Rabbit', 'breed' => 'Holland Lop'],
        ];

        $petModels = [];

        foreach ($pets as $pet) {
            $petModels[$pet['name']] = Pet::updateOrCreate(
                ['user_id' => $pet['user_id'], 'name' => $pet['name']],
                $pet
            );
        }

        $appointmentData = [
            [
                'user_id' => $user1->id,
                'pet_name' => 'Buddy',
                'service_name' => 'General Checkup',
                'appointment_date' => now()->addDays(1)->toDateString(),
                'appointment_time' => '09:00',
                'reason' => 'Annual checkup',
                'status' => 'pending',
            ],
            [
                'user_id' => $user1->id,
                'pet_name' => 'Whiskers',
                'service_name' => 'Vaccination',
                'appointment_date' => now()->addDays(2)->toDateString(),
                'appointment_time' => '10:00',
                'reason' => 'Vaccination due',
                'status' => 'approved',
            ],
            [
                'user_id' => $user2->id,
                'pet_name' => 'Max',
                'service_name' => 'Dental Cleaning',
                'appointment_date' => now()->addDays(3)->toDateString(),
                'appointment_time' => '11:00',
                'reason' => 'Teeth cleaning needed',
                'status' => 'pending',
            ],
            [
                'user_id' => $user2->id,
                'pet_name' => 'Luna',
                'service_name' => 'General Checkup',
                'appointment_date' => now()->toDateString(),
                'appointment_time' => '14:00',
                'reason' => 'Skin problem check',
                'status' => 'approved',
            ],
            [
                'user_id' => $user1->id,
                'pet_name' => 'Buddy',
                'service_name' => 'Grooming',
                'appointment_date' => now()->subDays(5)->toDateString(),
                'appointment_time' => '09:00',
                'reason' => 'Nail trimming',
                'status' => 'completed',
            ],
        ];

        $appointmentModels = [];

        foreach ($appointmentData as $apt) {
            $appointment = Appointment::updateOrCreate(
                [
                    'user_id' => $apt['user_id'],
                    'pet_id' => $petModels[$apt['pet_name']]->id,
                    'appointment_date' => $apt['appointment_date'],
                    'appointment_time' => Carbon::parse($apt['appointment_time'])->format('H:i:s'),
                ],
                [
                    'service_id' => $serviceModels[$apt['service_name']]->id,
                    'reason' => $apt['reason'],
                    'status' => $apt['status'],
                ]
            );

            $appointmentModels[$apt['pet_name'] . '|' . $apt['appointment_date'] . '|' . $apt['appointment_time']] = $appointment;
        }

        $records = [
            [
                'pet_name' => 'Buddy',
                'appointment_key' => 'Buddy|' . now()->subDays(5)->toDateString() . '|09:00',
                'title' => 'Nail Trim',
                'diagnosis' => 'Healthy',
                'treatment' => 'Nail trim completed',
                'notes' => 'No issues found',
                'record_date' => now()->subDays(5)->toDateString(),
            ],
            [
                'pet_name' => 'Buddy',
                'appointment_key' => null,
                'title' => 'Annual Vaccination',
                'diagnosis' => 'Annual vaccination',
                'treatment' => 'Rabies vaccine administered',
                'notes' => 'Next due in 1 year',
                'record_date' => now()->subMonths(6)->toDateString(),
            ],
        ];

        foreach ($records as $record) {
            MedicalRecord::updateOrCreate(
                [
                    'pet_id' => $petModels[$record['pet_name']]->id,
                    'title' => $record['title'],
                    'record_date' => $record['record_date'],
                ],
                [
                    'appointment_id' => $record['appointment_key'] ? ($appointmentModels[$record['appointment_key']]->id ?? null) : null,
                    'diagnosis' => $record['diagnosis'],
                    'treatment' => $record['treatment'],
                    'notes' => $record['notes'],
                ]
            );
        }

        Inquiry::updateOrCreate([
            'email' => 'mike@email.com',
            'message' => 'Do you offer weekend appointments?',
        ], [
            'name' => 'Mike Johnson',
            'phone' => '555-0199',
            'is_read' => false,
        ]);

        Inquiry::updateOrCreate([
            'email' => 'sarah@email.com',
            'message' => 'What are your emergency care rates?',
        ], [
            'name' => 'Sarah Wilson',
            'phone' => '555-0200',
            'is_read' => true,
        ]);

        Reminder::updateOrCreate([
            'user_id' => $user1->id,
            'appointment_id' => $appointmentModels['Buddy|' . now()->addDays(1)->toDateString() . '|09:00']->id,
            'type' => 'email',
            'send_at' => Carbon::parse($appointmentData[0]['appointment_date'])->subDay(),
        ], [
            'is_sent' => false,
        ]);
    }
}
