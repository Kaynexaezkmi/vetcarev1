<?php

use App\Models\Appointment;
use App\Models\Pet;
use App\Models\Service;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('appointment payment reference must be exactly twelve digits', function () {
    $user = User::factory()->create();
    $pet = Pet::factory()->create([
        'user_id' => $user->id,
    ]);
    $service = Service::factory()->create();

    $response = $this->actingAs($user)->post(route('appointments.store'), [
        'pet_id' => $pet->id,
        'service_id' => $service->id,
        'appointment_date' => now()->addDay()->toDateString(),
        'appointment_time' => '09:00',
        'payment_reference' => 'ABC123456789',
        'terms_agreement' => '1',
    ]);

    $response->assertSessionHasErrors('payment_reference');
    expect(Appointment::query()->exists())->toBeFalse();
});

test('appointment payment reference stores twelve digit number without spaces', function () {
    $user = User::factory()->create();
    $pet = Pet::factory()->create([
        'user_id' => $user->id,
    ]);
    $service = Service::factory()->create();

    $response = $this->actingAs($user)->post(route('appointments.store'), [
        'pet_id' => $pet->id,
        'service_id' => $service->id,
        'appointment_date' => now()->addDay()->toDateString(),
        'appointment_time' => '09:00',
        'payment_reference' => '6123 4567 8901',
        'terms_agreement' => '1',
    ]);

    $response->assertRedirect(route('appointments.create'));
    $this->assertDatabaseHas('appointments', [
        'payment_reference' => '612345678901',
    ]);
});

test('schedule step requires date and time before payment step', function () {
    $user = User::factory()->create();
    Pet::factory()->create([
        'user_id' => $user->id,
    ]);
    Service::factory()->create();

    $response = $this->actingAs($user)->get(route('appointments.create'));

    $response
        ->assertOk()
        ->assertSee('Select an appointment date and time slot to continue.')
        ->assertSee('id="nextPaymentBtn"', false)
        ->assertSee('disabled', false);
});
