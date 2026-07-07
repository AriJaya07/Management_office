<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            'users.view', 'users.manage',
            'departments.view', 'departments.manage',
            'attendance.view', 'attendance.manage',
            'reports.view',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        Role::firstOrCreate(['name' => 'admin']) 
            ->givePermissionTo(Permission::all());

        Role::firstOrCreate(['name' => 'manager'])
            ->givePermissionTo(['users.view', 'departments.view', 'attendance.view', 'attendance.manage', 'reports.view']);

        Role::firstOrCreate(['name' => 'employee'])
            ->givePermissionTo(['attendance.view']);
    }
}
