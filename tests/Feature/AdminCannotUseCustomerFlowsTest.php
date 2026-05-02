<?php

use App\Models\Pet;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('admin cannot open customer appointment booking page', function () {
    $admin = User::factory()->create([
        'role' => 'admin',
    ]);

    $response = $this->actingAs($admin)->get(route('appointments.create'));

    $response
        ->assertRedirect(route('admin.appointments.index'))
        ->assertSessionHas('error', 'Admins cannot book customer appointments.');
});

test('admin cannot create a personal pet profile from settings', function () {
    $admin = User::factory()->create([
        'role' => 'admin',
    ]);

    $response = $this->actingAs($admin)->post(route('pets.store'), [
        'name' => 'Milo',
        'type' => 'Cat',
        'breed' => 'Persian',
        'gender' => 'Male',
    ]);

    $response
        ->assertRedirect(route('dashboard'))
        ->assertSessionHas('error', 'Admins cannot add personal pet profiles.');

    expect(Pet::query()->where('user_id', $admin->id)->exists())->toBeFalse();
});

test('admin settings page does not show pet profile form', function () {
    $admin = User::factory()->create([
        'role' => 'admin',
    ]);

    $response = $this->actingAs($admin)->get(route('settings'));

    $response
        ->assertOk()
        ->assertDontSee('Add Pet Profile')
        ->assertDontSee('Pet Profile');
});
