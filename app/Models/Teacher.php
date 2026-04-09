<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Teacher extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'nip',
        'name',
        'gender',
        'phone',
        'address',
    ];

    /**
     * Relasi ke User
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relasi ke Classrooms (sebagai wali kelas)
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

    /**
     * Relasi ke Teacher Attendances
     */
    public function attendances()
    {
        return $this->hasMany(TeacherAttendance::class);
    }

    /**
     * Relasi ke Teaching Journals
     */
    public function teachingJournals()
    {
        return $this->hasManyThrough(TeachingJournal::class, Schedule::class);
    }

    /**
     * Relasi ke Picket Schedules
     */
    public function picketSchedules()
    {
        return $this->hasMany(PicketSchedule::class);
    }

    /**
     * Relasi ke Picket Attendances
     */
    public function picketAttendances()
    {
        return $this->hasMany(PicketAttendance::class);
    }
}
