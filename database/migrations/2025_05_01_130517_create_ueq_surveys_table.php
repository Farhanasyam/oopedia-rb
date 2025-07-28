<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('ueq_surveys', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            // UEQ Scale items (1-7 rating)
            $table->integer('annoying_enjoyable');
            $table->integer('not_understandable_understandable');
            $table->integer('creative_dull');
            $table->integer('easy_difficult');
            $table->integer('valuable_inferior');
            $table->integer('boring_exciting');
            $table->integer('not_interesting_interesting');
            $table->integer('unpredictable_predictable');
            $table->integer('fast_slow');
            $table->integer('inventive_conventional');
            $table->integer('obstructive_supportive');
            $table->integer('good_bad');
            $table->integer('complicated_easy');
            $table->integer('unlikable_pleasing');
            $table->integer('usual_leading_edge');
            $table->integer('unpleasant_pleasant');
            $table->integer('secure_not_secure');
            $table->integer('motivating_demotivating');
            $table->integer('meets_expectations_does_not_meet');
            $table->integer('inefficient_efficient');
            $table->integer('clear_confusing');
            $table->integer('impractical_practical');
            $table->integer('organized_cluttered');
            $table->integer('attractive_unattractive');
            $table->integer('friendly_unfriendly');
            $table->integer('conservative_innovative');
            $table->text('comments')->nullable();
            $table->text('suggestions')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('ueq_surveys');
    }
};