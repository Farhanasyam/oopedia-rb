<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuestionBankConfig extends Model
{
    use HasFactory;

    protected $fillable = [
        'question_bank_id',
        'beginner_count',
        'medium_count',
        'hard_count',
        'material_id',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'beginner_count' => 'integer',
        'medium_count' => 'integer',
        'hard_count' => 'integer',
    ];

    public function questionBank()
    {
        return $this->belongsTo(QuestionBank::class);
    }

    public function material()
    {
        return $this->belongsTo(Material::class);
    }
    
    /**
     * Get total question count
     */
    public function getTotalQuestionsAttribute()
    {
        return $this->beginner_count + $this->medium_count + $this->hard_count;
    }
    
    /**
     * Check if configuration has enough questions in bank
     */
    public function hasEnoughQuestions()
    {
        $questionBank = $this->questionBank;
        $material = $this->material;
        
        if (!$questionBank || !$material) {
            return false;
        }
        
        // Count questions by difficulty for this material in the bank
        $questions = $questionBank->questions()->where('material_id', $this->material_id);
        
        $beginnerCount = $questions->where('difficulty', 'beginner')->count();
        $mediumCount = $questions->where('difficulty', 'medium')->count();
        $hardCount = $questions->where('difficulty', 'hard')->count();
        
        // Check if we have enough questions for each difficulty
        return ($beginnerCount >= $this->beginner_count) && 
               ($mediumCount >= $this->medium_count) && 
               ($hardCount >= $this->hard_count);
    }
}
