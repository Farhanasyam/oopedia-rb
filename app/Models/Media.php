<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Media extends Model
{
    use HasFactory;

    protected $fillable = ['material_id', 'media_type', 'media_url', 'media_description'];

    public function material()
    {
        return $this->belongsTo(Material::class);
    }

    public function getFullUrlAttribute()
    {
        if (str_starts_with($this->media_url, 'http://') || 
            str_starts_with($this->media_url, 'https://')) {
            return $this->media_url;
        }
        
        $url = str_replace('storage/', '', $this->media_url);
        
        return asset('storage/' . $url);
    }
}