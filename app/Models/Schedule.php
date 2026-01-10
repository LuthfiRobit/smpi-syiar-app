<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'academic_year_id',
        'classroom_id',
        'subject_id',
        'teacher_id',
        'time_slot_id',
        'day',
    ];

    /**
     * Relasi ke Academic Year
     */
    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class);
    }

    /**
     * Relasi ke Classroom
     */
    public function classroom()
    {
        return $this->belongsTo(Classroom::class);
    }

    /**
     * Relasi ke Subject
     */
    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    /**
     * Relasi ke Teacher
     */
    public function teacher()
    {
        return $this->belongsTo(Teacher::class);
    }

    /**
     * Relasi ke Time Slot
     */
    public function timeSlot()
    {
        return $this->belongsTo(TimeSlot::class);
    }
}
