<?php

use App\Models\MedicalRecord;
use App\Models\Pet;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('pet owner can view redesigned pet medical records without download actions', function () {
    $user = User::factory()->create();
    $pet = Pet::factory()->create([
        'user_id' => $user->id,
        'name' => 'Mochi',
        'breed' => 'Shih Tzu',
        'gender' => 'Female',
        'date_of_birth' => now()->subYears(2),
    ]);

    MedicalRecord::factory()->create([
        'pet_id' => $pet->id,
        'title' => 'Distemper Vaccine',
        'diagnosis' => 'Routine annual vaccination.',
        'treatment' => 'Vaccination',
        'record_date' => now()->subDay(),
        'next_call' => now()->addYear()->format('M d, Y'),
    ]);

    $response = $this->actingAs($user)->get(route('pets.records', $pet));

    $response
        ->assertOk()
        ->assertSee("Mochi's Medical Records")
        ->assertSee('Activity Logs')
        ->assertSee('Distemper Vaccine')
        ->assertDontSee('Download', false);
});

test('pet owner medical records index uses owner records layout without download actions', function () {
    $user = User::factory()->create();
    $pet = Pet::factory()->create([
        'user_id' => $user->id,
        'name' => 'Tim1',
        'type' => 'Cat',
    ]);

    MedicalRecord::factory()->create([
        'pet_id' => $pet->id,
        'title' => 'Surgery Clearance',
        'diagnosis' => 'MAY TUMOR',
        'treatment' => 'TANGGALIN',
        'record_date' => now()->subDays(2),
        'next_call' => 'FOR SURGERY',
    ]);

    $response = $this->actingAs($user)->get(route('medical-records'));

    $response
        ->assertOk()
        ->assertSee('All Pets Medical Records')
        ->assertSee('Activity Logs')
        ->assertSee('Surgery Clearance')
        ->assertDontSee('Download', false);
});
