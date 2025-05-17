<?php

namespace Tests\Feature;

use App\Models\Registration;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

uses(RefreshDatabase::class);

// Testes para o método store do RegistrationController
describe('Registration Creation (Store Endpoint)', function () {
    it('should create a registration with valid data', function () {
        $registrationData = [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123', // Add this line
        ];

        $response = $this->post(route('registrations.store'), $registrationData);

        $response->assertRedirect(route('main.index'));
        $response->assertSessionHas('success', 'Registration successful!');

        $this->assertDatabaseHas('registrations', [
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        $registration = Registration::where('email', 'test@example.com')->first();
        expect($registration)->not->toBeNull();
        expect(Hash::check('password123', $registration->password))->toBeTrue();
    });

    it('should fail validation if name is not provided', function () {
        $response = $this->post(route('registrations.store'), [
            'email' => 'test@example.com',
            'password' => 'password123',
        ]);
        $response->assertSessionHasErrors('name');
    });

    it('should fail validation if email is not provided', function () {
        $response = $this->post(route('registrations.store'), [
            'name' => 'Test User',
            'password' => 'password123',
        ]);
        $response->assertSessionHasErrors('email');
    });

    it('should fail validation if email is invalid', function () {
        $response = $this->post(route('registrations.store'), [
            'name' => 'Test User',
            'email' => 'invalid-email',
            'password' => 'password123',
        ]);
        $response->assertSessionHasErrors('email');
    });

    it('should fail validation if email already exists', function () {
        Registration::factory()->create(['email' => 'existing@example.com']);
        $response = $this->post(route('registrations.store'), [
            'name' => 'Test User',
            'email' => 'existing@example.com',
            'password' => 'password123',
        ]);
        $response->assertSessionHasErrors('email');
    });

    it('should fail validation if password is not provided', function () {
        $response = $this->post(route('registrations.store'), [
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);
        $response->assertSessionHasErrors('password');
    });

    it('should fail validation if password is too short', function () {
        $response = $this->post(route('registrations.store'), [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'short', // Less than 8 characters
        ]);
        $response->assertSessionHasErrors('password');
    });
});

// Testes para o método validateEmail do RegistrationController
describe('Email Validation Endpoint (/api/validate-email)', function () {
    it('should return exists true if email is already registered', function () {
        Registration::factory()->create(['email' => 'taken@example.com']);

        $response = $this->postJson(route('api.validate.email'), ['email' => 'taken@example.com']);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors([
            'email' => 'This email address is already taken.',
        ]);
    });

    it('should return exists false if email is not registered', function () {
        $response = $this->postJson(route('api.validate.email'), ['email' => 'available@example.com']);

        $response->assertOk();
        $response->assertJson(['exists' => false]);
    });

    it('should fail validation if email is not provided to validate-email endpoint', function () {
        $response = $this->postJson(route('api.validate.email'), []);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('email');
    });

    it('should fail validation if email is invalid for validate-email endpoint', function () {
        $response = $this->postJson(route('api.validate.email'), ['email' => 'not-an-email']);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('email');
    });
});
