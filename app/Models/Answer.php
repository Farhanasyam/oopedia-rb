<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Answer extends Model
{
    use HasFactory;

    protected $fillable = [
        'question_id',
        'is_correct',
        'explanation',
        'answer_text',
        'drag_source',
        'drag_target',
        'blank_position'
    ];

    public function question()
    {
        return $this->belongsTo(Question::class);
    }
}