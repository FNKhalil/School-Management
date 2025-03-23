<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Grade extends Model
{
    use HasFactory;
    protected $fillable = ['name'];

    public function students()
{
    return $this->hasMany(User::class)->whereHas('roles', fn ($q) => $q->where('name', 'student'));
}


public function teachers()
{
    return $this->belongsToMany(User::class, 'grade_teacher', 'grade_id', 'user_id')
        ->whereHas('roles', fn ($q) => $q->where('name', 'teacher'));
}

}
