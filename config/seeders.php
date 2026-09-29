<?php

return [
    'admin' => [
        'name' => env('ADMIN_NAME', 'Portal Administrator'),
        'email' => env('ADMIN_EMAIL', 'admin@example.com'),
        'matric_no' => env('ADMIN_MATRIC_NO', 'ADMIN-0001'),
        'password' => env('ADMIN_PASSWORD'),
    ],
    'student' => [
        'name' => env('STUDENT_NAME', 'Portal Student'),
        'email' => env('STUDENT_EMAIL', 'student@example.com'),
        'matric_no' => env('STUDENT_MATRIC_NO', 'STUDENT-0001'),
        'password' => env('STUDENT_PASSWORD'),
    ],
];
