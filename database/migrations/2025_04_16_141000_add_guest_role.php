<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class AddGuestRole extends Migration
{
    public function up()
    {
        // Add guest role with ID 4
        DB::table('roles')->insert([
            'id' => 4,
            'role_name' => 'Tamu',
            'created_at' => now(),
            'updated_at' => now()
        ]);
    }

    public function down()
    {
        DB::table('roles')->where('id', 4)->delete();
    }
} 