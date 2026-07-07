<?php

namespace Database\Seeders;

use App\Enums\RoleName;
use App\Models\Department;
use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Department::with('positions')->get()->each(function (Department $department): void {
            $manager = User::factory()
                ->for($department)
                ->create(['position_id' => $department->positions->first()?->id]);
            $manager->assignRole(RoleName::Manager->value);

            $department->update(['manager_id' => $manager->id]);

            User::factory()
                ->count(4)
                ->for($department)
                ->create(['position_id' => $department->positions->random()->id])
                ->each(fn (User $user) => $user->assignRole(RoleName::Employee->value));
        });
    }
}
