<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'gavino.tan@deped.gov.ph'],
            [
                'fullname' => 'Gavino Tan',
                'firstname' => 'Gavino',
                'lastname' => 'Tan',
                'extname' => null,
                'password' => bcrypt('password'),
                'job_title' => 'ICT Administrator',
                'role' => 'admin',
            ]
        );

        User::create([
            'fullname' => 'Juan Dela Cruz',
            'firstname' => 'Juan',
            'lastname' => 'Dela Cruz',
            'extname' => null,
            'email' => 'juan.delacruz@deped.gov.ph',
            'password' => bcrypt('password'),
            'job_title' => 'Teacher I',
            'role' => 'employee',
        ]);

        User::create([
            'fullname' => 'Maria Santos Jr.',
            'firstname' => 'Maria',
            'lastname' => 'Santos',
            'extname' => 'Jr.',
            'email' => 'maria.santos@deped.gov.ph',
            'password' => bcrypt('password'),
            'job_title' => 'Administrative Aide IV',
            'role' => 'employee',
        ]);
    }
}
