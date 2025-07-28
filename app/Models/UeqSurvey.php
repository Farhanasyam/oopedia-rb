<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UeqSurvey extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'nim',
        'class',
        'annoying_enjoyable',
        'not_understandable_understandable',
        'creative_dull',
        'easy_difficult',
        'valuable_inferior',
        'boring_exciting',
        'not_interesting_interesting',
        'unpredictable_predictable',
        'fast_slow',
        'inventive_conventional',
        'obstructive_supportive',
        'good_bad',
        'complicated_easy',
        'unlikable_pleasing',
        'usual_leading_edge',
        'unpleasant_pleasant',
        'secure_not_secure',
        'motivating_demotivating',
        'meets_expectations_does_not_meet',
        'inefficient_efficient',
        'clear_confusing',
        'impractical_practical',
        'organized_cluttered',
        'attractive_unattractive',
        'friendly_unfriendly',
        'conservative_innovative',
        'comments',
        'suggestions'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
} 