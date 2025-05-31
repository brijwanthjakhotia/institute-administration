<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClassRoom extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'school_id',
        'grade_level',
        'section',
        'capacity',
        'room_number',
        'status'
    ];

    public function school()
    {
        return $this->belongsTo(School::class);
    }

    public function enrolledStudents()
    {
        return $this->hasMany(Student::class);
    }

    public function classSubjects()
    {
        return $this->hasMany(Subject::class);
    }

    public function assignedTeachers()
    {
        return $this->belongsToMany(Teacher::class, 'class_room_teacher');
    }
} 