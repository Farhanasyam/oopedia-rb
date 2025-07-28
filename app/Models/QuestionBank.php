<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuestionBank extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'material_id',
        'created_by',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function questions()
    {
        return $this->belongsToMany(Question::class, 'question_bank_items')
            ->withTimestamps();
    }

    public function configs()
    {
        return $this->hasMany(QuestionBankConfig::class);
    }

    public function material()
    {
        return $this->belongsTo(Material::class);
    }
}
