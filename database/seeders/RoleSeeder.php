<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        // Create or ensure roles exist with new structure
        Role::firstOrCreate(
            ['id' => 1],
            ['role_name' => 'Superadmin']
        );
        
        Role::firstOrCreate(
            ['id' => 2],
            ['role_name' => 'Admin']
        );
        
        Role::firstOrCreate(
            ['id' => 3],
            ['role_name' => 'Mahasiswa']
        );
        
        Role::firstOrCreate(
            ['id' => 4],
            ['role_name' => 'Tamu']
        );
    }
} 