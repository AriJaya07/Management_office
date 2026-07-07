<?php

declare(strict_types=1);

use App\Enums\RoleName;
use App\Models\Department;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    $this->seed(RoleSeeder::class);
    $this->withHeader('Referer', 'http://localhost:3000');

    $this->admin = User::factory()->create();
    $this->admin->assignRole(RoleName::Admin->value);

    $this->employee = User::factory()->create();
    $this->employee->assignRole(RoleName::Employee->value);
});

it('lists users for an admin with pagination meta', function (): void {
    User::factory()->count(3)->create();

    $this->actingAs($this->admin)
        ->getJson('/api/v1/admin/users')
        ->assertOk()
        ->assertJsonStructure(['data', 'meta' => ['current_page', 'total']]);
});

it('filters users by search term', function (): void {
    User::factory()->create(['name' => 'Zanzibar Unique']);

    $this->actingAs($this->admin)
        ->getJson('/api/v1/admin/users?search=Zanzibar')
        ->assertOk()
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.name', 'Zanzibar Unique');
});

it('blocks non-admin users from the admin area', function (): void {
    $this->actingAs($this->employee)
        ->getJson('/api/v1/admin/users')
        ->assertForbidden();
});

it('creates a user with a role and department', function (): void {
    $department = Department::factory()->create();

    $this->actingAs($this->admin)
        ->postJson('/api/v1/admin/users', [
            'name' => 'New Employee',
            'email' => 'new@office.test',
            'password' => 'password123',
            'role' => RoleName::Employee->value,
            'department_id' => $department->id,
        ])
        ->assertCreated()
        ->assertJsonPath('data.email', 'new@office.test')
        ->assertJsonPath('data.roles.0', 'employee')
        ->assertJsonPath('data.department.id', $department->id);

    expect(User::where('email', 'new@office.test')->exists())->toBeTrue();
});

it('rejects user creation with an invalid role', function (): void {
    $this->actingAs($this->admin)
        ->postJson('/api/v1/admin/users', [
            'name' => 'New Employee',
            'email' => 'new@office.test',
            'password' => 'password123',
            'role' => 'superhero',
        ])
        ->assertUnprocessable()
        ->assertJsonValidationErrors('role');
});

it('updates a user and syncs the role', function (): void {
    $this->actingAs($this->admin)
        ->putJson("/api/v1/admin/users/{$this->employee->id}", [
            'name' => 'Renamed Person',
            'role' => RoleName::Manager->value,
        ])
        ->assertOk()
        ->assertJsonPath('data.name', 'Renamed Person')
        ->assertJsonPath('data.roles.0', 'manager');
});

it('deactivates a user', function (): void {
    $this->actingAs($this->admin)
        ->putJson("/api/v1/admin/users/{$this->employee->id}", ['is_active' => false])
        ->assertOk()
        ->assertJsonPath('data.is_active', false);
});

it('soft deletes a user', function (): void {
    $this->actingAs($this->admin)
        ->deleteJson("/api/v1/admin/users/{$this->employee->id}")
        ->assertOk();

    $this->assertSoftDeleted($this->employee);
});

it('prevents an admin from deleting themselves', function (): void {
    $this->actingAs($this->admin)
        ->deleteJson("/api/v1/admin/users/{$this->admin->id}")
        ->assertForbidden();
});

it('returns dashboard stats for an admin', function (): void {
    Department::factory()->count(2)->create();

    $this->actingAs($this->admin)
        ->getJson('/api/v1/admin/stats')
        ->assertOk()
        ->assertJsonStructure(['data' => ['total_users', 'active_users', 'total_departments', 'total_positions']]);
});
