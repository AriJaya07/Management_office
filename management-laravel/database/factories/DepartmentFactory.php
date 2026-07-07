<?php

namespace Database\Factories;

use App\Models\Department;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Department>
 */
class DepartmentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->unique()->randomElement([
            'Engineering', 'Human Resources', 'Finance', 'Marketing',
            'Operations', 'Sales', 'Legal', 'Customer Support',
        ]);

        return [
            'name' => $name,
            'code' => Str::upper(Str::substr(Str::slug($name, ''), 0, 3)).fake()->unique()->numberBetween(10, 99),
            'manager_id' => null,
        ];
    }
}
