<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('progress', function (Blueprint $table) {
            $table->integer('attempt_number')->default(1)->after('is_correct');
        });
        
        // Update attempt_number untuk data yang sudah ada
        // Mengelompokkan berdasarkan user_id dan question_id, lalu update attempt_number
        $progressRecords = DB::table('progress')
            ->select('user_id', 'question_id', DB::raw('MAX(id) as id'))
            ->whereNotNull('user_id')
            ->groupBy('user_id', 'question_id')
            ->get();
            
        foreach ($progressRecords as $record) {
            // Dapatkan semua entri untuk user dan soal ini, urutkan berdasarkan waktu
            $attempts = DB::table('progress')
                ->select('id')
                ->where('user_id', $record->user_id)
                ->where('question_id', $record->question_id)
                ->orderBy('created_at')
                ->get();
                
            $attemptNumber = 1;
            foreach ($attempts as $attempt) {
                DB::table('progress')
                    ->where('id', $attempt->id)
                    ->update(['attempt_number' => $attemptNumber]);
                $attemptNumber++;
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('progress', function (Blueprint $table) {
            $table->dropColumn('attempt_number');
        });
    }
};
