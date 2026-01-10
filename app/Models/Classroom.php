<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Classroom extends Model
{
    use HasFactory;

    protected $fillable = [
        'academic_year_id',
        'teacher_id',
        'name',
        'grade_level',
    ];

    /**
     * Relasi ke Academic Year
     */
    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class);
    }

    /**
     * Relasi ke Teacher (Wali Kelas)
     */
    public function teacher()
    {
        return $this->belongsTo(Teacher::class);
    }

    /**
     * Relasi ke Students
     */
    public function students()
    {
        return $this->hasMany(Student::class);
    }

    /**
     * Relasi ke Schedules
     */
    public function schedules()
    {
        return $this->hasMany(Schedule::class);
    }
}
