<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class StudentUserSeeder extends Seeder
{
    public function run(): void
    {
        $students = [
            [
                'fname' => 'imole',
                'mname' => null,
                'lname' => null,
                'email' => 'imole@gmail.com',
                'matric_no' => '2022130240',
            ],
            [
                'fname' => 'Amina',
                'mname' => null,
                'lname' => 'Yusuf',
                'email' => 'amina.yusuf@example.com',
                'matric_no' => 'TASFUED/2022/130241',
            ],
            [
                'fname' => 'Daniel',
                'mname' => null,
                'lname' => 'Okafor',
                'email' => 'daniel.okafor@example.com',
                'matric_no' => 'TASFUED/2022/130242',
            ],
            [
                'fname' => 'Grace',
                'mname' => null,
                'lname' => 'Adeyemi',
                'email' => 'grace.adeyemi@example.com',
                'matric_no' => 'TASFUED/2022/130243',
            ],
            [
                'fname' => 'Samuel',
                'mname' => null,
                'lname' => 'Bello',
                'email' => 'samuel.bello@example.com',
                'matric_no' => 'TASFUED/2022/130244',
            ],
            [
                'fname' => 'Zainab',
                'mname' => null,
                'lname' => 'Ibrahim',
                'email' => 'zainab.ibrahim@example.com',
                'matric_no' => 'TASFUED/2022/130245',
            ],
        ];

        foreach ($students as $studentData) {
            $student = User::firstOrCreate(
                ['email' => $studentData['email']],
                [
                    'fname' => $studentData['fname'],
                    'mname' => $studentData['mname'],
                    'lname' => $studentData['lname'],
                    'matric_no' => strtoupper($studentData['matric_no']),
                    'email_verified_at' => now(),
                    'password' => Hash::make('12345'),
                ],
            );

            $student->assignRole('student');
        }
    }
}
