<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Create or ensure roles exist
        $roleDosen = Role::firstOrCreate(
            ['id' => 1],
            ['role_name' => 'Dosen']
        );
        
        $roleMahasiswa = Role::firstOrCreate(
            ['id' => 2],
            ['role_name' => 'Mahasiswa']
        );

        // Create dosen users
        $dosenList = [
            [
                'name' => 'Dosen PBO',
                'email' => 'dosen.pbo@lecturer.com',
                'password' => 'password123'
            ],
            [
                'name' => 'Dosen Praktikum',
                'email' => 'dosen.praktikum@lecturer.com',
                'password' => 'password123'
            ]
        ];

        foreach ($dosenList as $dosen) {
            User::create([
                'name' => $dosen['name'],
                'email' => $dosen['email'],
                'password' => Hash::make($dosen['password']),
                'role_id' => $roleDosen->id,
            ]);
        }

        // Create mahasiswa users
        $mahasiswaList = [
            [
                'name' => 'Mahasiswa Satu',
                'email' => 'mahasiswa.1@student.com',
                'password' => 'password123'
            ],
            [
                'name' => 'Mahasiswa Dua',
                'email' => 'mahasiswa.2@student.com',
                'password' => 'password123'
            ],
            [
                'name' => 'Mahasiswa Tiga',
                'email' => 'mahasiswa.3@student.com',
                'password' => 'password123'
            ],
            [
                'name' => 'Mahasiswa Empat',
                'email' => 'mahasiswa.4@student.com',
                'password' => 'password123'
            ],
            [
                'name' => 'Mahasiswa Lima',
                'email' => 'mahasiswa.5@student.com',
                'password' => 'password123'
            ]
        ];

        foreach ($mahasiswaList as $mahasiswa) {
            User::create([
                'name' => $mahasiswa['name'],
                'email' => $mahasiswa['email'],
                'password' => Hash::make($mahasiswa['password']),
                'role_id' => $roleMahasiswa->id,
            ]);
        }
    }
}