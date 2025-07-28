<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    use HasFactory;

    const TYPE_FILL_IN_THE_BLANK = 'fill_in_the_blank';
    
    protected $fillable = [
        'material_id',
        'question_text',
        'question_type',
        'difficulty',
        'created_by'
    ];

    public function material()
    {
        return $this->belongsTo(Material::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function answers()
    {
        return $this->hasMany(Answer::class);
    }

    public function questions()
    {
        return $this->hasMany(Question::class);
    }

    public function progress()
    {
        return $this->hasMany(Progress::class);
    }

    public function questionBanks()
    {
        return $this->belongsToMany(QuestionBank::class, 'question_bank_items')
            ->withTimestamps();
    }
}