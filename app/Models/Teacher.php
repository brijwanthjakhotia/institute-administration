<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Teacher extends Model
{
    use HasFactory;

    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'phone',
        'date_of_birth',
        'gender',
        'address',
        'school_id',
        'qualification',
        'experience',
        'subject_specialization',
        'joining_date',
        'status'
    ];

    public function school()
    {
        return $this->belongsTo(School::class);
    }

    public function assignedClasses()
    {
        return $this->belongsToMany(ClassRoom::class, 'class_room_teacher');
    }

    public function taughtSubjects()
    {
        return $this->hasMany(Subject::class);
    }
} 