<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class MahasiswaSeeder extends Seeder
{
    public function run(): void
    {
        // Create mahasiswa users
        $mahasiswaList = [
            [
                'name' => 'Andi Pratama',
                'email' => 'andi@mahasiswa.com',
                'password' => 'mhs123'
            ],
            [
                'name' => 'Budi Santoso',
                'email' => 'budi@mahasiswa.com',
                'password' => 'mhs123'
            ],
            [
                'name' => 'Citra Dewi',
                'email' => 'citra@mahasiswa.com',
                'password' => 'mhs123'
            ],
            [
                'name' => 'Deni Wijaya',
                'email' => 'deni@mahasiswa.com',
                'password' => 'mhs123'
            ],
            [
                'name' => 'Eva Putri',
                'email' => 'eva@mahasiswa.com',
                'password' => 'mhs123'
            ]
        ];

        foreach ($mahasiswaList as $mahasiswa) {
            User::firstOrCreate(
                ['email' => $mahasiswa['email']],
                [
                    'name' => $mahasiswa['name'],
                    'password' => Hash::make($mahasiswa['password']),
                    'role_id' => 3,
                    'is_approved' => true
                ]
            );
        }
    }
} 