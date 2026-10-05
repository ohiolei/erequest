<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class BursaryUserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            [
                'fname' => 'Bursar',
                'mname' => null,
                'lname' => 'Bursary',
                'email' => 'bursary@tasued.edu.ng',
                'staff_number' => 'BUR001',
            ],
            [
                'fname' => 'Assistant',
                'mname' => null,
                'lname' => 'Bursary',
                'email' => 'bursary.asst@tasued.edu.ng',
                'staff_number' => 'BUR002',
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

            $user->assignRole('busary');
        }
    }
}
