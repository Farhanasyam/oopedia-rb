<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = ['name', 'email', 'password', 'role_id'];

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function progress()
    {
        return $this->hasMany(Progress::class);
    }

    public function answers()
    {
        return $this->hasMany(Answer::class);
    }

    public function materials()
    {
        return $this->belongsToMany(Material::class, 'answers', 'user_id', 'material_id')
            ->distinct();
    }

    public function hasRole($role)
    {
        return $this->role_id == $role;
    }
}