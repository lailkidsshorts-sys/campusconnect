<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Department;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $cafeteria = Department::where('slug', 'cafeteria')->firstOrFail();
        $library = Department::where('slug', 'library')->firstOrFail();
        $labIt = Department::where('slug', 'lab-it')->firstOrFail();
        $academic = Department::where('slug', 'academic')->firstOrFail();

        $categories = [
            [
                'department_id' => $cafeteria->id,
                'name' => 'Cafeteria Services',
                'slug' => 'cafeteria-services',
            ],
            [
                'department_id' => $library->id,
                'name' => 'Library Facilities & Services',
                'slug' => 'library-services',
            ],
            [
                'department_id' => $labIt->id,
                'name' => 'Laboratories & IT Facilities',
                'slug' => 'lab-it-facilities',
            ],
            [
                'department_id' => $academic->id,
                'name' => 'Teaching & Academic Affairs',
                'slug' => 'teaching-academic-affairs',
            ],
        ];

        foreach ($categories as $cat) {
            Category::updateOrCreate(['slug' => $cat['slug']], $cat);
        }
    }
}
