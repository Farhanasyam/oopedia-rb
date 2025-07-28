<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class UpdateRolesStructure extends Migration
{
    public function up()
    {
        // 1. Update role_name untuk ID 1 menjadi Superadmin terlebih dahulu
        DB::table('roles')->where('id', 1)->update(['role_name' => 'Superadmin']);
        
        // 2. Tambahkan atau update role Admin dengan ID 2
        $roleExists = DB::table('roles')->where('id', 2)->exists();
        if (!$roleExists) {
            DB::table('roles')->insert([
                'id' => 2,
                'role_name' => 'Admin'
            ]);
        } else {
            DB::table('roles')->where('id', 2)->update(['role_name' => 'Admin']);
        }
        
        // 3. Update role Mahasiswa dari ID 2 menjadi ID 3
        DB::statement('UPDATE users SET role_id = 3 WHERE role_id = 2');
        
        // 4. Tambahkan atau update role Mahasiswa dengan ID 3
        $role3Exists = DB::table('roles')->where('id', 3)->exists();
        if (!$role3Exists) {
            DB::table('roles')->insert([
                'id' => 3,
                'role_name' => 'Mahasiswa'
            ]);
        } else {
            DB::table('roles')->where('id', 3)->update(['role_name' => 'Mahasiswa']);
        }
    }

    public function down()
    {
        // Hapus constraint unik sementara jika ada
        Schema::table('roles', function (Blueprint $table) {
            $table->dropUnique(['role_name']);
        });
        
        // Rollback changes
        DB::statement('UPDATE users SET role_id = 2 WHERE role_id = 3');
        DB::statement('UPDATE roles SET role_name = "Admin" WHERE id = 1');
        DB::statement('UPDATE roles SET role_name = "Mahasiswa" WHERE id = 2'); // Perhatikan perubahan ini
        DB::statement('DELETE FROM roles WHERE id = 3');
        
        // Kembalikan constraint unik
        Schema::table('roles', function (Blueprint $table) {
            $table->unique('role_name');
        });
    }
}