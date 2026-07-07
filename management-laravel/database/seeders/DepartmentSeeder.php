<?php

namespace Database\Seeders;

use App\Models\Department;
use Illuminate\Database\Seeder;

class DepartmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $departments = [
            ['name' => 'Engineering', 'code' => 'ENG', 'positions' => ['Software Engineer', 'QA Engineer', 'DevOps Engineer']],
            ['name' => 'Human Resources', 'code' => 'HR', 'positions' => ['HR Generalist', 'Recruiter']],
            ['name' => 'Finance', 'code' => 'FIN', 'positions' => ['Accountant', 'Financial Analyst']],
            ['name' => 'Marketing', 'code' => 'MKT', 'positions' => ['Marketing Specialist', 'Content Writer']],
        ];

        foreach ($departments as $data) {
            $department = Department::firstOrCreate(
                ['code' => $data['code']],
                ['name' => $data['name']]
            );

            foreach ($data['positions'] as $title) {
                $department->positions()->firstOrCreate(['title' => $title]);
            }
        }
    }
}
