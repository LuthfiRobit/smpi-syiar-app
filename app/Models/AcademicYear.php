<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AcademicYear extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'semester',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Relasi ke Classrooms
     */
    public function classrooms()
    {
        return $this->hasMany(Classroom::class);
    }

    /**
     * Relasi ke Schedules
     */
    public function schedules()
    {
        return $this->hasMany(Schedule::class);
    }
}
