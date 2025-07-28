<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStudentDetailsToUeqSurveys extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('ueq_surveys', function (Blueprint $table) {
            $table->string('nim')->after('user_id')->nullable();
            $table->string('class')->after('nim')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('ueq_surveys', function (Blueprint $table) {
            $table->dropColumn(['nim', 'class']);
        });
    }
}