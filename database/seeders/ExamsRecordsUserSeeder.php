<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class ExamsRecordsUserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            [
                'fname' => 'Exams',
                'mname' => null,
                'lname' => 'Records',
                'email' => 'exams.records@tasued.edu.ng',
                'staff_number' => 'EXR001',
            ],
            [
                'fname' => 'Assistant',
                'mname' => null,
                'lname' => 'Records',
                'email' => 'exams.records.asst@tasued.edu.ng',
                'staff_number' => 'EXR002',
            ],
        ];

        foreach ($users as $userData) {
            $user = User::firstOrCreate(
                ['email' => $userData['email']],
                [
                    'fname' => $userData['fname'],
                    'mname' => $userData['mname'],
                    'lname' => $userData['lname'],
                    'staff_number' => strtoupper($userData['staff_number']),
                    'email_verified_at' => now(),
                    'password' => Hash::make('12345'),
                ],
            );

            $user->assignRole('exams&records');
        }
    }
}
