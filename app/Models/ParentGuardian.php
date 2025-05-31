<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ParentGuardian extends Model
{
    use HasFactory;

    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'phone',
        'address',
        'occupation',
        'relationship', // father, mother, guardian
        'status'
    ];

    public function students()
    {
        return $this->hasMany(Student::class);
    }
} 