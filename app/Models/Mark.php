<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mark extends Model
{
    use HasFactory;

    protected $fillable = ['student_id', 'subject_id', 'teacher_id', 'mark'];
    public function student()
{
    return $this->belongsTo(User::class);
}

public function subject()
{
    return $this->belongsTo(Subject::class);
}

public function teacher()
{
    return $this->belongsTo(User::class);
}


}
