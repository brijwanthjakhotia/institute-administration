<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'description',
        'class_room_id',
        'teacher_id',
        'credits',
        'status'
    ];

    public function assignedClassRoom()
    {
        return $this->belongsTo(ClassRoom::class);
    }

    public function assignedTeacher()
    {
        return $this->belongsTo(Teacher::class);
    }

    public function studentGrades()
    {
        return $this->hasMany(Grade::class);
    }
} 