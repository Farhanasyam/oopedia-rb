<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        // Create admin users
        $adminList = [
            [
                'name' => 'Ahmad',
                'email' => 'ahmad@dosen.com',
                'password' => 'dosen123'
            ],
            [
                'name' => 'Sarah',
                'email' => 'sarah@dosen.com',
                'password' => 'dosen123'
            ],
            [
                'name' => 'Budi',
                'email' => 'budi@dosen.com',
                'password' => 'dosen123'
            ]
        ];

        foreach ($adminList as $admin) {
            User::firstOrCreate(
                ['email' => $admin['email']],
                [
                    'name' => $admin['name'],
                    'password' => Hash::make($admin['password']),
                    'role_id' => 2,
                    'is_approved' => true
                ]
            );
        }
    }
} 