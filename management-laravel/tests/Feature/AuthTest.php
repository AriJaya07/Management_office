<?php

declare(strict_types=1);

use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    $this->seed(RoleSeeder::class);
    $this->withHeader('Referer', 'http://localhost:3000');
});

it('registers a user with the employee role', function (): void {
    $response = $this->postJson('/api/register', [
        'name' => 'Test User',
        'email' => 'user@office.test',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ]);

    $response->assertCreated()
        ->assertJsonPath('user.email', 'user@office.test')
        ->assertJsonPath('user.roles.0', 'employee');

    $this->assertAuthenticated();
});

it('rejects registration with an already used email', function (): void {
    User::factory()->create(['email' => 'user@office.test']);

    $this->postJson('/api/register', [
        'name' => 'Test User',
        'email' => 'user@office.test',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ])->assertUnprocessable()->assertJsonValidationErrors('email');
});

it('logs in with valid credentials', function (): void {
    $user = User::factory()->create(['password' => 'password123']);

    $this->postJson('/api/login', [
        'email' => $user->email,
        'password' => 'password123',
    ])->assertOk()->assertJsonPath('user.email', $user->email);

    $this->assertAuthenticatedAs($user);
});

it('rejects login with a wrong password', function (): void {
    $user = User::factory()->create(['password' => 'password123']);

    $this->postJson('/api/login', [
        'email' => $user->email,
        'password' => 'wrong-password',
    ])->assertUnprocessable()->assertJsonValidationErrors('email');

    $this->assertGuest('web');
});

it('returns the authenticated user', function (): void {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->getJson('/api/user')
        ->assertOk()
        ->assertJsonPath('data.email', $user->email);
});

it('blocks guests from the user endpoint', function (): void {
    $this->getJson('/api/user')->assertUnauthorized();
});

it('logs out the authenticated user', function (): void {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->postJson('/api/logout')
        ->assertOk();

    $this->assertGuest('web');
});
