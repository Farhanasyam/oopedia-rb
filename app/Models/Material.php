<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Material extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'content', 'created_by'];

    public function questions()
    {
        return $this->hasMany(Question::class);
    }

    public function media()
    {
        return $this->hasMany(Media::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function progress()
    {
        return $this->hasOne(Progress::class);
    }

    public function inProgress()
    {
        $materials = Material::all();
        return view('mahasiswa.dashboard.in-progress', compact('materials'));
    }

    public function complete()
    {
        $materials = Material::all();
        return view('mahasiswa.dashboard.completed', compact('materials'));
    }
    
    public function questionBankConfigs()
    {
        return $this->hasMany(QuestionBankConfig::class);
    }

    public function questionBanks()
    {
        return $this->hasMany(QuestionBank::class);
    }
}