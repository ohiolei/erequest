<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class StudentSeeder extends Seeder
{
    public function run(): void
    {
        $students = [
            ['name' => 'imole', 'email' => 'imole@gmail.com', 'matric_no' => '2022130240'],
            ['name' => 'Amina Yusuf', 'email' => 'amina.yusuf@example.com', 'matric_no' => 'TASFUED/2022/130241'],
            ['name' => 'Daniel Okafor', 'email' => 'daniel.okafor@example.com', 'matric_no' => 'TASFUED/2022/130242'],
            ['name' => 'Grace Adeyemi', 'email' => 'grace.adeyemi@example.com', 'matric_no' => 'TASFUED/2022/130243'],
            ['name' => 'Samuel Bello', 'email' => 'samuel.bello@example.com', 'matric_no' => 'TASFUED/2022/130244'],
            ['name' => 'Zainab Ibrahim', 'email' => 'zainab.ibrahim@example.com', 'matric_no' => 'TASFUED/2022/130245'],
        ];

        foreach ($students as $studentData) {
            $student = User::firstOrCreate(
                ['email' => $studentData['email']],
                [
                    'name' => $studentData['name'],
                    'matric_no' => strtoupper($studentData['matric_no']),
                    'email_verified_at' => now(),
                    'password' => Hash::make('12345'),
                ],
            );

            $student->assignRole('student');
        }
    }
}
