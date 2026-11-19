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
            ['email' => 'super@campusconnect.edu'],
            [
                'name' => 'Campus Central Administration',
                'roll_no' => null,
                'password' => $password,
                'role' => UserRole::SUPER_ADMIN,
                'department_id' => null,
            ]
        );
        User::updateOrCreate(
            ['email' => 'super@biit.edu.pk'],
            [
                'name' => 'Campus Central Administration',
                'roll_no' => null,
                'password' => $password,
                'role' => UserRole::SUPER_ADMIN,
                'department_id' => null,
            ]
        );

        // 2. Department Admins
        $deptMap = [
            'cafeteria' => ['email' => 'cafeteria@campusconnect.edu', 'alt_email' => 'cafeteria@biit.edu.pk', 'name' => 'Cafeteria Administration'],
            'library' => ['email' => 'library@campusconnect.edu', 'alt_email' => 'library@biit.edu.pk', 'name' => 'Library Administration'],
            'lab-it' => ['email' => 'labit@campusconnect.edu', 'alt_email' => 'labit@biit.edu.pk', 'name' => 'Laboratory & IT Administration'],
            'academic' => ['email' => 'academic@campusconnect.edu', 'alt_email' => 'academic@biit.edu.pk', 'name' => 'Teaching & Academic Affairs'],
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
            User::updateOrCreate(
                ['email' => $data['alt_email']],
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
            ['email' => 'student@campusconnect.edu'],
            [
                'name' => 'Campus Student',
                'roll_no' => 'FA22-BCS-042',
                'password' => $password,
                'role' => UserRole::STUDENT,
                'department_id' => null,
            ]
        );
        User::updateOrCreate(
            ['email' => 'student@biit.edu.pk'],
            [
                'name' => 'Campus Student',
                'roll_no' => 'BIIT-FA22-BCS-042',
                'password' => $password,
                'role' => UserRole::STUDENT,
                'department_id' => null,
            ]
        );

        // Additional demo students
        $extraStudents = [
            ['name' => 'Ayesha Khan', 'roll_no' => 'FA22-BCS-015', 'email' => 'ayesha@campusconnect.edu'],
            ['name' => 'Ali Raza', 'roll_no' => 'SP23-BCS-088', 'email' => 'ali.raza@campusconnect.edu'],
            ['name' => 'Zainab Fatima', 'roll_no' => 'FA23-SE-003', 'email' => 'zainab@campusconnect.edu'],
            ['name' => 'Usman Tariq', 'roll_no' => 'SP24-SE-041', 'email' => 'usman@campusconnect.edu'],
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
