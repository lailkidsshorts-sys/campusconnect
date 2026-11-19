<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\Department;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $password = Hash::make('Password@123');

        // 1. Super Administrator
        User::updateOrCreate(
            ['email' => 'super@biit.edu.pk'],
            [
                'name' => 'BIIT Central Administration',
                'roll_no' => null,
                'password' => $password,
                'role' => UserRole::SUPER_ADMIN,
                'department_id' => null,
            ]
        );

        // 2. Department Admins
        $deptMap = [
            'cafeteria' => ['email' => 'cafeteria@biit.edu.pk', 'name' => 'Cafeteria Administrator'],
            'library' => ['email' => 'library@biit.edu.pk', 'name' => 'Library Administrator'],
            'lab-it' => ['email' => 'labit@biit.edu.pk', 'name' => 'Lab & IT Administrator'],
            'academic' => ['email' => 'academic@biit.edu.pk', 'name' => 'Academic Coordinator'],
        ];

        foreach ($deptMap as $slug => $data) {
            $dept = Department::where('slug', $slug)->firstOrFail();
            User::updateOrCreate(
                ['email' => $data['email']],
                [
                    'name' => $data['name'],
                    'roll_no' => null,
                    'password' => $password,
                    'role' => UserRole::DEPT_ADMIN,
                    'department_id' => $dept->id,
                ]
            );
        }

        // 3. Demo Student
        User::updateOrCreate(
            ['email' => 'student@biit.edu.pk'],
            [
                'name' => 'Muhammad Hamza',
                'roll_no' => 'BIIT-FA22-BCS-042',
                'password' => $password,
                'role' => UserRole::STUDENT,
                'department_id' => null,
            ]
        );

        // Additional demo students
        $extraStudents = [
            ['name' => 'Ayesha Khan', 'roll_no' => 'BIIT-FA22-BCS-015', 'email' => 'ayesha@biit.edu.pk'],
            ['name' => 'Ali Raza', 'roll_no' => 'BIIT-SP23-BCS-088', 'email' => 'ali.raza@biit.edu.pk'],
            ['name' => 'Zainab Fatima', 'roll_no' => 'BIIT-FA23-SE-003', 'email' => 'zainab@biit.edu.pk'],
            ['name' => 'Usman Tariq', 'roll_no' => 'BIIT-SP24-SE-041', 'email' => 'usman@biit.edu.pk'],
        ];

        foreach ($extraStudents as $student) {
            User::updateOrCreate(
                ['email' => $student['email']],
                [
                    'name' => $student['name'],
                    'roll_no' => $student['roll_no'],
                    'password' => $password,
                    'role' => UserRole::STUDENT,
                    'department_id' => null,
                ]
            );
        }
    }
}
