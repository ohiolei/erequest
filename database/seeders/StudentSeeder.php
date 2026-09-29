<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use RuntimeException;

class StudentSeeder extends Seeder
{
    public function run(): void
    {
      
        $student = User::firstOrCreate(
            ['email' => "imole@gmail.com"],
            [
                'name' => 'imole',
                'matric_no' => strtoupper('2022130240'),
                'email_verified_at' => now(),
                'password' => Hash::make('12345'),
            ],
        );

      

        $student->assignRole('student');
    }
}
