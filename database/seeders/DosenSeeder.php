<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;

class DosenSeeder extends Seeder
{
    public function run(): void
    {
        // Create or ensure Dosen role exists
        $roleDosen = Role::firstOrCreate(
            ['id' => 1],
            ['role_name' => 'Dosen']
        );

        // Create dosen users
        $dosenList = [
            [
                'name' => 'Dr. Ahmad',
                'email' => 'ahmad@dosen.com',
                'password' => 'dosen123'
            ],
            [
                'name' => 'Prof. Sarah',
                'email' => 'sarah@dosen.com',
                'password' => 'dosen123'
            ],
            [
                'name' => 'Dr. Budi',
                'email' => 'budi@dosen.com',
                'password' => 'dosen123'
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
    }
} 