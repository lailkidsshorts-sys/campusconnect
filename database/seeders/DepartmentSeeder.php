<?php

namespace Database\Seeders;

use App\Models\Department;
use Illuminate\Database\Seeder;

class DepartmentSeeder extends Seeder
{
    public function run(): void
    {
        $departments = [
            [
                'name' => 'Cafeteria Administration',
                'slug' => 'cafeteria',
                'admin_title' => 'Cafeteria Administrator',
            ],
            [
                'name' => 'Library Administration',
                'slug' => 'library',
                'admin_title' => 'Library Administrator',
            ],
            [
                'name' => 'Laboratory / IT Administration',
                'slug' => 'lab-it',
                'admin_title' => 'Laboratory / IT Administrator',
            ],
            [
                'name' => 'Teaching Manager / Academic Coordination',
                'slug' => 'academic',
                'admin_title' => 'Academic Coordinator',
            ],
        ];

        foreach ($departments as $dept) {
            Department::updateOrCreate(['slug' => $dept['slug']], $dept);
        }
    }
}
