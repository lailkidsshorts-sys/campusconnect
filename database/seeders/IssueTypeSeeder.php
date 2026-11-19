<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\IssueType;
use Illuminate\Database\Seeder;

class IssueTypeSeeder extends Seeder
{
    public function run(): void
    {
        $map = [
            'cafeteria-services' => [
                'Poor food quality or hygiene',
                'High food prices',
                'Limited food variety',
                'Unprofessional staff behaviour or poor service',
            ],
            'library-services' => [
                'Insufficient or outdated books and learning resources',
                'Librarian availability or staff behaviour',
                'Library opening and closing hours',
                'Insufficient seating or study space',
            ],
            'lab-it-facilities' => [
                'Non-functional computers',
                'Internet connectivity issues',
                'Projector or multimedia equipment problems',
                'Missing or unavailable software',
            ],
            'teaching-academic-affairs' => [
                'Teachers arriving late to lectures',
                'Unavailability of lecture notes or learning materials',
                'Inadequate explanation of course topics',
                'Classroom management or discipline issues',
            ],
        ];

        foreach ($map as $catSlug => $issues) {
            $cat = Category::where('slug', $catSlug)->firstOrFail();
            foreach ($issues as $issue) {
                IssueType::firstOrCreate([
                    'category_id' => $cat->id,
                    'name' => $issue,
                ]);
            }
        }
    }
}
