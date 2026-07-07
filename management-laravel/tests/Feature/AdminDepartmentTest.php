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
});

it('lists departments with user counts', function (): void {
    Department::factory()->count(3)->create();

    $this->actingAs($this->admin)
        ->getJson('/api/v1/admin/departments')
        ->assertOk()
        ->assertJsonCount(3, 'data')
        ->assertJsonStructure(['data' => [['id', 'name', 'code', 'users_count']], 'meta']);
});

it('creates a department', function (): void {
    $this->actingAs($this->admin)
        ->postJson('/api/v1/admin/departments', [
            'name' => 'Research & Development',
            'code' => 'RND',
        ])
        ->assertCreated()
        ->assertJsonPath('data.name', 'Research & Development')
        ->assertJsonPath('data.code', 'RND');
});

it('rejects a duplicate department code', function (): void {
    Department::factory()->create(['code' => 'RND']);

    $this->actingAs($this->admin)
        ->postJson('/api/v1/admin/departments', [
            'name' => 'Another Department',
            'code' => 'RND',
        ])
        ->assertUnprocessable()
        ->assertJsonValidationErrors('code');
});

it('updates a department and assigns a manager', function (): void {
    $department = Department::factory()->create();
    $manager = User::factory()->create();

    $this->actingAs($this->admin)
        ->putJson("/api/v1/admin/departments/{$department->id}", [
            'name' => 'Renamed Department',
            'manager_id' => $manager->id,
        ])
        ->assertOk()
        ->assertJsonPath('data.name', 'Renamed Department')
        ->assertJsonPath('data.manager.id', $manager->id);
});

it('soft deletes a department', function (): void {
    $department = Department::factory()->create();

    $this->actingAs($this->admin)
        ->deleteJson("/api/v1/admin/departments/{$department->id}")
        ->assertOk();

    $this->assertSoftDeleted($department);
});

it('blocks employees from managing departments', function (): void {
    $employee = User::factory()->create();
    $employee->assignRole(RoleName::Employee->value);

    $this->actingAs($employee)
        ->getJson('/api/v1/admin/departments')
        ->assertForbidden();
});
