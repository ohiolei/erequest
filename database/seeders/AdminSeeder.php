<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use RuntimeException;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::firstOrCreate(
            ['email' => "ttihub@taused.edu.ng"],
            [
                'name' => 'Admin',
                'staff_number' => strtoupper('pss2022'),
                'email_verified_at' => now(),
                'password' => Hash::make('12345'),
            ],
        );

        $admin->assignRole('admin');

        $sheggz = User::firstOrCreate(
            ['email' => "akinwandeov@tasued.edu.ng"],
            [
                'name' => 'Akinwande Olusegun',
                'staff_number' => strtoupper('pss2724'),
                'email_verified_at' => now(),
                'password' => Hash::make('12345'),
            ],
        );

        $sheggz->assignRole('admin');

        $wills = User::firstOrCreate(
            ['email' => "williamsoo@tasued.edu.ng"],
            [
                'name' => 'Williams Oluwatobiloba',
                'staff_number' => strtoupper('pss2481'),
                'email_verified_at' => now(),
                'password' => Hash::make('12345'),
            ],
        );

        $wills->assignRole('admin');
    }
}
